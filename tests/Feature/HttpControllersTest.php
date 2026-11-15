<?php

namespace Tests\Feature;

use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ReturnTransaction;
use App\Models\StockOutTransaction;
use App\Models\User;
use Tests\TestCase;

class HttpControllersTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--database' => 'mariadb']);
        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\CategorySeeder::class,
            \Database\Seeders\UnitSeeder::class,
            \Database\Seeders\LocationSeeder::class,
            \Database\Seeders\SupplierSeeder::class,
            \Database\Seeders\ProductSeeder::class,
            \Database\Seeders\ProductSerialSeeder::class,
            \Database\Seeders\InitialTransactionsSeeder::class,
        ]);
        $this->admin = User::where('email', 'admin@gayuhmedia.id')->firstOrFail();
    }

    public function test_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Gayuh Media Informatika');
        $response->assertSee('Dashboard Logistik & Inventaris');
        $response->assertSee('Total SKU');
    }

    public function test_products_index_and_show_render_successfully(): void
    {
        $product = Product::where('sku', 'ONT-ZTE-F609')->firstOrFail();

        $responseIndex = $this->actingAs($this->admin)->get(route('products.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Katalog', false);
        $responseIndex->assertSee($product->sku);

        $responseShow = $this->actingAs($this->admin)->get(route('products.show', $product));
        $responseShow->assertStatus(200);
        $responseShow->assertSee($product->name);
        $responseShow->assertSee('Inventaris Serial Number', false);
    }

    public function test_stock_out_flow_and_print_view(): void
    {
        $product = Product::where('sku', 'ONT-ZTE-F609')->firstOrFail();

        // 1. Render create form
        $responseCreate = $this->actingAs($this->admin)->get(route('stock-out.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Loket Pengeluaran Barang');

        // 2. Submit dispatch
        $responseStore = $this->actingAs($this->admin)->post(route('stock-out.store'), [
            'recipient_type' => RecipientType::TEKNISI->value,
            'picked_up_by' => 'Rahmat Teknisi',
            'recipient_department_or_team' => 'Tim Fiber 1',
            'reference_ticket_no' => 'SPK-TEST-HTTP-01',
            'purpose' => 'Pasang Baru 100Mbps',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 1,
                ],
            ],
        ]);

        $responseStore->assertRedirect();
        $this->assertDatabaseHas('stock_out_transactions', [
            'picked_up_by' => 'Rahmat Teknisi',
            'reference_ticket_no' => 'SPK-TEST-HTTP-01',
        ]);

        $tx = StockOutTransaction::where('reference_ticket_no', 'SPK-TEST-HTTP-01')->firstOrFail();

        // 3. Render print view
        $responsePrint = $this->actingAs($this->admin)->get(route('stock-out.print', $tx));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSee('Surat Serah Terima / Bukti Pengeluaran Barang');
        $responsePrint->assertSee('Rahmat Teknisi');
    }

    public function test_return_flow_with_qc_and_print(): void
    {
        $product = Product::where('sku', 'ONT-HW-HG8546M')->firstOrFail();

        // 1. Render create form
        $responseCreate = $this->actingAs($this->admin)->get(route('returns.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Form Pengembalian Barang', false);

        // 2. Submit return with QC: DAMAGED
        $responseStore = $this->actingAs($this->admin)->post(route('returns.store'), [
            'returned_by' => 'Eko Saputra',
            'returned_by_contact' => '081234455',
            'recipient_type' => RecipientType::TEKNISI->value,
            'return_category' => 'Bongkaran Pelanggan (Dismantle Cacat)',
            'reference_ticket_no' => 'TKT-TEST-RET-02',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 1,
                    'qc_status' => QcStatus::DAMAGED->value,
                    'serial_number' => 'SN-HW-RET-TEST',
                    'damage_category' => DamageCategory::LIGHTNING_STRIKE->value,
                    'damage_description' => 'Port LAN hangus kena petir',
                    'qc_notes' => 'Pemeriksaan port terbakar',
                ],
            ],
        ]);

        $responseStore->assertRedirect();
        $this->assertDatabaseHas('return_transactions', [
            'returned_by' => 'Eko Saputra',
            'reference_ticket_no' => 'TKT-TEST-RET-02',
        ]);

        $ret = ReturnTransaction::where('reference_ticket_no', 'TKT-TEST-RET-02')->firstOrFail();

        // 3. Render print view
        $responsePrint = $this->actingAs($this->admin)->get(route('returns.print', $ret));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSee('Bukti Penerimaan Barang Kembali (Retur)');
        $responsePrint->assertSee('Eko Saputra');
    }

    public function test_damaged_goods_resolve_action(): void
    {
        $dmg = DamagedGood::where('action_taken', DamagedActionTaken::PENDING)->firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('damaged-goods.resolve', $dmg), [
            'action_taken' => DamagedActionTaken::RMA_VENDOR->value,
            'rma_number' => 'RMA-VENDOR-TEST-123',
            'action_notes' => 'Dikirim ke distributor Huawei',
        ]);

        $response->assertRedirect();
        $dmg->refresh();
        $this->assertEquals(DamagedActionTaken::RMA_VENDOR, $dmg->action_taken);
    }

    public function test_stock_mutations_ledger_view(): void
    {
        $response = $this->actingAs($this->admin)->get(route('mutations.index'));
        $response->assertStatus(200);
        $response->assertSee('Kartu Stok Terpadu (Unified Stock Ledger)');
    }

    public function test_login_flow(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Gayuh Media Informatika');
        $response->assertSee('Masuk Petugas Gudang');

        $loginPost = $this->post(route('login'), [
            'email' => 'admin@gayuhmedia.id',
            'password' => 'password',
        ]);

        $loginPost->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }
}
