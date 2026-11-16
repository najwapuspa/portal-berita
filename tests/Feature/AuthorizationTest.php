<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_mengakses_dashboard_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_biasa_tidak_dapat_mengakses_dashboard_admin(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_dapat_mengakses_kelola_berita(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/berita');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_dapat_mengakses_kelola_kategori(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/kategori');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_dapat_mengakses_kelola_pengguna(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/pengguna');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_dapat_mengakses_laporan(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/laporan');
        $response->assertStatus(403);
    }

    public function test_admin_dapat_mengakses_kelola_berita(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/berita');
        $response->assertStatus(200);
    }

    public function test_admin_dapat_mengakses_kelola_kategori(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/kategori');
        $response->assertStatus(200);
    }

    public function test_admin_dapat_mengakses_laporan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/laporan');
        $response->assertStatus(200);
    }
}
