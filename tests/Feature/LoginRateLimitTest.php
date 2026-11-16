<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRateLimitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login normal tetap berfungsi dengan kredensial benar.
     */
    public function test_login_normal_tetap_berfungsi(): void
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

    /**
     * Login dengan password salah 5 kali berturut-turut harus terkena rate limit.
     * Setelah 5 percobaan gagal, percobaan ke-6 harus menghasilkan 429 Too Many Requests.
     */
    public function test_login_terkena_rate_limit_setelah_5_percobaan_gagal(): void
    {
        User::factory()->create([
            'email'    => 'ratelimit@test.com',
            'password' => bcrypt('passwordbenar'),
        ]);

        // 5 percobaan gagal (batas = 5,1 = 5 per menit)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email'    => 'ratelimit@test.com',
                'password' => 'passwordsalah',
            ]);
            // 5 percobaan pertama masih boleh diproses (302 redirect back dengan error)
            $this->assertContains($response->getStatusCode(), [200, 302]);
        }

        // Percobaan ke-6 harus tertolak oleh throttle
        $response = $this->post('/login', [
            'email'    => 'ratelimit@test.com',
            'password' => 'passwordsalah',
        ]);

        $response->assertStatus(429);
    }

    /**
     * Halaman login GET tidak terkena rate limiting (hanya POST).
     */
    public function test_halaman_login_get_tidak_terkena_rate_limit(): void
    {
        // Akses GET /login berkali-kali tidak boleh diblokir
        for ($i = 0; $i < 10; $i++) {
            $response = $this->get('/login');
            $response->assertStatus(200);
        }
    }

    /**
     * Logout tetap berfungsi setelah login berhasil.
     */
    public function test_logout_berfungsi_setelah_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'logout@test.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
