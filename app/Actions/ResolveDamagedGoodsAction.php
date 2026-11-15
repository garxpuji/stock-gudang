<?php

namespace App\Actions;

use App\Enums\DamagedActionTaken;
use App\Enums\MutationType;
use App\Enums\SerialStatus;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\StockMutation;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class ResolveDamagedGoodsAction
{
    /**
     * Resolve quarantined damaged goods: RMA Vendor, Scrapped / Disposal, or Internal Repair
     */
    public function execute(int $damagedGoodId, string|DamagedActionTaken $actionTaken, array $options, int $userId): DamagedGood
    {
        return DB::transaction(function () use ($damagedGoodId, $actionTaken, $options, $userId) {
            $action = is_string($actionTaken) ? DamagedActionTaken::from($actionTaken) : $actionTaken;

            /** @var DamagedGood $damagedGood */
            $damagedGood = DamagedGood::where('id', $damagedGoodId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($damagedGood->action_taken !== DamagedActionTaken::PENDING) {
                throw new DomainException("Barang rusak [{$damagedGood->code}] sudah diselesaikan sebelumnya dengan status {$damagedGood->action_taken->label()}.");
            }

            /** @var Product $product */
            $product = Product::where('id', $damagedGood->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            $qty = (float) $damagedGood->qty;
            $prevStock = (float) $product->stock;
            $prevDamaged = (float) $product->damaged_stock;

            // Always decrement damaged stock balance
            $newDamaged = max(0.00, $prevDamaged - $qty);
            $product->damaged_stock = $newDamaged;

            $now = Carbon::now();
            $lossCost = ! empty($options['loss_cost']) ? (float) $options['loss_cost'] : null;
            $actionNotes = $options['action_notes'] ?? null;

            if ($action === DamagedActionTaken::RMA_VENDOR) {
                // 1. Klaim Garansi / Retur Vendor (RMA)
                $product->save();

                if ($damagedGood->serial_number) {
                    ProductSerial::where('product_id', $product->id)
                        ->where('serial_number', $damagedGood->serial_number)
                        ->update([
                            'status' => SerialStatus::RMA,
                            'notes' => "Klaim Garansi Vendor (RMA) via {$damagedGood->code}: {$actionNotes}",
                        ]);
                }

                StockMutation::create([
                    'product_id' => $product->id,
                    'mutation_type' => MutationType::RMA_VENDOR,
                    'qty' => -$qty,
                    'previous_stock' => $prevStock,
                    'current_stock' => $prevStock,
                    'previous_damaged_stock' => $prevDamaged,
                    'current_damaged_stock' => $newDamaged,
                    'system_operator_id' => $userId,
                    'person_in_charge' => 'Vendor RMA Representative',
                    'reference_table' => 'damaged_goods',
                    'reference_id' => $damagedGood->id,
                    'reference_ticket_no' => $options['rma_number'] ?? null,
                    'remarks' => "Klaim Garansi Vendor (RMA) [{$damagedGood->code}]: ".($actionNotes ?? 'Pengiriman unit rusak ke supplier/distributor'),
                ]);

            } elseif ($action === DamagedActionTaken::SCRAPPED) {
                // 2. Pemusnahan / Write-Off (Scrapped)
                $product->save();

                $calculatedLoss = $lossCost ?? ((float) $product->cost_price * $qty);
                $damagedGood->loss_cost = $calculatedLoss;

                if ($damagedGood->serial_number) {
                    ProductSerial::where('product_id', $product->id)
                        ->where('serial_number', $damagedGood->serial_number)
                        ->update([
                            'status' => SerialStatus::SCRAPPED,
                            'notes' => "Afkir / Dimusnahkan via {$damagedGood->code}. Nilai kerugian aset: Rp ".number_format($calculatedLoss, 0, ',', '.'),
                        ]);
                }

                StockMutation::create([
                    'product_id' => $product->id,
                    'mutation_type' => MutationType::DISPOSAL,
                    'qty' => -$qty,
                    'previous_stock' => $prevStock,
                    'current_stock' => $prevStock,
                    'previous_damaged_stock' => $prevDamaged,
                    'current_damaged_stock' => $newDamaged,
                    'system_operator_id' => $userId,
                    'person_in_charge' => 'Tim Gudang & Audit Aset',
                    'reference_table' => 'damaged_goods',
                    'reference_id' => $damagedGood->id,
                    'reference_ticket_no' => null,
                    'remarks' => "Pemusnahan / Write-Off Aset [{$damagedGood->code}] - Kerugian Aset: Rp ".number_format($calculatedLoss, 0, ',', '.').' ('.($actionNotes ?? 'Barang tidak dapat diperbaiki').')',
                ]);

            } elseif ($action === DamagedActionTaken::REPAIRED_INTERNAL) {
                // 3. Perbaikan Internal (In-House Repair) Berhasil -> Kembalikan ke Stok Aktif
                $newStock = $prevStock + $qty;
                $product->stock = $newStock;
                $product->save();

                if ($damagedGood->serial_number) {
                    ProductSerial::where('product_id', $product->id)
                        ->where('serial_number', $damagedGood->serial_number)
                        ->update([
                            'status' => SerialStatus::AVAILABLE,
                            'notes' => "Hasil perbaikan internal berhasil, siap digunakan kembali (Ref: {$damagedGood->code})",
                        ]);
                }

                StockMutation::create([
                    'product_id' => $product->id,
                    'mutation_type' => MutationType::RETURN_GOOD,
                    'qty' => $qty,
                    'previous_stock' => $prevStock,
                    'current_stock' => $newStock,
                    'previous_damaged_stock' => $prevDamaged,
                    'current_damaged_stock' => $newDamaged,
                    'system_operator_id' => $userId,
                    'person_in_charge' => 'Teknisi Repair Internal',
                    'reference_table' => 'damaged_goods',
                    'reference_id' => $damagedGood->id,
                    'reference_ticket_no' => null,
                    'remarks' => "Perbaikan Internal Berhasil [{$damagedGood->code}] - Unit direkondisi dan dimasukkan kembali ke stok aktif",
                ]);
            }

            // Update Damaged Good Header
            $damagedGood->update([
                'action_taken' => $action,
                'action_date' => $now,
                'action_notes' => $actionNotes,
                'loss_cost' => $damagedGood->loss_cost,
                'resolved_by_user_id' => $userId,
            ]);

            return $damagedGood->load(['product.unit', 'resolvedBy']);
        });
    }
}
