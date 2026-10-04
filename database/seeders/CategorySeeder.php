<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'T-Shirts',
                'slug' => 't-shirts',
                'description' => 'Kaos untuk kebutuhan casual sehari-hari.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shirts',
                'slug' => 'shirts',
                'description' => 'Kemeja casual dan lifestyle.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pants',
                'slug' => 'pants',
                'description' => 'Celana casual untuk penggunaan sehari-hari.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outerwear',
                'slug' => 'outerwear',
                'description' => 'Jaket dan outer lifestyle.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Footwear',
                'slug' => 'footwear',
                'description' => 'Sepatu casual dan lifestyle.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bags',
                'slug' => 'bags',
                'description' => 'Tas untuk kebutuhan sehari-hari.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Aksesori fashion dan lifestyle.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
