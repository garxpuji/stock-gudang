<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            
            // Stock balances & threshold
            $table->decimal('stock', 12, 2)->default(0);
            $table->decimal('damaged_stock', 12, 2)->default(0);
            $table->decimal('min_stock', 12, 2)->default(5.00);
            $table->decimal('cost_price', 15, 2)->default(0.00);
            
            // Device characteristics
            $table->boolean('has_serial_number')->default(false);
            $table->text('specification')->nullable();
            $table->text('stock_notes')->nullable();
            $table->string('image')->nullable();
            
            $table->timestamps();

            // Strategic index
            $table->index(['name']);
            $table->index(['category_id', 'stock']);
        });

        // Enforce non-negative stock at database level (MariaDB check constraint)
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_stock CHECK (stock >= 0)');
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_damaged_stock CHECK (damaged_stock >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
