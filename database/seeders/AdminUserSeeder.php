<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    /**
     * Seeder mandiri untuk membuat akun administrator utama tanpa menyentuh artikel atau kategori.
     * Tidak menggunakan password bawaan statis untuk mencegah celah keamanan kredensial default.
     *
     * Cara penggunaan aman di hosting:
     * ADMIN_INITIAL_PASSWORD="SandiKuatPilihanAnda123!" php artisan db:seed --class=AdminUserSeeder --force
     */
    public function run(): void
    {
        $password = env('ADMIN_INITIAL_PASSWORD') ?: env('ADMIN_PASSWORD');

        if (empty($password)) {
            if ($this->command) {
                $this->command->error(' [!] GAGAL: Variabel environment ADMIN_INITIAL_PASSWORD belum ditentukan.');
                $this->command->line('     Untuk keamanan sistem, seeder ini TIDAK memiliki password bawaan statis.');
                $this->command->line('     Silakan gunakan perintah interaktif: php artisan make:admin');
                $this->command->line('     Atau tentukan sandi: ADMIN_INITIAL_PASSWORD="Sandi123!" php artisan db:seed --class=AdminUserSeeder --force');
            }
            return;
        }

        $email = env('ADMIN_INITIAL_EMAIL', 'admin@newsxpaper.com');
        $name  = env('ADMIN_INITIAL_NAME', 'Admin NewsXPaper');

        $user = User::where('email', $email)->first() ?? new User(['email' => $email]);
        $user->name = $name;
        $user->password = Hash::make($password);

        if (Schema::hasColumn('users', 'role')) {
            $user->role = 'admin';
        }
        if (Schema::hasColumn('users', 'is_admin')) {
            $user->is_admin = true;
        }

        $user->save();

        if ($this->command) {
            $this->command->info(" [OK] Akun administrator '{$email}' berhasil diproses secara aman.");
        }
    }
}
