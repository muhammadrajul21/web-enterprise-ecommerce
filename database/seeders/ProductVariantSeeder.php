<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $oversized = DB::table('products')->where('slug', 'oversized-t-shirt')->first();
        $oxford = DB::table('products')->where('slug', 'casual-oxford-shirt')->first();
        $pants = DB::table('products')->where('slug', 'relaxed-pants')->first();
        $jacket = DB::table('products')->where('slug', 'lightweight-jacket')->first();
        $sneakers = DB::table('products')->where('slug', 'everyday-sneakers')->first();
        $tote = DB::table('products')->where('slug', 'minimal-tote-bag')->first();

        DB::table('product_variants')->insert([
            [
                'product_id' => $oversized->id,
                'sku' => 'OTS-BLK-M',
                'color' => 'Black',
                'size' => 'M',
                'price' => 199000,
                'stock' => 20,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $oversized->id,
                'sku' => 'OTS-BLK-L',
                'color' => 'Black',
                'size' => 'L',
                'price' => 199000,
                'stock' => 15,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $oversized->id,
                'sku' => 'OTS-WHT-M',
                'color' => 'White',
                'size' => 'M',
                'price' => 199000,
                'stock' => 12,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $oxford->id,
                'sku' => 'COS-BLU-M',
                'color' => 'Blue',
                'size' => 'M',
                'price' => 279000,
                'stock' => 10,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $oxford->id,
                'sku' => 'COS-WHT-L',
                'color' => 'White',
                'size' => 'L',
                'price' => 279000,
                'stock' => 8,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $pants->id,
                'sku' => 'RP-BLK-M',
                'color' => 'Black',
                'size' => 'M',
                'price' => 299000,
                'stock' => 14,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $pants->id,
                'sku' => 'RP-BLK-L',
                'color' => 'Black',
                'size' => 'L',
                'price' => 299000,
                'stock' => 10,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $jacket->id,
                'sku' => 'LJ-OLV-M',
                'color' => 'Olive',
                'size' => 'M',
                'price' => 399000,
                'stock' => 9,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $sneakers->id,
                'sku' => 'ES-WHT-40',
                'color' => 'White',
                'size' => '40',
                'price' => 549000,
                'stock' => 7,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $sneakers->id,
                'sku' => 'ES-WHT-41',
                'color' => 'White',
                'size' => '41',
                'price' => 549000,
                'stock' => 6,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $tote->id,
                'sku' => 'MTB-BLK-OS',
                'color' => 'Black',
                'size' => 'One Size',
                'price' => 189000,
                'stock' => 18,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}