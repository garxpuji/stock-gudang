<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--database' => 'mariadb']);
        $this->seed([\Database\Seeders\UserSeeder::class]);
        $this->admin = User::where('email', 'admin@gayuhmedia.id')->firstOrFail();
    }

    public function test_category_crud_operations(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('categories.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Kategori Barang');

        // 2. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('categories.store'), [
            'name' => 'Kabel Drop Core FO',
            'code' => 'CABLE-FO',
            'description' => 'Kabel fiber optic drop core 1 core & 2 core',
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('categories', ['code' => 'CABLE-FO', 'name' => 'Kabel Drop Core FO']);

        $category = Category::where('code', 'CABLE-FO')->firstOrFail();

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('categories.update', $category), [
            'name' => 'Kabel Drop Core Fiber Optic',
            'code' => 'CABLE-FO-UPDATED',
            'description' => 'Updated description',
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('categories', ['code' => 'CABLE-FO-UPDATED']);

        // 4. Destroy
        $deleteResponse = $this->actingAs($this->admin)->delete(route('categories.destroy', $category));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_unit_crud_operations(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('units.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Satuan Ukur');

        // 2. Create Page
        $createPageResponse = $this->actingAs($this->admin)->get(route('units.create'));
        $createPageResponse->assertStatus(200);
        $createPageResponse->assertSee('Tambah Satuan Ukur');

        // 3. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('units.store'), [
            'name' => 'Pieces Unit',
            'abbreviation' => 'Pcs',
        ]);
        $storeResponse->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('units', ['abbreviation' => 'Pcs', 'name' => 'Pieces Unit']);

        $unit = Unit::where('abbreviation', 'Pcs')->firstOrFail();

        // 4. Edit Page
        $editPageResponse = $this->actingAs($this->admin)->get(route('units.edit', $unit));
        $editPageResponse->assertStatus(200);
        $editPageResponse->assertSee('Ubah Satuan');

        // 5. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('units.update', $unit), [
            'name' => 'Pieces Satuan',
            'abbreviation' => 'Pcs-New',
        ]);
        $updateResponse->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('units', ['abbreviation' => 'Pcs-New']);

        // 6. Destroy
        $deleteResponse = $this->actingAs($this->admin)->delete(route('units.destroy', $unit));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    public function test_location_crud_operations(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('locations.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Lokasi Rak');

        // 2. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('locations.store'), [
            'name' => 'Rak A1 Perangkat',
            'code' => 'RAK-A1',
            'description' => 'Lantai 1 dekat loket',
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('locations', ['code' => 'RAK-A1']);

        $location = Location::where('code', 'RAK-A1')->firstOrFail();

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('locations.update', $location), [
            'name' => 'Rak A1 Lantai 2',
            'code' => 'RAK-A1-L2',
            'description' => 'Dipindah ke lantai 2',
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('locations', ['code' => 'RAK-A1-L2']);

        // 4. Destroy
        $deleteResponse = $this->actingAs($this->admin)->delete(route('locations.destroy', $location));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_supplier_crud_operations(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('suppliers.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Supplier');

        // 2. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('suppliers.store'), [
            'name' => 'PT Fiber Media Distributor',
            'code' => 'FMD-01',
            'contact_name' => 'Pak Budi Vendor',
            'phone' => '081233445566',
            'email' => 'sales@fibermedia.co.id',
            'address' => 'Kawasan Industri Cikarang',
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['name' => 'PT Fiber Media Distributor']);

        $supplier = Supplier::where('code', 'FMD-01')->firstOrFail();

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('suppliers.update', $supplier), [
            'name' => 'PT Fiber Media Internasional',
            'code' => 'FMD-INTL',
            'contact_name' => 'Pak Budi Director',
            'phone' => '081233445566',
            'email' => 'director@fibermedia.co.id',
            'address' => 'Jakarta Selatan',
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['code' => 'FMD-INTL']);

        // 4. Destroy
        $deleteResponse = $this->actingAs($this->admin)->delete(route('suppliers.destroy', $supplier));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_product_can_be_deleted_if_no_transactions(): void
    {
        $category = Category::create(['name' => 'Test Cat', 'code' => 'TCAT']);
        $unit = Unit::create(['name' => 'Test Unit', 'abbreviation' => 'TU']);

        $product = Product::create([
            'sku' => 'TEST-DEL-001',
            'name' => 'Item to be deleted',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'stock' => 0,
            'damaged_stock' => 0,
            'min_stock' => 5,
            'cost_price' => 100000,
            'has_serial_number' => false,
        ]);

        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $response = $this->actingAs($this->admin)->delete(route('products.destroy', $product));
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
