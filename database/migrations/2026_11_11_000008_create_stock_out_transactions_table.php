<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_out_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->string('recipient_type'); // TEKNISI, KARYAWAN
            $table->string('picked_up_by'); // Nama Lengkap Teknisi / Karyawan
            $table->string('recipient_department_or_team'); // Tim Lapangan / Divisi
            $table->string('recipient_phone')->nullable();
            $table->string('reference_ticket_no')->nullable(); // No. SPK / Tiket / ID Pelanggan
            $table->string('purpose'); // PSB, Maintenance, Relokasi, Internal NOC, dll
            $table->text('keterangan_pengeluaran')->nullable();
            $table->foreignId('dispatched_by_user_id')->constrained('users');
            $table->dateTime('transaction_date');
            $table->string('status')->default('COMPLETED');
            $table->unsignedInteger('total_items')->default(0);
            $table->timestamps();

            $table->index('picked_up_by');
            $table->index('reference_ticket_no');
            $table->index('transaction_date');
            $table->index(['recipient_type', 'transaction_date']);
        });

        Schema::create('stock_out_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_transaction_id')->constrained('stock_out_transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('qty', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });

        Schema::create('stock_out_item_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_item_id')->constrained('stock_out_items')->cascadeOnDelete();
            $table->foreignId('product_serial_id')->constrained('product_serials')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out_item_serials');
        Schema::dropIfExists('stock_out_items');
        Schema::dropIfExists('stock_out_transactions');
    }
};
