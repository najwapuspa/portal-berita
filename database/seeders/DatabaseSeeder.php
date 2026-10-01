<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::firstOrCreate(
            ['email' => 'admin@newsxpaper.com'],
            [
                'name'     => 'Admin NewsXpaper',
                'password' => Hash::make('Admin123!'),
                'role'     => 'admin',
                'is_admin' => true,
            ]
        );

        // User demo
        User::firstOrCreate(
            ['email' => 'user@newsxpaper.com'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('User123!'),
                'role'     => 'user',
                'is_admin' => false,
            ]
        );

        // User tambahan
        $extras = [
            ['name' => 'Siti Rahayu',   'email' => 'siti@newsxpaper.com',   'password' => Hash::make('password')],
            ['name' => 'Ahmad Fauzi',   'email' => 'ahmad@newsxpaper.com',   'password' => Hash::make('password')],
            ['name' => 'Dewi Lestari',  'email' => 'dewi@newsxpaper.com',   'password' => Hash::make('password')],
        ];
        foreach ($extras as $u) {
            User::firstOrCreate(['email' => $u['email']], array_merge($u, ['role' => 'user', 'is_admin' => false]));
        }

        $this->call([
            CategorySeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
