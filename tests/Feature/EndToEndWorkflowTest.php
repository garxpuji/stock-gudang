<?php

namespace Tests\Feature;

use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use App\Enums\SerialStatus;
use App\Models\Category;
use App\Models\DamagedGood;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ReturnTransaction;
use App\Models\StockInTransaction;
use App\Models\StockMutation;
use App\Models\StockOutTransaction;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Tests\TestCase;

class EndToEndWorkflowTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--database' => 'mariadb']);
        $this->seed([\Database\Seeders\UserSeeder::class]);
        $this->admin = User::where('email', 'admin@gayuhmedia.id')->firstOrFail();
    }

    public function test_complete_warehouse_lifecycle_from_empty_state(): void
    {
        // 1. STEP 1: Buat Master Data Kategori, Satuan, Lokasi, Supplier
        $category = Category::create([
            'name' => 'Perangkat Aktif ONT',
            'code' => 'ONT',
            'description' => 'Optical Network Terminal Pelanggan FTTH',
        ]);

        $unit = Unit::create([
            'name' => 'Unit (Pcs)',
            'abbreviation' => 'Unit',
        ]);

        $location = Location::create([
            'name' => 'Rak A1 - Perangkat Utama',
            'code' => 'RAK-A1',
            'description' => 'Lantai 1 Ruang Gudang ISP',
        ]);

        $supplier = Supplier::create([
            'name' => 'PT Fiberhome Technologies',
            'code' => 'SUP-FBT',
            'contact_name' => 'Bambang Vendor',
            'phone' => '081299998888',
            'email' => 'sales@fiberhome.id',
            'address' => 'Jakarta Selatan',
        ]);

        $this->assertDatabaseHas('categories', ['code' => 'ONT']);
        $this->assertDatabaseHas('units', ['abbreviation' => 'Unit']);
        $this->assertDatabaseHas('locations', ['code' => 'RAK-A1']);
        $this->assertDatabaseHas('suppliers', ['code' => 'SUP-FBT']);

        // 2. STEP 2: Daftarkan Produk Baru ke Master Katalog
        $productPayload = [
            'sku' => 'ONT-ZTE-F609-V3',
            'barcode' => '8991234567890',
            'name' => 'ZTE F609 GPON ONT V3.0',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'location_id' => $location->id,
            'min_stock' => 5,
            'cost_price' => 250000,
            'has_serial_number' => 1,
            'specification' => 'Dual Band WiFi 2.4/5GHz, 4 GE Ports, 1 POTS',
            'stock_notes' => 'Simpan di suhu ruang kering',
        ];

        $createProductResp = $this->actingAs($this->admin)->post(route('products.store'), $productPayload);
        $createProductResp->assertRedirect();
        $this->assertDatabaseHas('products', ['sku' => 'ONT-ZTE-F609-V3']);

        $product = Product::where('sku', 'ONT-ZTE-F609-V3')->firstOrFail();
        $this->assertEquals(0, (float) $product->stock);

        // 3. STEP 3: Transaksi Barang Masuk (Inbound / Pengadaan Vendor)
        $inboundPayload = [
            'supplier_id' => $supplier->id,
            'po_number' => 'PO-GMI-202611-001',
            'vendor_delivery_order_no' => 'SJ-FBT-9901',
            'courier_or_driver_name' => 'Joko (Truk Logistik)',
            'transaction_date' => now()->format('Y-m-d'),
            'notes' => 'Penerimaan batch pengadaan 3 unit ONT',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 3,
                    'cost_price' => 250000,
                    'serial_numbers' => "ZTEG001, ZTEG002, ZTEG003",
                ]
            ]
        ];

        $stockInResp = $this->actingAs($this->admin)->post(route('stock-in.store'), $inboundPayload);
        $stockInResp->assertRedirect();

        $product->refresh();
        $this->assertEquals(3, (float) $product->stock);
        $this->assertEquals(3, $product->availableSerials()->count());

        // Verifikasi mutasi tercatat
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'qty' => 3,
            'current_stock' => 3,
        ]);

        // 4. STEP 4: Transaksi Barang Keluar ke Teknisi (Outbound Dispatch)
        $sn1 = ProductSerial::where('serial_number', 'ZTEG001')->firstOrFail();
        $sn2 = ProductSerial::where('serial_number', 'ZTEG002')->firstOrFail();

        $outboundPayload = [
            'recipient_type' => RecipientType::TEKNISI->value,
            'picked_up_by' => 'Rian Hidayat',
            'recipient_department_or_team' => 'Tim Teknisi Lapangan FTTH',
            'reference_ticket_no' => 'TICK-PSB-2026-001',
            'purpose' => 'Pasang Baru Pelanggan FTTH Griya Indah Blok A-12',
            'transaction_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2,
                    'serial_ids' => [$sn1->id, $sn2->id],
                    'notes' => 'Diserahkan dalam kondisi segel baik',
                ]
            ]
        ];

        $stockOutResp = $this->actingAs($this->admin)->post(route('stock-out.store'), $outboundPayload);
        $stockOutResp->assertRedirect();

        $product->refresh();
        $this->assertEquals(1, (float) $product->stock);

        // Verifikasi status SN berubah menjadi ISSUED
        $this->assertDatabaseHas('product_serials', ['serial_number' => 'ZTEG001', 'status' => SerialStatus::ISSUED->value]);
        $this->assertDatabaseHas('product_serials', ['serial_number' => 'ZTEG002', 'status' => SerialStatus::ISSUED->value]);
        $this->assertDatabaseHas('product_serials', ['serial_number' => 'ZTEG003', 'status' => SerialStatus::AVAILABLE->value]);

        $stockOutTx = StockOutTransaction::latest('id')->firstOrFail();

        // Print Surat Jalan Outbound
        $printOutResp = $this->actingAs($this->admin)->get(route('stock-out.print', $stockOutTx));
        $printOutResp->assertStatus(200);
        $printOutResp->assertSee('Bukti Pengeluaran Barang');
        $printOutResp->assertSee('Rian Hidayat');

        // 5. STEP 5: Transaksi Retur & Inspeksi QC (1 Baik Restock, 1 Rusak Karantina)
        $returnPayload = [
            'returned_by' => 'Rian Hidayat (Teknisi Lapangan)',
            'recipient_type' => RecipientType::TEKNISI->value,
            'return_category' => 'Dismantle / Pembatalan PSB Pelanggan',
            'reference_ticket_no' => 'TICK-PSB-2026-001',
            'original_stock_out_id' => $stockOutTx->id,
            'transaction_date' => now()->format('Y-m-d'),
            'keterangan_pengembalian' => '1 unit normal restock, 1 unit terkena petir di lokasi',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 1,
                    'serial_number' => 'ZTEG001',
                    'qc_status' => QcStatus::GOOD->value,
                    'qc_notes' => 'Casing bersih, redaman stabil -19dBm',
                ],
                [
                    'product_id' => $product->id,
                    'qty' => 1,
                    'serial_number' => 'ZTEG002',
                    'qc_status' => QcStatus::DAMAGED->value,
                    'damage_category' => DamageCategory::LIGHTNING_STRIKE->value,
                    'damage_description' => 'Port LAN gosong dan adaptor meledak',
                    'qc_notes' => 'Mati total tidak keluar power',
                ]
            ]
        ];

        $returnResp = $this->actingAs($this->admin)->post(route('returns.store'), $returnPayload);
        $returnResp->assertRedirect();

        $product->refresh();
        // Stok aktif bertambah 1 dari yang GOOD (1 + 1 = 2)
        $this->assertEquals(2, (float) $product->stock);
        // Stok rusak bertambah 1 dari yang DAMAGED
        $this->assertEquals(1, (float) $product->damaged_stock);

        // Verifikasi serial number yang GOOD kembali AVAILABLE
        $this->assertDatabaseHas('product_serials', ['serial_number' => 'ZTEG001', 'status' => SerialStatus::AVAILABLE->value]);
        // Verifikasi serial number yang DAMAGED berubah menjadi DAMAGED
        $this->assertDatabaseHas('product_serials', ['serial_number' => 'ZTEG002', 'status' => SerialStatus::DAMAGED->value]);

        // Verifikasi masuk ke tabel karantina damaged_goods
        $this->assertDatabaseHas('damaged_goods', [
            'product_id' => $product->id,
            'serial_number' => 'ZTEG002',
            'action_taken' => DamagedActionTaken::PENDING->value,
        ]);

        $returnTx = ReturnTransaction::latest('id')->firstOrFail();
        $printReturnResp = $this->actingAs($this->admin)->get(route('returns.print', $returnTx));
        $printReturnResp->assertStatus(200);
        $printReturnResp->assertSee('Bukti Penerimaan Barang Kembali');

        // 6. STEP 6: Resolusi Barang Rusak di Area Karantina (RMA Vendor)
        $damagedRecord = DamagedGood::where('serial_number', 'ZTEG002')->firstOrFail();
        $resolveResp = $this->actingAs($this->admin)->post(route('damaged-goods.resolve', $damagedRecord), [
            'action_taken' => DamagedActionTaken::RMA_VENDOR->value,
            'rma_number' => 'RMA-FBT-2026-0089',
            'action_notes' => 'Kirim balik ke vendor PT Fiberhome untuk klaim garansi ganti unit baru',
        ]);
        $resolveResp->assertRedirect();

        $damagedRecord->refresh();
        $this->assertEquals(DamagedActionTaken::RMA_VENDOR, $damagedRecord->action_taken);
        $this->assertDatabaseHas('stock_mutations', [
            'reference_table' => 'damaged_goods',
            'reference_id' => $damagedRecord->id,
            'reference_ticket_no' => 'RMA-FBT-2026-0089',
        ]);

        // 7. STEP 7: Stok Opname Fisik
        // Misalkan perhitungan fisik menemukan stok fisik sebenarnya ada 3 unit (selisih +1)
        $opnamePayload = [
            'product_id' => $product->id,
            'actual_stock' => 3,
            'actual_damaged_stock' => 1,
            'person_in_charge' => 'Budi Santoso (Auditor Internal)',
            'notes' => 'Penemuan 1 unit ONT di rak belakang saat audit fisik bulanan',
        ];

        $opnameResp = $this->actingAs($this->admin)->post(route('opname.store'), $opnamePayload);
        $opnameResp->assertRedirect();

        $product->refresh();
        $this->assertEquals(3, (float) $product->stock);

        // 8. STEP 8: Kartu Stok Terpadu & Audit Mutasi
        $mutationsResp = $this->actingAs($this->admin)->get(route('mutations.index', ['product_id' => $product->id]));
        $mutationsResp->assertStatus(200);
        $mutationsResp->assertSee('Kartu Stok Terpadu');
        $mutationsResp->assertSee($product->sku);

        // 9. STEP 9: Dashboard KPI Analytics
        $dashboardResp = $this->actingAs($this->admin)->get(route('dashboard'));
        $dashboardResp->assertStatus(200);
        $dashboardResp->assertSee('Dashboard Logistik &amp; Inventaris', false);
        $dashboardResp->assertSee($product->name);

        // 10. STEP 10: Export Laporan
        $exportProductsResp = $this->actingAs($this->admin)->get(route('export.products', ['type' => 'excel']));
        $exportProductsResp->assertStatus(200);

        $exportStockOutResp = $this->actingAs($this->admin)->get(route('export.stock-out', ['type' => 'excel']));
        $exportStockOutResp->assertStatus(200);

        $exportReturnsResp = $this->actingAs($this->admin)->get(route('export.returns', ['type' => 'excel']));
        $exportReturnsResp->assertStatus(200);

        $exportDamagedResp = $this->actingAs($this->admin)->get(route('export.damaged-goods', ['type' => 'excel']));
        $exportDamagedResp->assertStatus(200);
    }
}
