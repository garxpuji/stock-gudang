<?php

namespace App\Actions;

use App\Enums\MutationType;
use App\Enums\RecipientType;
use App\Enums\SerialStatus;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidSerialException;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\StockMutation;
use App\Models\StockOutItem;
use App\Models\StockOutItemSerial;
use App\Models\StockOutTransaction;
use App\Services\TransactionCodeGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OutboundStockAction
{
    /**
     * Dispatch goods to Field Technicians or Internal Employees
     *
     * @throws InsufficientStockException
     * @throws InvalidSerialException
     */
    public function execute(array $data, int $userId): StockOutTransaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $date = ! empty($data['transaction_date']) ? Carbon::parse($data['transaction_date']) : Carbon::now();
            $code = TransactionCodeGenerator::next('OUT-'.$date->format('Ym').'-', 'stock_out_transactions');

            $recipientType = is_string($data['recipient_type'])
                ? RecipientType::from($data['recipient_type'])
                : $data['recipient_type'];

            $transaction = StockOutTransaction::create([
                'transaction_code' => $code,
                'recipient_type' => $recipientType,
                'picked_up_by' => trim($data['picked_up_by']),
                'recipient_department_or_team' => trim($data['recipient_department_or_team']),
                'recipient_phone' => $data['recipient_phone'] ?? null,
                'reference_ticket_no' => $data['reference_ticket_no'] ?? null,
                'purpose' => trim($data['purpose']),
                'keterangan_pengeluaran' => $data['keterangan_pengeluaran'] ?? null,
                'dispatched_by_user_id' => $userId,
                'transaction_date' => $date,
                'status' => 'COMPLETED',
                'total_items' => count($data['items']),
            ]);

            $mutationType = ($recipientType === RecipientType::TEKNISI)
                ? MutationType::OUTBOUND_TEKNISI
                : MutationType::OUTBOUND_KARYAWAN;

            foreach ($data['items'] as $item) {
                $qty = (float) $item['qty'];

                /** @var Product $product */
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $previousStock = (float) $product->stock;

                if ($previousStock < $qty) {
                    throw new InsufficientStockException(
                        $product->name,
                        $qty,
                        $previousStock
                    );
                }

                $newStock = $previousStock - $qty;

                $serialIds = array_values(array_unique(array_map('intval', array_filter($item['serial_ids'] ?? []))));
                if ($product->has_serial_number) {
                    if (floor($qty) != $qty) {
                        throw new InvalidSerialException("Kuantitas keluar untuk perangkat berserial '{$product->name}' harus berupa bilangan bulat.");
                    }

                    if (count($serialIds) !== (int) $qty) {
                        throw new InvalidSerialException('Jumlah Serial Number yang dipilih ('.count($serialIds).') harus sesuai dengan kuantitas keluar ('.(int) $qty.") untuk produk '{$product->name}'.");
                    }

                    $serials = ProductSerial::whereIn('id', $serialIds)
                        ->where('product_id', $product->id)
                        ->lockForUpdate()
                        ->get();

                    if ($serials->count() !== count($serialIds)) {
                        throw new InvalidSerialException("Satu atau lebih Serial Number yang dipilih tidak valid untuk produk '{$product->name}'.");
                    }

                    foreach ($serials as $serial) {
                        if ($serial->status !== SerialStatus::AVAILABLE) {
                            throw new InvalidSerialException("Serial Number '{$serial->serial_number}' tidak berstatus AVAILABLE (Status saat ini: {$serial->status->value}).");
                        }
                    }
                } elseif (! empty($serialIds)) {
                    throw new InvalidSerialException("Produk '{$product->name}' tidak menggunakan pelacakan Serial Number.");
                }

                $product->stock = $newStock;
                $product->save();

                $outItem = StockOutItem::create([
                    'stock_out_transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'notes' => $item['notes'] ?? null,
                ]);

                if ($product->has_serial_number && ! empty($serialIds)) {
                    foreach ($serialIds as $serialId) {
                        ProductSerial::where('id', $serialId)
                            ->where('product_id', $product->id)
                            ->where('status', SerialStatus::AVAILABLE)
                            ->update([
                                'status' => SerialStatus::ISSUED,
                                'last_recipient' => $data['picked_up_by']." ({$data['recipient_department_or_team']})",
                                'last_ticket_no' => $data['reference_ticket_no'] ?? null,
                            ]);

                        StockOutItemSerial::create([
                            'stock_out_item_id' => $outItem->id,
                            'product_serial_id' => $serialId,
                        ]);
                    }
                }

                StockMutation::create([
                    'product_id' => $product->id,
                    'mutation_type' => $mutationType,
                    'qty' => -$qty,
                    'previous_stock' => $previousStock,
                    'current_stock' => $newStock,
                    'previous_damaged_stock' => (float) $product->damaged_stock,
                    'current_damaged_stock' => (float) $product->damaged_stock,
                    'system_operator_id' => $userId,
                    'person_in_charge' => $data['picked_up_by']." ({$data['recipient_department_or_team']})",
                    'reference_table' => 'stock_out_transactions',
                    'reference_id' => $transaction->id,
                    'reference_ticket_no' => $data['reference_ticket_no'] ?? null,
                    'remarks' => "Pengeluaran Barang [{$code}] untuk {$data['picked_up_by']} - Keperluan: {$data['purpose']}".(! empty($data['reference_ticket_no']) ? " (Ref: {$data['reference_ticket_no']})" : ''),
                ]);
            }

            return $transaction->load(['items.product.unit', 'items.serials', 'dispatchedBy']);
        }, 3);
    }
}
