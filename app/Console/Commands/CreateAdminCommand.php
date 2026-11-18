<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'make:admin
                            {--name= : Nama lengkap administrator}
                            {--email= : Alamat email administrator}
                            {--password= : Kata sandi administrator}';

    protected $description = 'Membuat atau memperbarui akun administrator NewsXPaper dengan aman';

    public function handle(): int
    {
        $this->info('=== NewsXPaper Admin Account Creator ===');

        $name = $this->option('name') ?: $this->ask('Masukkan Nama Admin', 'Admin NewsXPaper');
        $email = $this->option('email') ?: $this->ask('Masukkan Email Admin', 'admin@newsxpaper.com');
        $password = $this->option('password') ?: $this->secret('Masukkan Kata Sandi Admin (min. 8 karakter)');

        $validator = Validator::make([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ], [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error("  [!] {$error}");
            }
            return self::FAILURE;
        }

        $user = User::where('email', $email)->first() ?? new User(['email' => $email]);
        $user->name = $name;
        $user->password = Hash::make($password);

        if (Schema::hasColumn('users', 'role')) {
            $user->role = 'admin';
        }
        if (Schema::hasColumn('users', 'is_admin')) {
            $user->is_admin = true;
        }

        $isNew = !$user->exists;
        $user->save();

        $action = $isNew ? 'DIBUAT' : 'DIPERBARUI';
        $this->info(" [OK] Akun administrator '{$email}' berhasil {$action} (ID: {$user->id}).");

        $roleVal = Schema::hasColumn('users', 'role') ? ($user->role ?? 'N/A') : 'N/A';
        $adminVal = Schema::hasColumn('users', 'is_admin') ? ($user->is_admin ? 'true (1)' : 'false (0)') : 'N/A';

        $this->table(
            ['ID', 'Nama', 'Email', 'Role', 'is_admin'],
            [[$user->id, $user->name, $user->email, $roleVal, $adminVal]]
        );

        return self::SUCCESS;
    }
}
