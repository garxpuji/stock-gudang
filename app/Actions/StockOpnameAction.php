<?php

namespace App\Actions;

use App\Enums\MutationType;
use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;

class StockOpnameAction
{
    /**
     * Reconcile physical warehouse stock with system inventory (Stock Opname)
     */
    public function execute(array $data, int $userId): Product
    {
        return DB::transaction(function () use ($data, $userId) {
            /** @var Product $product */
            $product = Product::where('id', $data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $prevStock = (float) $product->stock;
            $prevDamaged = (float) $product->damaged_stock;

            $actualStock = (float) $data['actual_stock'];
            $actualDamaged = isset($data['actual_damaged_stock'])
                ? (float) $data['actual_damaged_stock']
                : $prevDamaged;

            $diffStock = $actualStock - $prevStock;
            $diffDamaged = $actualDamaged - $prevDamaged;

            $product->stock = $actualStock;
            $product->damaged_stock = $actualDamaged;
            $product->save();

            $personInCharge = ! empty($data['person_in_charge'])
                ? trim($data['person_in_charge'])
                : 'Auditor Gudang';

            $signStock = $diffStock >= 0 ? "+{$diffStock}" : (string) $diffStock;
            $signDamaged = $diffDamaged >= 0 ? "+{$diffDamaged}" : (string) $diffDamaged;

            StockMutation::create([
                'product_id' => $product->id,
                'mutation_type' => MutationType::OPNAME_ADJUSTMENT,
                'qty' => $diffStock,
                'previous_stock' => $prevStock,
                'current_stock' => $actualStock,
                'previous_damaged_stock' => $prevDamaged,
                'current_damaged_stock' => $actualDamaged,
                'system_operator_id' => $userId,
                'person_in_charge' => $personInCharge,
                'reference_table' => 'products',
                'reference_id' => $product->id,
                'reference_ticket_no' => $data['reference_doc_no'] ?? null,
                'remarks' => "Penyesuaian Stok Opname: Stok aktif ({$signStock}), Rusak ({$signDamaged}). Catatan: ".($data['notes'] ?? 'Pencocokan fisik berkala'),
            ]);

            return $product->load(['category', 'unit', 'location']);
        });
    }
}
