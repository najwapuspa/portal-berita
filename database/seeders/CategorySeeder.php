<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Politik', 'description' => 'Berita seputar politik dan pemerintahan'],
            ['name' => 'Ekonomi', 'description' => 'Berita ekonomi, bisnis, dan keuangan'],
            ['name' => 'Olahraga', 'description' => 'Berita olahraga terkini'],
            ['name' => 'Teknologi', 'description' => 'Berita teknologi dan gadget'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}