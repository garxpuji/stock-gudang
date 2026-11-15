<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--database' => 'mariadb']);
        $this->seed([UserSeeder::class]);
        $this->admin = User::where('email', 'admin@gayuhmedia.id')->firstOrFail();
    }

    public function test_users_index_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Pengguna Sistem');
        $response->assertSee($this->admin->name);
        $response->assertSee('Total Akun');
    }

    public function test_users_create_form_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Pengguna Baru');
        $response->assertSee('Alamat Email (Login)');
    }

    public function test_user_can_be_stored(): void
    {
        $payload = [
            'name' => 'Teknisi Baru',
            'email' => 'teknisi@gayuhmedia.id',
            'phone' => '089912345678',
            'role' => 'admin_gudang',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('users.store'), $payload);
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'teknisi@gayuhmedia.id',
            'name' => 'Teknisi Baru',
            'role' => 'admin_gudang',
            'is_active' => 1,
        ]);
    }

    public function test_user_edit_form_renders_successfully(): void
    {
        $targetUser = User::where('email', 'gudang@gayuhmedia.id')->firstOrFail();

        $response = $this->actingAs($this->admin)->get(route('users.edit', $targetUser));
        $response->assertStatus(200);
        $response->assertSee('Edit Data Pengguna');
        $response->assertSee($targetUser->email);
    }

    public function test_user_can_be_updated(): void
    {
        $targetUser = User::where('email', 'gudang@gayuhmedia.id')->firstOrFail();

        $payload = [
            'name' => 'Aris Munandar Senior',
            'email' => 'aris.senior@gayuhmedia.id',
            'phone' => '081377889911',
            'role' => 'superadmin',
            'password' => '',
            'password_confirmation' => '',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin)->put(route('users.update', $targetUser), $payload);
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertEquals('Aris Munandar Senior', $targetUser->name);
        $this->assertEquals('aris.senior@gayuhmedia.id', $targetUser->email);
        $this->assertEquals('superadmin', $targetUser->role);
    }

    public function test_cannot_delete_logged_in_user(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
    }

    public function test_user_can_be_deleted(): void
    {
        $targetUser = User::where('email', 'gudang@gayuhmedia.id')->firstOrFail();

        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $targetUser));
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $targetUser = User::where('email', 'gudang@gayuhmedia.id')->firstOrFail();
        $targetUser->update(['is_active' => false]);

        $response = $this->post(route('login'), [
            'email' => $targetUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_non_admin_user_cannot_access_user_management(): void
    {
        $staff = User::create([
            'name' => 'Staff Biasa',
            'email' => 'staff@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->get(route('users.index'));
        $response->assertStatus(403);
    }
}
