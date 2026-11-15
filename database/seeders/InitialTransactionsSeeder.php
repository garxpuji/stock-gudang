<?php

namespace Database\Seeders;

use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\MutationType;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\ReturnTransaction;
use App\Models\ReturnTransactionItem;
use App\Models\StockInItem;
use App\Models\StockInTransaction;
use App\Models\StockMutation;
use App\Models\StockOutItem;
use App\Models\StockOutTransaction;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InitialTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@gayuhmedia.id')->first();
        if (! $admin) {
            return;
        }

        $now = Carbon::now();

        // 1. INBOUND TRANSACTIONS
        $supZte = Supplier::where('code', 'SUP-ZTE-FIBER')->first();
        $prodZte = Product::where('sku', 'ONT-ZTE-F609')->first();
        $prodSfp = Product::where('sku', 'SFP-BIDI-1G')->first();

        if ($supZte && $prodZte && $prodSfp) {
            $inbound1 = StockInTransaction::updateOrCreate(
                ['transaction_code' => 'IN-202611-0001'],
                [
                    'supplier_id' => $supZte->id,
                    'supplier_name' => $supZte->name,
                    'po_number' => 'PO-GMI-202611-001',
                    'vendor_delivery_order_no' => 'SJ-ZTE-2026-9912',
                    'courier_or_driver_name' => 'Supardi (JNE Cargo Truck B 9812 PXZ)',
                    'transaction_date' => $now->copy()->subDays(5)->toDateString(),
                    'received_by_user_id' => $admin->id,
                    'notes' => 'Pengadaan rutin ONT ZTE Dual Band dan SFP BiDi 1.25G. Kondisi kardus segel utuh.',
                    'total_items' => 65,
                    'total_cost' => (30 * 285000) + (35 * 95000),
                ]
            );

            StockInItem::updateOrCreate(
                ['stock_in_transaction_id' => $inbound1->id, 'product_id' => $prodZte->id],
                [
                    'qty' => 30.00,
                    'cost_price' => 285000.00,
                    'subtotal' => 30 * 285000.00,
                    'notes' => 'Serial number tertera di kardus ZTEGCA881001 s/d ZTEGCA881030',
                ]
            );

            StockInItem::updateOrCreate(
                ['stock_in_transaction_id' => $inbound1->id, 'product_id' => $prodSfp->id],
                [
                    'qty' => 35.00,
                    'cost_price' => 95000.00,
                    'subtotal' => 35 * 95000.00,
                    'notes' => 'Modul optik Blue/Blue Tx1310/Rx1550nm',
                ]
            );

            // Record initial mutations
            StockMutation::updateOrCreate(
                [
                    'reference_table' => 'stock_in_transactions',
                    'reference_id' => $inbound1->id,
                    'product_id' => $prodZte->id,
                ],
                [
                    'mutation_type' => MutationType::INBOUND,
                    'qty' => 30.00,
                    'previous_stock' => 0.00,
                    'current_stock' => 30.00,
                    'previous_damaged_stock' => 0.00,
                    'current_damaged_stock' => 0.00,
                    'system_operator_id' => $admin->id,
                    'person_in_charge' => 'Supardi (Kurir JNE Cargo)',
                    'reference_ticket_no' => 'PO-GMI-202611-001',
                    'remarks' => 'Penerimaan barang masuk dari PT Fiberhome & ZTE Optic Network (SJ: SJ-ZTE-2026-9912)',
                    'created_at' => $now->copy()->subDays(5),
                ]
            );
        }

        // 2. OUTBOUND TRANSACTIONS
        $prodDcMtr = Product::where('sku', 'CBL-DC-1C-MTR')->first();
        $prodFast = Product::where('sku', 'CON-FAST-SCUPC')->first();
        $prodHex = Product::where('sku', 'RTR-RB750GR3')->first();

        // Outbound 1: Teknisi PSB
        if ($prodZte && $prodDcMtr && $prodFast) {
            $out1 = StockOutTransaction::updateOrCreate(
                ['transaction_code' => 'OUT-202611-0001'],
                [
                    'recipient_type' => RecipientType::TEKNISI,
                    'picked_up_by' => 'Ahmad Wahyudi',
                    'recipient_department_or_team' => 'Tim Fiber 1 (PSB Area Barat)',
                    'recipient_phone' => '081255443322',
                    'reference_ticket_no' => 'SPK-PSB-202611-042',
                    'purpose' => 'Pasang Baru (PSB) Pelanggan Perumahan Griya Indah Blok C No. 14 (Paket 50Mbps)',
                    'keterangan_pengeluaran' => 'Pengambilan material pasang baru & 1 unit ONT ZTE Dual Band untuk instalasi pelanggan rumah Bapak Hendro.',
                    'dispatched_by_user_id' => $admin->id,
                    'transaction_date' => $now->copy()->subDays(2),
                    'status' => 'COMPLETED',
                    'total_items' => 3,
                ]
            );

            StockOutItem::updateOrCreate(
                ['stock_out_transaction_id' => $out1->id, 'product_id' => $prodZte->id],
                [
                    'qty' => 1.00,
                    'notes' => 'SN: ZTEGCA881006 / MAC: 48:D2:24:A1:01:06',
                ]
            );

            StockOutItem::updateOrCreate(
                ['stock_out_transaction_id' => $out1->id, 'product_id' => $prodDcMtr->id],
                [
                    'qty' => 150.00,
                    'notes' => 'Tarikan drop core dari ODP-GRIYA-04 ke rumah pelanggan',
                ]
            );

            // Record mutation
            StockMutation::updateOrCreate(
                [
                    'reference_table' => 'stock_out_transactions',
                    'reference_id' => $out1->id,
                    'product_id' => $prodZte->id,
                ],
                [
                    'mutation_type' => MutationType::OUTBOUND_TEKNISI,
                    'qty' => -1.00,
                    'previous_stock' => 30.00,
                    'current_stock' => 29.00,
                    'previous_damaged_stock' => 0.00,
                    'current_damaged_stock' => 0.00,
                    'system_operator_id' => $admin->id,
                    'person_in_charge' => 'Ahmad Wahyudi (Tim Fiber 1)',
                    'reference_ticket_no' => 'SPK-PSB-202611-042',
                    'remarks' => 'Pengeluaran 1 unit ONT ZTE untuk Pasang Baru pelanggan Perum Griya Indah Blok C',
                    'created_at' => $now->copy()->subDays(2),
                ]
            );
        }

        // Outbound 2: Karyawan Internal NOC
        if ($prodHex && $prodSfp) {
            $out2 = StockOutTransaction::updateOrCreate(
                ['transaction_code' => 'OUT-202611-0002'],
                [
                    'recipient_type' => RecipientType::KARYAWAN,
                    'picked_up_by' => 'Dani Ramadhan',
                    'recipient_department_or_team' => 'NOC & Core Infrastructure',
                    'recipient_phone' => '081399887766',
                    'reference_ticket_no' => 'INC-NOC-202611-018',
                    'purpose' => 'Penggantian Router Gateway PoP Sukajadi & Uji Bandwidth Peering',
                    'keterangan_pengeluaran' => 'Router hEX RB750Gr3 lama di PoP hang pasca lonjakan listrik, diambil pengganti baru.',
                    'dispatched_by_user_id' => $admin->id,
                    'transaction_date' => $now->copy()->subDays(1),
                    'status' => 'COMPLETED',
                    'total_items' => 2,
                ]
            );

            StockOutItem::updateOrCreate(
                ['stock_out_transaction_id' => $out2->id, 'product_id' => $prodHex->id],
                [
                    'qty' => 1.00,
                    'notes' => 'SN: 6E0807C4A003 / MAC: CC:2D:E0:51:A0:03',
                ]
            );

            StockMutation::updateOrCreate(
                [
                    'reference_table' => 'stock_out_transactions',
                    'reference_id' => $out2->id,
                    'product_id' => $prodHex->id,
                ],
                [
                    'mutation_type' => MutationType::OUTBOUND_KARYAWAN,
                    'qty' => -1.00,
                    'previous_stock' => 9.00,
                    'current_stock' => 8.00,
                    'previous_damaged_stock' => 0.00,
                    'current_damaged_stock' => 0.00,
                    'system_operator_id' => $admin->id,
                    'person_in_charge' => 'Dani Ramadhan (NOC)',
                    'reference_ticket_no' => 'INC-NOC-202611-018',
                    'remarks' => 'Pengeluaran MikroTik hEX untuk kebutuhan penggantian perangkat di PoP Sukajadi',
                    'created_at' => $now->copy()->subDays(1),
                ]
            );
        }

        // 3. RETURNS WITH QC (GOOD RESTOCKED vs DAMAGED QUARANTINED)
        // Return 1: Sisa Material Baik
        if ($prodDcMtr) {
            $ret1 = ReturnTransaction::updateOrCreate(
                ['transaction_code' => 'RET-202611-0001'],
                [
                    'returned_by' => 'Ahmad Wahyudi',
                    'returned_by_contact' => '081255443322 (Tim Fiber 1)',
                    'recipient_type' => RecipientType::TEKNISI,
                    'reference_ticket_no' => 'SPK-PSB-202611-042',
                    'return_category' => 'Sisa Material Proyek PSB',
                    'keterangan_pengembalian' => 'Sisa kabel drop core tarikan PSB Griya Indah tidak habis terpakai, dikembalikan ke gudang.',
                    'received_by_user_id' => $admin->id,
                    'transaction_date' => $now->copy()->subDay(),
                    'total_items' => 1,
                ]
            );

            ReturnTransactionItem::updateOrCreate(
                ['return_transaction_id' => $ret1->id, 'product_id' => $prodDcMtr->id],
                [
                    'qty' => 30.00,
                    'qc_status' => QcStatus::GOOD,
                    'qc_notes' => 'Kondisi kabel mulus, kawat messenger utuh tanpa tekukan. Layak untuk pemakaian PSB berikutnya.',
                    'action_taken' => 'RESTOCKED',
                ]
            );

            StockMutation::updateOrCreate(
                [
                    'reference_table' => 'return_transactions',
                    'reference_id' => $ret1->id,
                    'product_id' => $prodDcMtr->id,
                ],
                [
                    'mutation_type' => MutationType::RETURN_GOOD,
                    'qty' => 30.00,
                    'previous_stock' => 420.00,
                    'current_stock' => 450.00,
                    'previous_damaged_stock' => 0.00,
                    'current_damaged_stock' => 0.00,
                    'system_operator_id' => $admin->id,
                    'person_in_charge' => 'Ahmad Wahyudi (Tim Fiber 1)',
                    'reference_ticket_no' => 'SPK-PSB-202611-042',
                    'remarks' => 'Retur sisa material drop core 30 meter kondisi BAIK (QC lolos, langsung restock)',
                    'created_at' => $now->copy()->subDay(),
                ]
            );
        }

        // Return 2: Barang Dismantle Rusak (QC: DAMAGED -> QUARANTINED)
        if ($prodZte) {
            $ret2 = ReturnTransaction::updateOrCreate(
                ['transaction_code' => 'RET-202611-0002'],
                [
                    'returned_by' => 'Rian Hidayat',
                    'returned_by_contact' => '081344556677 (Maintenance)',
                    'recipient_type' => RecipientType::TEKNISI,
                    'reference_ticket_no' => 'TKT-MTN-202611-109',
                    'return_category' => 'Bongkaran Pelanggan (Dismantle Cacat)',
                    'keterangan_pengembalian' => 'Modem ONT bekas pelanggan Jl. Mangga No. 4 yang tersambar petir semalam.',
                    'received_by_user_id' => $admin->id,
                    'transaction_date' => $now->copy()->subHours(6),
                    'total_items' => 1,
                ]
            );

            $retItem2 = ReturnTransactionItem::updateOrCreate(
                ['return_transaction_id' => $ret2->id, 'product_id' => $prodZte->id],
                [
                    'qty' => 1.00,
                    'qc_status' => QcStatus::DAMAGED,
                    'serial_number' => 'ZTEGCA881008',
                    'mac_address' => '48:D2:24:A1:01:08',
                    'qc_notes' => 'Port LAN 1-4 hangus berbau terbakar, indikator LOS merah mati total pasca petir.',
                    'action_taken' => 'QUARANTINED_DAMAGED',
                ]
            );

            // Automatically route to damaged_goods table
            $dmg1 = DamagedGood::updateOrCreate(
                ['code' => 'DMG-202611-0001'],
                [
                    'product_id' => $prodZte->id,
                    'serial_number' => 'ZTEGCA881008',
                    'mac_address' => '48:D2:24:A1:01:08',
                    'qty' => 1.00,
                    'reported_at' => $now->copy()->subHours(6),
                    'reported_by' => 'Rian Hidayat (Maintenance)',
                    'reporter_type' => RecipientType::TEKNISI,
                    'damage_category' => DamageCategory::LIGHTNING_STRIKE,
                    'damage_description' => 'Port LAN 1 sampai 4 hangus terbakar tersambar petir saat hujan lebat di area pelanggan. Bau gosong menyengat pada PCB.',
                    'photo_proof' => null,
                    'source' => 'QC_RETURN',
                    'return_item_id' => $retItem2->id,
                    'action_taken' => DamagedActionTaken::PENDING,
                ]
            );

            StockMutation::updateOrCreate(
                [
                    'reference_table' => 'damaged_goods',
                    'reference_id' => $dmg1->id,
                    'product_id' => $prodZte->id,
                ],
                [
                    'mutation_type' => MutationType::RETURN_DAMAGED,
                    'qty' => 1.00,
                    'previous_stock' => 28.00,
                    'current_stock' => 28.00, // Stok aktif tidak bertambah!
                    'previous_damaged_stock' => 3.00,
                    'current_damaged_stock' => 4.00, // Saldo rusak bertambah!
                    'system_operator_id' => $admin->id,
                    'person_in_charge' => 'Rian Hidayat (Maintenance)',
                    'reference_ticket_no' => 'TKT-MTN-202611-109',
                    'remarks' => 'Retur dismantle pelanggan rusak kena petir (SN: ZTEGCA881008) dialihkan ke Karantina Rusak',
                    'created_at' => $now->copy()->subHours(6),
                ]
            );
        }

        // Additional Damaged Good Record (Direct report from handling)
        $prodHw = Product::where('sku', 'ONT-HW-HG8546M')->first();
        if ($prodHw) {
            DamagedGood::updateOrCreate(
                ['code' => 'DMG-202611-0002'],
                [
                    'product_id' => $prodHw->id,
                    'serial_number' => '48575443B105',
                    'mac_address' => '00:E0:FC:88:11:05',
                    'qty' => 1.00,
                    'reported_at' => $now->copy()->subDays(3),
                    'reported_by' => 'Budi Santoso (Admin Gudang)',
                    'reporter_type' => RecipientType::KARYAWAN,
                    'damage_category' => DamageCategory::TOTAL_DEAD,
                    'damage_description' => 'Modem mati total saat dilakukan uji redaman dan konfigurasi pre-provisioning di meja staging.',
                    'photo_proof' => null,
                    'source' => 'DIRECT_REPORT',
                    'action_taken' => DamagedActionTaken::RMA_VENDOR,
                    'action_date' => $now->copy()->subDay(),
                    'action_notes' => 'Telah dibuatkan dokumen RMA ke distributor Huawei untuk proses pergantian unit baru (swap unit).',
                    'resolved_by_user_id' => $admin->id,
                ]
            );
        }
    }
}
