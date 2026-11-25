<?php

namespace Tests\Feature;

use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use App\Enums\SerialStatus;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ReturnTransaction;
use App\Models\StockOutTransaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\InitialTransactionsSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ProductSerialSeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class HttpControllersTest extends TestCase
{
    protected User $admin;

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
            ProductSerialSeeder::class,
            InitialTransactionsSeeder::class,
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
        $serial = ProductSerial::where('product_id', $product->id)
            ->where('status', SerialStatus::AVAILABLE)
            ->firstOrFail();

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
                    'serial_ids' => [$serial->id],
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

    public function test_role_authorization_boundaries_for_admin_gudang_and_staff_gudang(): void
    {
        $product = Product::where('sku', 'ONT-ZTE-F609')->firstOrFail();
        $dmg = DamagedGood::where('action_taken', DamagedActionTaken::PENDING)->firstOrFail();

        // admin_gudang: full access except user management
        $this->actingAs($this->admin)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($this->admin)->get(route('opname.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('categories.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('products.create'))->assertStatus(200);

        // staff_gudang: operational routes allowed, admin/master/opname/resolve denied
        $staff = User::create([
            'name' => 'Staff Operasional',
            'email' => 'staff.ops@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->actingAs($staff)->get(route('dashboard'))->assertStatus(200);
        $this->actingAs($staff)->get(route('products.index'))->assertStatus(200);
        $this->actingAs($staff)->get(route('stock-out.create'))->assertStatus(200);

        $this->actingAs($staff)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($staff)->get(route('products.create'))->assertStatus(403);
        $this->actingAs($staff)->delete(route('products.destroy', $product))->assertStatus(403);
        $this->actingAs($staff)->post(route('categories.store'), ['name' => 'X', 'code' => 'X'])->assertStatus(403);
        $this->actingAs($staff)->post(route('opname.store'), [
            'product_id' => $product->id,
            'actual_stock' => 10,
            'actual_damaged_stock' => 0,
            'person_in_charge' => 'Staff',
        ])->assertStatus(403);
        $this->actingAs($staff)->post(route('damaged-goods.resolve', $dmg), [
            'action_taken' => DamagedActionTaken::SCRAPPED->value,
        ])->assertStatus(403);
    }

    public function test_excel_export_neutralizes_formula_injection(): void
    {
        $product = Product::where('sku', 'CBL-DC-1C-MTR')->firstOrFail();

        $this->actingAs($this->admin)->post(route('stock-out.store'), [
            'recipient_type' => RecipientType::TEKNISI->value,
            'picked_up_by' => '+cmd|calc',
            'recipient_department_or_team' => 'Tim Fiber',
            'reference_ticket_no' => '@SUM(1,1)',
            'purpose' => '=SUM(1,2)',
            'transaction_date' => now()->format('Y-m-d\TH:i'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 1,
                ],
            ],
        ])->assertRedirect();

        $response = $this->actingAs($this->admin)->get(route('export.stock-out', ['type' => 'excel']));
        $response->assertStatus(200);

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_test_');
        file_put_contents($tmpFile, $response->streamedContent());

        try {
            $spreadsheet = IOFactory::load($tmpFile);
            $sheet = $spreadsheet->getActiveSheet();

            // Row 5 is the newest transaction (ordered latest first); columns D(4)=picked_up_by, H(8)=ticket, I(9)=purpose
            $pickedUpCell = $sheet->getCell('D5');
            $ticketCell = $sheet->getCell('H5');
            $purposeCell = $sheet->getCell('I5');

            $this->assertEquals(DataType::TYPE_STRING, $pickedUpCell->getDataType());
            $this->assertEquals("'+cmd|calc", $pickedUpCell->getValue());

            $this->assertEquals(DataType::TYPE_STRING, $ticketCell->getDataType());
            $this->assertEquals("'@SUM(1,1)", $ticketCell->getValue());

            $this->assertEquals(DataType::TYPE_STRING, $purposeCell->getDataType());
            $this->assertEquals("'=SUM(1,2)", $purposeCell->getValue());
        } finally {
            @unlink($tmpFile);
        }
    }
}
