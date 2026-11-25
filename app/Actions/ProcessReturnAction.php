<?php

namespace App\Actions;

use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\MutationType;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use App\Enums\SerialStatus;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ReturnTransaction;
use App\Models\ReturnTransactionItem;
use App\Models\StockMutation;
use App\Services\TransactionCodeGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessReturnAction
{
    /**
     * Process return of materials/tools/devices with Quality Control (QC) branching
     */
    public function execute(array $data, int $userId): ReturnTransaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $date = ! empty($data['transaction_date']) ? Carbon::parse($data['transaction_date']) : Carbon::now();
            $code = TransactionCodeGenerator::next('RET-'.$date->format('Ym').'-', 'return_transactions');

            $recipientType = ! empty($data['recipient_type'])
                ? (is_string($data['recipient_type']) ? RecipientType::from($data['recipient_type']) : $data['recipient_type'])
                : RecipientType::TEKNISI;

            $transaction = ReturnTransaction::create([
                'transaction_code' => $code,
                'returned_by' => trim($data['returned_by']),
                'returned_by_contact' => $data['returned_by_contact'] ?? null,
                'recipient_type' => $recipientType,
                'original_stock_out_id' => $data['original_stock_out_id'] ?? null,
                'reference_ticket_no' => $data['reference_ticket_no'] ?? null,
                'return_category' => $data['return_category'] ?? 'Sisa Material Proyek',
                'keterangan_pengembalian' => $data['keterangan_pengembalian'] ?? null,
                'received_by_user_id' => $userId,
                'transaction_date' => $date,
                'total_items' => count($data['items']),
            ]);

            foreach ($data['items'] as $item) {
                $qty = (float) $item['qty'];
                $qcStatus = is_string($item['qc_status'])
                    ? QcStatus::from($item['qc_status'])
                    : $item['qc_status'];

                /** @var Product $product */
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $previousStock = (float) $product->stock;
                $previousDamagedStock = (float) $product->damaged_stock;

                $sn = ! empty($item['serial_number']) ? trim($item['serial_number']) : null;
                $mac = ! empty($item['mac_address']) ? trim($item['mac_address']) : null;

                if ($qcStatus === QcStatus::GOOD) {
                    // Restock item to active inventory when QC passes.
                    $newStock = $previousStock + $qty;
                    $product->stock = $newStock;
                    $product->save();

                    $actionTaken = 'RESTOCKED';

                    if ($sn) {
                        ProductSerial::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'serial_number' => $sn,
                            ],
                            [
                                'mac_address' => $mac,
                                'status' => SerialStatus::AVAILABLE,
                                'last_recipient' => null,
                                'notes' => "Dikembalikan kondisi BAIK oleh {$data['returned_by']} via {$code}",
                            ]
                        );
                    }

                    $returnItem = ReturnTransactionItem::create([
                        'return_transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'qc_status' => $qcStatus,
                        'serial_number' => $sn,
                        'mac_address' => $mac,
                        'qc_notes' => $item['qc_notes'] ?? 'Kondisi fisik mulus dan fungsi normal lolos uji QC gudang.',
                        'action_taken' => $actionTaken,
                    ]);

                    StockMutation::create([
                        'product_id' => $product->id,
                        'mutation_type' => MutationType::RETURN_GOOD,
                        'qty' => $qty,
                        'previous_stock' => $previousStock,
                        'current_stock' => $newStock,
                        'previous_damaged_stock' => $previousDamagedStock,
                        'current_damaged_stock' => $previousDamagedStock,
                        'system_operator_id' => $userId,
                        'person_in_charge' => $data['returned_by'],
                        'reference_table' => 'return_transactions',
                        'reference_id' => $transaction->id,
                        'reference_ticket_no' => $data['reference_ticket_no'] ?? null,
                        'remarks' => "Retur Barang Kondisi BAIK [{$code}] dari {$data['returned_by']} (Lolos QC, siap dialokasikan kembali)",
                    ]);
                } else {
                    // Quarantine damaged item without increasing active stock.
                    $newDamagedStock = $previousDamagedStock + $qty;
                    $product->damaged_stock = $newDamagedStock;
                    $product->save();

                    $actionTaken = 'QUARANTINED_DAMAGED';

                    if ($sn) {
                        ProductSerial::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'serial_number' => $sn,
                            ],
                            [
                                'mac_address' => $mac,
                                'status' => SerialStatus::DAMAGED,
                                'notes' => "Karantina barang rusak dari {$data['returned_by']}",
                            ]
                        );
                    }

                    $returnItem = ReturnTransactionItem::create([
                        'return_transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'qc_status' => $qcStatus,
                        'serial_number' => $sn,
                        'mac_address' => $mac,
                        'qc_notes' => $item['qc_notes'] ?? 'Ditemukan cacat/rusak saat inspeksi QC barang kembali.',
                        'action_taken' => $actionTaken,
                    ]);

                    $damageCategory = ! empty($item['damage_category'])
                        ? (is_string($item['damage_category']) ? DamageCategory::from($item['damage_category']) : $item['damage_category'])
                        : DamageCategory::PHYSICAL_DEFECT;

                    $dmgCode = TransactionCodeGenerator::next('DMG-'.$date->format('Ym').'-', 'damaged_goods', 'code');

                    DamagedGood::create([
                        'code' => $dmgCode,
                        'product_id' => $product->id,
                        'serial_number' => $sn,
                        'mac_address' => $mac,
                        'qty' => $qty,
                        'reported_at' => $date,
                        'reported_by' => $data['returned_by'],
                        'reporter_type' => $recipientType,
                        'damage_category' => $damageCategory,
                        'damage_description' => $item['damage_description'] ?? ($item['qc_notes'] ?? 'Rusak saat pengembalian barang teknisi'),
                        'photo_proof' => $item['photo_proof'] ?? null,
                        'source' => 'QC_RETURN',
                        'return_item_id' => $returnItem->id,
                        'action_taken' => DamagedActionTaken::PENDING,
                    ]);

                    StockMutation::create([
                        'product_id' => $product->id,
                        'mutation_type' => MutationType::RETURN_DAMAGED,
                        'qty' => $qty,
                        'previous_stock' => $previousStock,
                        'current_stock' => $previousStock,
                        'previous_damaged_stock' => $previousDamagedStock,
                        'current_damaged_stock' => $newDamagedStock,
                        'system_operator_id' => $userId,
                        'person_in_charge' => $data['returned_by'],
                        'reference_table' => 'return_transactions',
                        'reference_id' => $transaction->id,
                        'reference_ticket_no' => $data['reference_ticket_no'] ?? null,
                        'remarks' => "Retur Barang RUSAK [{$code}] dari {$data['returned_by']} dialihkan ke Area Karantina Rusak ({$damageCategory->value})",
                    ]);
                }
            }

            return $transaction->load(['items.product.unit', 'receivedBy', 'originalStockOut']);
        }, 3);
    }
}
