<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_login_berhasil_dengan_kredensial_benar(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->create([
            'email'    => 'user@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'user@test.com',
            'password' => 'salahpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_gagal_dengan_email_tidak_terdaftar(): void
    {
        $response = $this->post('/login', [
            'email'    => 'tidakada@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_mengarahkan_ke_homepage(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_halaman_dilindungi_mengarahkan_ke_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_halaman_admin_mengarahkan_ke_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }
}
