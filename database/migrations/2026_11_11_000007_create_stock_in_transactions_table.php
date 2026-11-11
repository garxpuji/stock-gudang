<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_in_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_name')->nullable();
            $table->string('po_number')->nullable();
            $table->string('vendor_delivery_order_no')->nullable(); // No. Surat Jalan Vendor
            $table->string('courier_or_driver_name')->nullable(); // Kurir / Driver Pengirim
            $table->date('transaction_date');
            $table->foreignId('received_by_user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->unsignedInteger('total_items')->default(0);
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->timestamps();

            $table->index('transaction_date');
            $table->index('po_number');
            $table->index('vendor_delivery_order_no');
        });

        Schema::create('stock_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_transaction_id')->constrained('stock_in_transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('qty', 12, 2);
            $table->decimal('cost_price', 15, 2)->default(0.00);
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->text('serial_numbers')->nullable(); // comma-separated or JSON list of SNs
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_in_items');
        Schema::dropIfExists('stock_in_transactions');
    }
};
