<?php

namespace Tests\Feature;

use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\MutationType;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use App\Enums\StockStatus;
use App\Models\Category;
use App\Models\DamagedGood;
use App\Models\Location;
use App\Models\Product;
use App\Models\ReturnTransaction;
use App\Models\StockMutation;
use App\Models\StockOutTransaction;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\InitialTransactionsSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ProductSerialSeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ModelAndEnumTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--database' => 'mariadb']);
        $this->seed([
            UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            LocationSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            ProductSerialSeeder::class,
            InitialTransactionsSeeder::class,
        ]);
    }

    public function test_models_and_enums_are_working_properly(): void
    {
        // Check User
        $admin = User::where('email', 'admin@gayuhmedia.id')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdminGudang());

        // Check Product and relationships
        $product = Product::with(['category', 'unit', 'location', 'serials'])->where('sku', 'ONT-ZTE-F609')->first();
        $this->assertNotNull($product);
        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertInstanceOf(Unit::class, $product->unit);
        $this->assertInstanceOf(Location::class, $product->location);
        $this->assertGreaterThan(0, $product->serials->count());

        // Check Accessors & Enums
        $this->assertEquals(StockStatus::SAFE, $product->stock_status);
        $this->assertEquals('Aman / Tersedia', $product->status_label);
        $this->assertTrue($product->has_damaged);

        // Check Outbound Transaction & Enums
        $out = StockOutTransaction::where('transaction_code', 'OUT-202611-0001')->first();
        $this->assertNotNull($out);
        $this->assertEquals(RecipientType::TEKNISI, $out->recipient_type);
        $this->assertEquals('Ahmad Wahyudi', $out->picked_up_by);

        // Check Return Transaction & QC Enum
        $ret = ReturnTransaction::with('items')->where('transaction_code', 'RET-202611-0002')->first();
        $this->assertNotNull($ret);
        $item = $ret->items->first();
        $this->assertEquals(QcStatus::DAMAGED, $item->qc_status);

        // Check Damaged Good & Enums
        $dmg = DamagedGood::where('code', 'DMG-202611-0001')->first();
        $this->assertNotNull($dmg);
        $this->assertEquals(DamageCategory::LIGHTNING_STRIKE, $dmg->damage_category);
        $this->assertEquals(DamagedActionTaken::PENDING, $dmg->action_taken);

        // Check Stock Mutation Ledger
        $mutation = StockMutation::where('reference_ticket_no', 'SPK-PSB-202611-042')->first();
        $this->assertNotNull($mutation);
        $this->assertInstanceOf(MutationType::class, $mutation->mutation_type);
    }

    public function test_negative_stock_violates_check_constraint(): void
    {
        $this->expectException(QueryException::class);

        $product = Product::first();
        $product->stock = -5.00;
        $product->save();
    }
}
