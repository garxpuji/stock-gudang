<?php

namespace App\Services;

use App\Models\TransactionSequence;
use Illuminate\Support\Facades\DB;

class TransactionCodeGenerator
{
    /**
     * Generate the next atomic sequential transaction code under the current DB transaction.
     */
    public static function next(string $prefix, string $table, string $column = 'transaction_code'): string
    {
        $lastCode = DB::table($table)
            ->where($column, 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->lockForUpdate()
            ->value($column);

        $existingMax = 0;
        if (is_string($lastCode) && preg_match('/-(\d+)$/', $lastCode, $matches)) {
            $existingMax = (int) $matches[1];
        }

        // Insert row atomically if missing so concurrent first-of-month calls serialize on the PK lock.
        DB::table('transaction_sequences')->insertOrIgnore([
            'prefix' => $prefix,
            'last_number' => $existingMax,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = TransactionSequence::where('prefix', $prefix)
            ->lockForUpdate()
            ->firstOrFail();

        $nextNumber = max((int) $sequence->last_number, $existingMax) + 1;
        $sequence->update(['last_number' => $nextNumber]);

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
