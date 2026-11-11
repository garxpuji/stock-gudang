<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->string('returned_by'); // Nama Lengkap Teknisi / Karyawan
            $table->string('returned_by_contact')->nullable(); // No Telp / Tim
            $table->string('recipient_type')->default('TEKNISI'); // TEKNISI, KARYAWAN
            $table->foreignId('original_stock_out_id')->nullable()->constrained('stock_out_transactions')->nullOnDelete();
            $table->string('reference_ticket_no')->nullable(); // No SPK / Tiket / ID Pelanggan
            $table->string('return_category')->default('Sisa Material Proyek'); // Sisa Material, Dismantle Pelanggan, Pengembalian Alat
            $table->text('keterangan_pengembalian')->nullable();
            $table->foreignId('received_by_user_id')->constrained('users');
            $table->dateTime('transaction_date');
            $table->unsignedInteger('total_items')->default(0);
            $table->timestamps();

            $table->index('returned_by');
            $table->index('reference_ticket_no');
            $table->index('transaction_date');
        });

        Schema::create('return_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_transaction_id')->constrained('return_transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('qty', 12, 2);
            $table->string('qc_status')->default('GOOD'); // GOOD, DAMAGED
            $table->string('serial_number')->nullable();
            $table->string('mac_address')->nullable();
            $table->text('qc_notes')->nullable(); // Detail hasil inspeksi redaman/fisik/port
            $table->string('action_taken')->default('RESTOCKED'); // RESTOCKED, QUARANTINED_DAMAGED
            $table->timestamps();

            $table->index(['product_id', 'qc_status']);
            $table->index('serial_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_transaction_items');
        Schema::dropIfExists('return_transactions');
    }
};
