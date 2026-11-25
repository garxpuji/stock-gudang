<?php

namespace Tests\Feature;

use App\Actions\InboundStockAction;
use App\Actions\RecordDamagedGoodsAction;
use App\Enums\DamageCategory;
use App\Enums\RecipientType;
use App\Models\Category;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    protected User $superadmin;

    protected User $adminGudang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--database' => 'mariadb']);
        $this->seed([
            UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            LocationSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
        ]);

        $this->superadmin = User::where('email', 'tegar@gayuhmedia.id')->firstOrFail();
        $this->adminGudang = User::where('email', 'admin@gayuhmedia.id')->firstOrFail();
    }

    public function test_user_seeder_does_not_overwrite_existing_user_passwords(): void
    {
        $this->superadmin->update([
            'password' => Hash::make('CustomSuperPass123'),
        ]);

        $this->seed(UserSeeder::class);

        $refreshed = $this->superadmin->fresh();
        $this->assertTrue(Hash::check('CustomSuperPass123', $refreshed->password));
        $this->assertFalse(Hash::check('password', $refreshed->password));
    }

    public function test_transaction_controller_hides_internal_sql_errors_but_exposes_domain_exceptions(): void
    {
        $product = Product::where('sku', 'CBL-DC-1C-MTR')->firstOrFail();

        // 1. Domain exception tetap ditampilkan secara informatif ke pengguna
        $domainResponse = $this->actingAs($this->adminGudang)->post(route('stock-out.store'), [
            'recipient_type' => RecipientType::TEKNISI->value,
            'picked_up_by' => 'Teknisi Uji',
            'recipient_department_or_team' => 'Tim Fiber',
            'purpose' => 'Uji Batas Stok',
            'transaction_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 99999,
                ],
            ],
        ]);

        $domainResponse->assertSessionHas('error');
        $this->assertStringContainsString('Stok', (string) session('error'));

        // 2. System/SQL exception dicatat di log dan diganti pesan generik
        $this->mock(InboundStockAction::class, function ($mock) {
            $mock->shouldReceive('execute')
                ->once()
                ->andThrow(new \RuntimeException('SQLSTATE[42S02]: Base table or view not found: stock_in_transactions'));
        });

        $systemResponse = $this->actingAs($this->adminGudang)->post(route('stock-in.store'), [
            'transaction_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 5,
                ],
            ],
        ]);

        $systemResponse->assertSessionHas('error');
        $errorMsg = (string) session('error');
        $this->assertStringNotContainsString('SQLSTATE', $errorMsg);
        $this->assertStringNotContainsString('stock_in_transactions', $errorMsg);
        $this->assertStringContainsString('Terjadi kesalahan sistem', $errorMsg);
    }

    public function test_inactive_user_cannot_access_dashboard_and_sessions_are_revoked_on_deactivation(): void
    {
        // 1. User yang dinonaktifkan otomatis logout saat mengakses dashboard
        $staff = User::create([
            'name' => 'Staff Nonaktif',
            'email' => 'nonaktif.dash@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => Hash::make('Password1234'),
            'is_active' => false,
        ]);

        $dashResponse = $this->actingAs($staff)->get(route('dashboard'));
        $dashResponse->assertRedirect(route('login'));
        $this->assertGuest();

        // 2. Menonaktifkan user via UserController menghapus baris sesi & memutar remember_token
        $target = User::where('email', 'gudang@gayuhmedia.id')->firstOrFail();
        $target->forceFill(['remember_token' => 'old-remember-token'])->save();

        DB::table('sessions')->insert([
            'id' => 'test-active-session-id-123',
            'user_id' => $target->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'e30=',
            'last_activity' => time(),
        ]);

        $updateResponse = $this->actingAs($this->superadmin)->put(route('users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'role' => $target->role,
            'is_active' => '0',
            'current_password' => 'password',
        ]);

        $updateResponse->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('sessions', ['user_id' => $target->id]);
        $this->assertNotEquals('old-remember-token', $target->fresh()->remember_token);
    }

    public function test_master_data_redirect_to_blocks_open_redirects_and_allows_whitelisted_routes(): void
    {
        $maliciousTargets = [
            'https://localhost.evil.com',
            'https://localhost@evil.com',
            '//evil.com/phishing',
            '/\\evil.com',
        ];

        foreach ($maliciousTargets as $idx => $target) {
            $resp = $this->actingAs($this->adminGudang)->post(route('categories.store'), [
                'name' => 'Kategori Test '.$idx,
                'code' => 'CAT-SEC-'.$idx,
                'redirect_to' => $target,
            ]);

            $resp->assertRedirect(route('categories.index'));
        }

        $safeResp = $this->actingAs($this->adminGudang)->post(route('categories.store'), [
            'name' => 'Kategori Whitelist',
            'code' => 'CAT-SAFE-1',
            'redirect_to' => route('products.create', [], false),
        ]);

        $safeResp->assertRedirect(route('products.create', [], false));
    }

    public function test_damaged_photo_proof_uses_private_storage_rejects_svg_and_cleans_up_orphans(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $product = Product::where('sku', 'ONT-ZTE-F609')->firstOrFail();

        // 1. Tolak file SVG
        $svgFile = UploadedFile::fake()->create('xss.svg', 5, 'image/svg+xml');
        $svgResp = $this->actingAs($this->adminGudang)->post(route('damaged-goods.store'), [
            'product_id' => $product->id,
            'qty' => 1,
            'reported_by' => 'Teknisi A',
            'reporter_type' => RecipientType::TEKNISI->value,
            'damage_category' => DamageCategory::PHYSICAL_DEFECT->value,
            'damage_description' => 'Uji tolak SVG',
            'photo_proof' => $svgFile,
        ]);
        $svgResp->assertSessionHasErrors('photo_proof');

        // 2. Simpan foto valid ke disk privat 'local'
        $pngFile = UploadedFile::fake()->create('proof.png', 100, 'image/png');
        $okResp = $this->actingAs($this->adminGudang)->post(route('damaged-goods.store'), [
            'product_id' => $product->id,
            'qty' => 1,
            'reported_by' => 'Teknisi B',
            'reporter_type' => RecipientType::TEKNISI->value,
            'damage_category' => DamageCategory::PHYSICAL_DEFECT->value,
            'damage_description' => 'Casing retak',
            'photo_proof' => $pngFile,
        ]);

        $damagedGood = DamagedGood::latest('id')->firstOrFail();
        $okResp->assertRedirect(route('damaged-goods.show', $damagedGood));

        $this->assertNotNull($damagedGood->photo_proof);
        Storage::disk('local')->assertExists($damagedGood->photo_proof);
        $this->assertEmpty(Storage::disk('public')->allFiles());

        // 3. Tamu tidak bisa mengakses endpoint foto privat
        auth()->logout();
        $this->get(route('damaged-goods.photo', $damagedGood))->assertRedirect(route('login'));

        // 4. Petugas terautentikasi dapat mengakses dengan header nosniff
        $photoResp = $this->actingAs($this->adminGudang)->get(route('damaged-goods.photo', $damagedGood));
        $photoResp->assertStatus(200);
        $photoResp->assertHeader('X-Content-Type-Options', 'nosniff');

        // 5. Jika transaksi gagal, file upload dibersihkan (no orphan file)
        $beforeFiles = Storage::disk('local')->allFiles('damaged_proofs');
        $this->mock(RecordDamagedGoodsAction::class, function ($mock) {
            $mock->shouldReceive('execute')->once()->andThrow(new \RuntimeException('DB failure'));
        });

        $orphanPng = UploadedFile::fake()->create('orphan.png', 100, 'image/png');
        $failResp = $this->actingAs($this->adminGudang)->post(route('damaged-goods.store'), [
            'product_id' => $product->id,
            'qty' => 1,
            'reported_by' => 'Teknisi C',
            'reporter_type' => RecipientType::TEKNISI->value,
            'damage_category' => DamageCategory::PHYSICAL_DEFECT->value,
            'damage_description' => 'Gagal simpan',
            'photo_proof' => $orphanPng,
        ]);

        $failResp->assertSessionHas('error');
        $this->assertEquals($beforeFiles, Storage::disk('local')->allFiles('damaged_proofs'));
    }

    public function test_security_headers_and_encrypted_session_defaults_are_enabled(): void
    {
        $sessionConfigRaw = file_get_contents(config_path('session.php'));
        $envExampleRaw = file_get_contents(base_path('.env.example'));

        $this->assertIsString($sessionConfigRaw);
        $this->assertIsString($envExampleRaw);
        $this->assertStringContainsString("env('SESSION_ENCRYPT', true)", $sessionConfigRaw);
        $this->assertStringContainsString('SESSION_ENCRYPT=true', $envExampleRaw);

        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_login_rate_limiter_scopes_by_email_and_ip(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'target.brute@gayuhmedia.id',
                'password' => 'wrong-pass',
            ])->assertStatus(302);
        }

        // Percobaan ke-6 untuk email yang sama dibatasi (429)
        $this->post(route('login'), [
            'email' => 'target.brute@gayuhmedia.id',
            'password' => 'wrong-pass',
        ])->assertStatus(429);

        // Email lain dari IP yang sama tetap diizinkan login
        $validLogin = $this->post(route('login'), [
            'email' => 'admin@gayuhmedia.id',
            'password' => 'password',
        ]);
        $validLogin->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->adminGudang);
    }

    public function test_sql_like_wildcard_characters_are_escaped_in_product_search(): void
    {
        $cat = Category::firstOrFail();
        $unit = Unit::firstOrFail();

        Product::create([
            'sku' => 'TST-PCT-100',
            'name' => 'Kabel Drop 100% Tembaga',
            'category_id' => $cat->id,
            'unit_id' => $unit->id,
            'stock' => 10,
            'damaged_stock' => 0,
            'min_stock' => 2,
            'cost_price' => 10000,
            'has_serial_number' => false,
        ]);

        $response = $this->actingAs($this->adminGudang)->get(route('products.index', ['search' => '%']));
        $response->assertStatus(200);
        $response->assertSee('Kabel Drop 100% Tembaga');
        $response->assertDontSee('ONT-ZTE-F609');
    }
}
