<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_goods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('serial_number')->nullable();
            $table->string('mac_address')->nullable();
            $table->decimal('qty', 12, 2)->default(1.00);
            $table->dateTime('reported_at');
            $table->string('reported_by'); // Nama Teknisi / Karyawan
            $table->string('reporter_type')->nullable(); // TEKNISI / KARYAWAN
            $table->string('damage_category'); // Tersambar Petir / Overvoltage, Port LAN/Fiber Rusak, etc.
            $table->text('damage_description'); // Keterangan detail kerusakan teknis
            $table->string('photo_proof')->nullable(); // Foto kondisi fisik
            $table->string('source')->default('DIRECT_REPORT'); // QC_RETURN, DIRECT_REPORT, WAREHOUSE_HANDLING
            $table->foreignId('return_item_id')->nullable()->constrained('return_transaction_items')->nullOnDelete();
            
            // Follow-up actions
            $table->string('action_taken')->default('PENDING'); // PENDING, RMA_VENDOR, SCRAPPED, REPAIRED_INTERNAL
            $table->dateTime('action_date')->nullable();
            $table->text('action_notes')->nullable();
            $table->decimal('loss_cost', 15, 2)->nullable();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('reported_by');
            $table->index('serial_number');
            $table->index('action_taken');
            $table->index(['product_id', 'action_taken']);
            $table->index('reported_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_goods');
    }
};
