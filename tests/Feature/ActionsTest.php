<?php

namespace Tests\Feature;

use App\Actions\InboundStockAction;
use App\Actions\OutboundStockAction;
use App\Actions\ProcessReturnAction;
use App\Actions\RecordDamagedGoodsAction;
use App\Actions\ResolveDamagedGoodsAction;
use App\Actions\StockOpnameAction;
use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\MutationType;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use App\Enums\SerialStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\StockMutation;
use App\Models\User;
use Tests\TestCase;

class ActionsTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--database' => 'mariadb']);
        $this->seed();
        $this->admin = User::where('email', 'admin@gayuhmedia.id')->firstOrFail();
    }

    public function test_inbound_stock_action_increases_stock_and_records_mutation(): void
    {
        $product = Product::where('sku', 'ONT-ZTE-F609')->firstOrFail();
        $initialStock = (float) $product->stock;

        $action = new InboundStockAction();
        $transaction = $action->execute([
            'supplier_name' => 'PT Fiberhome',
            'po_number' => 'PO-TEST-001',
            'vendor_delivery_order_no' => 'SJ-TEST-001',
            'courier_or_driver_name' => 'Kurir Express',
            'notes' => 'Test inbound',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 10,
                    'cost_price' => 290000,
                    'serials' => [
                        ['serial_number' => 'ZTE-NEW-001', 'mac_address' => '00:11:22:33:44:01'],
                        ['serial_number' => 'ZTE-NEW-002', 'mac_address' => '00:11:22:33:44:02'],
                    ],
                ],
            ],
        ], $this->admin->id);

        $this->assertNotNull($transaction);
        $product->refresh();
        $this->assertEquals($initialStock + 10, (float) $product->stock);

        // Check serial created
        $serial = ProductSerial::where('serial_number', 'ZTE-NEW-001')->first();
        $this->assertNotNull($serial);
        $this->assertEquals(SerialStatus::AVAILABLE, $serial->status);

        // Check mutation recorded
        $mutation = StockMutation::where('reference_ticket_no', 'PO-TEST-001')->first();
        $this->assertNotNull($mutation);
        $this->assertEquals(MutationType::INBOUND, $mutation->mutation_type);
        $this->assertEquals(10, (float) $mutation->qty);
    }

    public function test_outbound_stock_action_deducts_stock_and_validates_limits(): void
    {
        $product = Product::where('sku', 'ONT-ZTE-F609')->firstOrFail();
        $initialStock = (float) $product->stock;
        $serial = ProductSerial::where('product_id', $product->id)
            ->where('status', SerialStatus::AVAILABLE)
            ->firstOrFail();

        $action = new OutboundStockAction();
        $transaction = $action->execute([
            'recipient_type' => RecipientType::TEKNISI,
            'picked_up_by' => 'Joko Susilo',
            'recipient_department_or_team' => 'Tim PSB 2',
            'reference_ticket_no' => 'SPK-TEST-99',
            'purpose' => 'Pasang Baru Pelanggan',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 1,
                    'serial_ids' => [$serial->id],
                ],
            ],
        ], $this->admin->id);

        $this->assertNotNull($transaction);
        $product->refresh();
        $this->assertEquals($initialStock - 1, (float) $product->stock);

        $serial->refresh();
        $this->assertEquals(SerialStatus::ISSUED, $serial->status);
        $this->assertStringContainsString('Joko Susilo', $serial->last_recipient);

        // Test insufficient stock throws exception
        $this->expectException(InsufficientStockException::class);
        $action->execute([
            'recipient_type' => RecipientType::TEKNISI,
            'picked_up_by' => 'Joko Susilo',
            'recipient_department_or_team' => 'Tim PSB 2',
            'purpose' => 'Request Over Limit',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 99999,
                ],
            ],
        ], $this->admin->id);
    }

    public function test_process_return_action_qc_branching(): void
    {
        $productDropcore = Product::where('sku', 'CBL-DC-1C-MTR')->firstOrFail();
        $productOnt = Product::where('sku', 'ONT-HW-HG8546M')->firstOrFail();

        $initialDcStock = (float) $productDropcore->stock;
        $initialOntStock = (float) $productOnt->stock;
        $initialOntDamaged = (float) $productOnt->damaged_stock;

        $action = new ProcessReturnAction();
        $returnTx = $action->execute([
            'returned_by' => 'Bambang Teknisi',
            'returned_by_contact' => '0812345678',
            'recipient_type' => RecipientType::TEKNISI,
            'reference_ticket_no' => 'TKT-RET-01',
            'return_category' => 'Sisa Proyek & Dismantle',
            'items' => [
                // Item 1: GOOD -> Should Restock active stock
                [
                    'product_id' => $productDropcore->id,
                    'qty' => 50,
                    'qc_status' => QcStatus::GOOD,
                    'qc_notes' => 'Sisa tarikan kabel mulus',
                ],
                // Item 2: DAMAGED -> Should NOT increase active stock, increase damaged_stock & create damaged_goods record!
                [
                    'product_id' => $productOnt->id,
                    'qty' => 1,
                    'qc_status' => QcStatus::DAMAGED,
                    'serial_number' => '48575443-TEST-DMG',
                    'qc_notes' => 'Port LAN rusak tersambar petir',
                    'damage_category' => DamageCategory::LIGHTNING_STRIKE,
                    'damage_description' => 'Port terbakar kena petir',
                ],
            ],
        ], $this->admin->id);

        $this->assertNotNull($returnTx);

        // Verify Item 1 (Good)
        $productDropcore->refresh();
        $this->assertEquals($initialDcStock + 50, (float) $productDropcore->stock);

        // Verify Item 2 (Damaged)
        $productOnt->refresh();
        $this->assertEquals($initialOntStock, (float) $productOnt->stock); // Active stock unchanged!
        $this->assertEquals($initialOntDamaged + 1, (float) $productOnt->damaged_stock); // Damaged stock increased!

        // Verify DamagedGood record created
        $dmg = DamagedGood::where('serial_number', '48575443-TEST-DMG')->first();
        $this->assertNotNull($dmg);
        $this->assertEquals(DamageCategory::LIGHTNING_STRIKE, $dmg->damage_category);
        $this->assertEquals(DamagedActionTaken::PENDING, $dmg->action_taken);
    }

    public function test_resolve_damaged_goods_action(): void
    {
        $product = Product::where('sku', 'ONT-ZTE-F609')->firstOrFail();
        $dmg = DamagedGood::where('action_taken', DamagedActionTaken::PENDING)->firstOrFail();
        $damagedProductId = $dmg->product_id;
        $damagedProduct = Product::findOrFail($damagedProductId);

        $initialDamaged = (float) $damagedProduct->damaged_stock;
        $initialActive = (float) $damagedProduct->stock;

        $action = new ResolveDamagedGoodsAction();
        // Resolve with REPAIRED_INTERNAL
        $resolved = $action->execute(
            $dmg->id,
            DamagedActionTaken::REPAIRED_INTERNAL,
            ['action_notes' => 'Selesai ganti kapasitor dan port LAN'],
            $this->admin->id
        );

        $this->assertEquals(DamagedActionTaken::REPAIRED_INTERNAL, $resolved->action_taken);

        $damagedProduct->refresh();
        $this->assertEquals($initialDamaged - (float) $dmg->qty, (float) $damagedProduct->damaged_stock);
        $this->assertEquals($initialActive + (float) $dmg->qty, (float) $damagedProduct->stock);
    }

    public function test_stock_opname_action(): void
    {
        $product = Product::firstOrFail();

        $action = new StockOpnameAction();
        $updatedProduct = $action->execute([
            'product_id' => $product->id,
            'actual_stock' => 99.00,
            'actual_damaged_stock' => 5.00,
            'person_in_charge' => 'Lead Auditor',
            'notes' => 'Opname akhir bulan',
        ], $this->admin->id);

        $this->assertEquals(99.00, (float) $updatedProduct->stock);
        $this->assertEquals(5.00, (float) $updatedProduct->damaged_stock);

        $mutation = StockMutation::where('mutation_type', MutationType::OPNAME_ADJUSTMENT)
            ->where('product_id', $product->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($mutation);
        $this->assertEquals('Lead Auditor', $mutation->person_in_charge);
        $this->assertEquals(99.00, (float) $mutation->current_stock);
    }
}
