<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('serial_number');
            $table->string('mac_address')->nullable();
            $table->string('status')->default('AVAILABLE'); // AVAILABLE, ISSUED, DAMAGED, RMA, SCRAPPED
            $table->string('last_recipient')->nullable();
            $table->string('last_ticket_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'serial_number'], 'uq_prod_serial');
            $table->index('serial_number');
            $table->index('mac_address');
            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_serials');
    }
};
