<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('collections')->insert([
            [
                'name' => 'New Arrivals',
                'slug' => 'new-arrivals',
                'description' => 'Produk terbaru.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Essentials',
                'slug' => 'essentials',
                'description' => 'Produk essential untuk kebutuhan sehari-hari.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Best Seller',
                'slug' => 'best-seller',
                'description' => 'Produk pilihan yang paling banyak diminati.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Daily Wear',
                'slug' => 'daily-wear',
                'description' => 'Produk lifestyle untuk aktivitas sehari-hari.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}