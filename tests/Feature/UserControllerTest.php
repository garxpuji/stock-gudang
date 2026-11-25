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
        $this->admin = User::where('email', 'tegar@gayuhmedia.id')->firstOrFail();
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
        $response->assertSee('Konfirmasi Kata Sandi Superadmin Anda');
    }

    public function test_user_can_be_stored(): void
    {
        $payload = [
            'name' => 'Teknisi Baru',
            'email' => 'teknisi@gayuhmedia.id',
            'phone' => '089912345678',
            'role' => 'admin_gudang',
            'password' => 'Secret12345',
            'password_confirmation' => 'Secret12345',
            'is_active' => '1',
            'current_password' => 'password',
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
            'current_password' => 'password',
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
        $adminGudang = User::where('email', 'admin@gayuhmedia.id')->firstOrFail();

        $this->actingAs($adminGudang)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($adminGudang)->post(route('users.store'), [])->assertStatus(403);

        $staff = User::create([
            'name' => 'Staff Biasa',
            'email' => 'staff@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => Hash::make('Password1234'),
            'is_active' => true,
        ]);

        $this->actingAs($staff)->get(route('users.index'))->assertStatus(403);
    }

    public function test_user_password_enforces_complexity_and_length_bounds(): void
    {
        // Terlalu pendek (< 10)
        $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Staff Password Pendek',
            'email' => 'short@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => 'Short123',
            'password_confirmation' => 'Short123',
            'is_active' => '1',
            'current_password' => 'password',
        ])->assertSessionHasErrors('password');

        // Tanpa angka
        $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Staff Tanpa Angka',
            'email' => 'no-digit@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => 'OnlyLettersPass',
            'password_confirmation' => 'OnlyLettersPass',
            'is_active' => '1',
            'current_password' => 'password',
        ])->assertSessionHasErrors('password');

        // Terlalu panjang (> 128)
        $tooLong = str_repeat('a1', 65);
        $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Staff Terlalu Panjang',
            'email' => 'long@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => $tooLong,
            'password_confirmation' => $tooLong,
            'is_active' => '1',
            'current_password' => 'password',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'short@gayuhmedia.id']);
        $this->assertDatabaseMissing('users', ['email' => 'no-digit@gayuhmedia.id']);
        $this->assertDatabaseMissing('users', ['email' => 'long@gayuhmedia.id']);
    }

    public function test_user_management_requires_valid_current_password_for_sensitive_changes(): void
    {
        $responseStore = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Tanpa Verifikasi',
            'email' => 'noverify@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => 'Secret12345',
            'password_confirmation' => 'Secret12345',
            'is_active' => '1',
            'current_password' => 'wrong-password',
        ]);

        $responseStore->assertSessionHasErrors('current_password');
        $this->assertDatabaseMissing('users', ['email' => 'noverify@gayuhmedia.id']);

        $targetUser = User::where('email', 'gudang@gayuhmedia.id')->firstOrFail();

        $responseUpdate = $this->actingAs($this->admin)->put(route('users.update', $targetUser), [
            'name' => $targetUser->name,
            'email' => $targetUser->email,
            'role' => 'superadmin',
            'is_active' => '1',
            'current_password' => 'wrong-password',
        ]);

        $responseUpdate->assertSessionHasErrors('current_password');
        $this->assertEquals('admin_gudang', $targetUser->fresh()->role);
    }

    public function test_superadmin_can_create_staff_gudang_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Staf Loket',
            'email' => 'staf.loket@gayuhmedia.id',
            'role' => 'staff_gudang',
            'password' => 'Secret12345',
            'password_confirmation' => 'Secret12345',
            'is_active' => '1',
            'current_password' => 'password',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'staf.loket@gayuhmedia.id',
            'role' => 'staff_gudang',
        ]);
    }
}
