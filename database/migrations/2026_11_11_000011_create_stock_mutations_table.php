<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('mutation_type'); // INBOUND, OUTBOUND_TEKNISI, OUTBOUND_KARYAWAN, RETURN_GOOD, RETURN_DAMAGED, DISPOSAL, RMA_VENDOR, OPNAME_ADJUSTMENT
            $table->decimal('qty', 12, 2);
            $table->decimal('previous_stock', 12, 2);
            $table->decimal('current_stock', 12, 2);
            $table->decimal('previous_damaged_stock', 12, 2)->default(0.00);
            $table->decimal('current_damaged_stock', 12, 2)->default(0.00);
            $table->foreignId('system_operator_id')->constrained('users');
            $table->string('person_in_charge'); // Nama Teknisi / Karyawan / Kurir fisik
            $table->string('reference_table')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_ticket_no')->nullable(); // No SPK / Tiket / ID Pelanggan
            $table->text('remarks'); // Keterangan lengkap
            $table->timestamps();

            // Strategic indexes for high-speed stock ledger audit
            $table->index(['product_id', 'created_at']);
            $table->index('person_in_charge');
            $table->index('reference_ticket_no');
            $table->index('mutation_type');
            $table->index(['reference_table', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
