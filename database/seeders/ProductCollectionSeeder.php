<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCollectionSeeder extends Seeder
{
    public function run(): void
    {
        $newArrivals = DB::table('collections')->where('slug', 'new-arrivals')->first();
        $essentials = DB::table('collections')->where('slug', 'essentials')->first();
        $bestSeller = DB::table('collections')->where('slug', 'best-seller')->first();
        $dailyWear = DB::table('collections')->where('slug', 'daily-wear')->first();

        $oversized = DB::table('products')->where('slug', 'oversized-t-shirt')->first();
        $oxford = DB::table('products')->where('slug', 'casual-oxford-shirt')->first();
        $pants = DB::table('products')->where('slug', 'relaxed-pants')->first();
        $jacket = DB::table('products')->where('slug', 'lightweight-jacket')->first();
        $sneakers = DB::table('products')->where('slug', 'everyday-sneakers')->first();
        $tote = DB::table('products')->where('slug', 'minimal-tote-bag')->first();

        DB::table('product_collections')->insert([
            [
                'product_id' => $oversized->id,
                'collection_id' => $newArrivals->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $oversized->id,
                'collection_id' => $essentials->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $oversized->id,
                'collection_id' => $bestSeller->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $oxford->id,
                'collection_id' => $dailyWear->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $pants->id,
                'collection_id' => $essentials->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $jacket->id,
                'collection_id' => $newArrivals->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $sneakers->id,
                'collection_id' => $bestSeller->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $tote->id,
                'collection_id' => $dailyWear->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}