<?php

namespace App\Actions;

use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\MutationType;
use App\Enums\RecipientType;
use App\Enums\SerialStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\StockMutation;
use App\Services\TransactionCodeGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecordDamagedGoodsAction
{
    /**
     * Record damaged goods directly reported by technician, staff, or warehouse handling
     *
     * @throws InsufficientStockException
     */
    public function execute(array $data, int $userId): DamagedGood
    {
        return DB::transaction(function () use ($data, $userId) {
            $date = ! empty($data['reported_at']) ? Carbon::parse($data['reported_at']) : Carbon::now();
            $code = TransactionCodeGenerator::next('DMG-'.$date->format('Ym').'-', 'damaged_goods', 'code');

            $damageCategory = is_string($data['damage_category'])
                ? DamageCategory::from($data['damage_category'])
                : $data['damage_category'];

            $reporterType = ! empty($data['reporter_type'])
                ? (is_string($data['reporter_type']) ? RecipientType::from($data['reporter_type']) : $data['reporter_type'])
                : RecipientType::TEKNISI;

            $qty = (float) ($data['qty'] ?? 1.00);
            $deductActive = ! empty($data['deduct_from_active_stock']);

            /** @var Product $product */
            $product = Product::where('id', $data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $prevStock = (float) $product->stock;
            $prevDamaged = (float) $product->damaged_stock;

            if ($deductActive) {
                if ($prevStock < $qty) {
                    throw new InsufficientStockException($product->name, $qty, $prevStock);
                }
                $product->stock = $prevStock - $qty;
            }

            $product->damaged_stock = $prevDamaged + $qty;
            $product->save();

            $sn = ! empty($data['serial_number']) ? trim($data['serial_number']) : null;
            $mac = ! empty($data['mac_address']) ? trim($data['mac_address']) : null;

            if ($sn) {
                ProductSerial::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'serial_number' => $sn,
                    ],
                    [
                        'mac_address' => $mac,
                        'status' => SerialStatus::DAMAGED,
                        'notes' => "Laporan barang rusak [{$code}] oleh {$data['reported_by']}: {$data['damage_description']}",
                    ]
                );
            }

            $damagedGood = DamagedGood::create([
                'code' => $code,
                'product_id' => $product->id,
                'serial_number' => $sn,
                'mac_address' => $mac,
                'qty' => $qty,
                'reported_at' => $date,
                'reported_by' => trim($data['reported_by']),
                'reporter_type' => $reporterType,
                'damage_category' => $damageCategory,
                'damage_description' => trim($data['damage_description']),
                'photo_proof' => $data['photo_proof'] ?? null,
                'source' => $data['source'] ?? 'DIRECT_REPORT',
                'action_taken' => DamagedActionTaken::PENDING,
            ]);

            StockMutation::create([
                'product_id' => $product->id,
                'mutation_type' => MutationType::RETURN_DAMAGED,
                'qty' => $qty,
                'previous_stock' => $prevStock,
                'current_stock' => (float) $product->stock,
                'previous_damaged_stock' => $prevDamaged,
                'current_damaged_stock' => (float) $product->damaged_stock,
                'system_operator_id' => $userId,
                'person_in_charge' => $data['reported_by'],
                'reference_table' => 'damaged_goods',
                'reference_id' => $damagedGood->id,
                'reference_ticket_no' => $data['reference_ticket_no'] ?? null,
                'remarks' => "Laporan Barang Rusak [{$code}] dari {$data['reported_by']} ({$damageCategory->value})".($deductActive ? ' - Dialihkan dari stok aktif' : ' - Masuk karantina'),
            ]);

            return $damagedGood->load(['product.unit']);
        }, 3);
    }
}
