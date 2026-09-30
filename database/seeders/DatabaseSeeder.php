<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@portalberita.test',
            'password' => 'password',
        ]);

        $this->call([
            CategorySeeder::class,
            ArticleSeeder::class,
        ]);
    }
}