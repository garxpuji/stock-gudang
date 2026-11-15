<?php

namespace App\Actions;

use App\Enums\MutationType;
use App\Enums\SerialStatus;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\StockInItem;
use App\Models\StockInTransaction;
use App\Models\StockMutation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InboundStockAction
{
    /**
     * Process inbound stock from supplier / distributor
     */
    public function execute(array $data, int $userId): StockInTransaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $date = ! empty($data['transaction_date']) ? Carbon::parse($data['transaction_date']) : Carbon::now();
            $code = $this->generateTransactionCode($date);

            $totalItems = 0;
            $totalCost = 0.00;

            // 1. Create Transaction Header
            $transaction = StockInTransaction::create([
                'transaction_code' => $code,
                'supplier_id' => $data['supplier_id'] ?? null,
                'supplier_name' => $data['supplier_name'] ?? null,
                'po_number' => $data['po_number'] ?? null,
                'vendor_delivery_order_no' => $data['vendor_delivery_order_no'] ?? null,
                'courier_or_driver_name' => $data['courier_or_driver_name'] ?? null,
                'transaction_date' => $date->toDateString(),
                'received_by_user_id' => $userId,
                'notes' => $data['notes'] ?? null,
                'total_items' => 0,
                'total_cost' => 0.00,
            ]);

            // 2. Process Items with Pessimistic Locking
            foreach ($data['items'] as $item) {
                $qty = (float) $item['qty'];
                $costPrice = isset($item['cost_price']) ? (float) $item['cost_price'] : 0.00;
                $subtotal = $qty * $costPrice;

                /** @var Product $product */
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $previousStock = (float) $product->stock;
                $newStock = $previousStock + $qty;

                // Update product stock balance & cost price
                $product->stock = $newStock;
                if ($costPrice > 0) {
                    $product->cost_price = $costPrice;
                }
                $product->save();

                // Process Serial Numbers / MAC addresses if applicable
                $serialsString = null;
                $snList = [];

                if (! empty($item['serials']) && is_array($item['serials'])) {
                    foreach ($item['serials'] as $snData) {
                        $sn = is_array($snData) ? trim($snData['serial_number'] ?? '') : trim((string) $snData);
                        $mac = is_array($snData) ? trim($snData['mac_address'] ?? '') : null;

                        if (! empty($sn)) {
                            ProductSerial::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'serial_number' => $sn,
                                ],
                                [
                                    'mac_address' => ! empty($mac) ? $mac : null,
                                    'status' => SerialStatus::AVAILABLE,
                                    'notes' => 'Penerimaan masuk via '.$code,
                                ]
                            );
                            $snList[] = $sn;
                        }
                    }
                } elseif (! empty($item['serial_numbers'])) {
                    $rawSerials = is_array($item['serial_numbers'])
                        ? $item['serial_numbers']
                        : preg_split('/[\r\n,;]+/', (string) $item['serial_numbers'], -1, PREG_SPLIT_NO_EMPTY);

                    foreach ($rawSerials as $rawSn) {
                        $sn = trim((string) $rawSn);
                        if (! empty($sn)) {
                            ProductSerial::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'serial_number' => $sn,
                                ],
                                [
                                    'status' => SerialStatus::AVAILABLE,
                                    'notes' => 'Penerimaan masuk via '.$code,
                                ]
                            );
                            $snList[] = $sn;
                        }
                    }
                }

                if (! empty($snList)) {
                    $serialsString = implode(', ', $snList);
                }

                // Create Transaction Item
                StockInItem::create([
                    'stock_in_transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'cost_price' => $costPrice,
                    'subtotal' => $subtotal,
                    'serial_numbers' => $serialsString,
                    'notes' => $item['notes'] ?? null,
                ]);

                // Create Immutable Stock Mutation Ledger Record
                StockMutation::create([
                    'product_id' => $product->id,
                    'mutation_type' => MutationType::INBOUND,
                    'qty' => $qty,
                    'previous_stock' => $previousStock,
                    'current_stock' => $newStock,
                    'previous_damaged_stock' => (float) $product->damaged_stock,
                    'current_damaged_stock' => (float) $product->damaged_stock,
                    'system_operator_id' => $userId,
                    'person_in_charge' => $data['courier_or_driver_name'] ?? ($data['supplier_name'] ?? 'Vendor / Supplier'),
                    'reference_table' => 'stock_in_transactions',
                    'reference_id' => $transaction->id,
                    'reference_ticket_no' => $data['po_number'] ?? ($data['vendor_delivery_order_no'] ?? null),
                    'remarks' => "Barang Masuk Inbound [{$code}] dari ".($data['supplier_name'] ?? 'Vendor').($data['vendor_delivery_order_no'] ? " (SJ: {$data['vendor_delivery_order_no']})" : ''),
                ]);

                $totalItems += 1;
                $totalCost += $subtotal;
            }

            // Update header totals
            $transaction->update([
                'total_items' => $totalItems,
                'total_cost' => $totalCost,
            ]);

            return $transaction->load(['items.product', 'supplier', 'receivedBy']);
        });
    }

    protected function generateTransactionCode(Carbon $date): string
    {
        $prefix = 'IN-'.$date->format('Ym').'-';
        $last = StockInTransaction::where('transaction_code', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        $number = 1;
        if ($last && preg_match('/-(\d+)$/', $last->transaction_code, $matches)) {
            $number = intval($matches[1]) + 1;
        }

        return $prefix.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
