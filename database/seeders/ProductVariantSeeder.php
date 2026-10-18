<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $oversized = DB::table('products')
            ->where('slug', 'oversized-t-shirt')
            ->first();

        $oxford = DB::table('products')
            ->where('slug', 'casual-oxford-shirt')
            ->first();

        $pants = DB::table('products')
            ->where('slug', 'relaxed-pants')
            ->first();

        $jacket = DB::table('products')
            ->where('slug', 'lightweight-jacket')
            ->first();

        $sneakers = DB::table('products')
            ->where('slug', 'everyday-sneakers')
            ->first();

        $tote = DB::table('products')
            ->where('slug', 'minimal-tote-bag')
            ->first();

        $cap = DB::table('products')
            ->where('slug', 'classic-baseball-cap')
            ->first();

        $kidsTshirt = DB::table('products')
            ->where('slug', 'kids-graphic-t-shirt')
            ->first();


        DB::table('product_variants')->insert([
            [
                'product_id' => $oversized->id,
                'sku' => 'OTS-BLK-M',
                'color' => 'Black',
                'size' => 'M',
                'price' => 199000,
                'stock' => 30,
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
                'stock' => 25,
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
                'stock' => 20,
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
                'stock' => 20,
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
                'stock' => 18,
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
                'stock' => 22,
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
                'stock' => 18,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $jacket->id,
                'sku' => 'LJ-BLK-M',
                'color' => 'Black',
                'size' => 'M',
                'price' => 399000,
                'stock' => 15,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $jacket->id,
                'sku' => 'LJ-BLK-L',
                'color' => 'Black',
                'size' => 'L',
                'price' => 399000,
                'stock' => 12,
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
                'stock' => 15,
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
                'stock' => 12,
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
                'stock' => 25,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $cap->id,
                'sku' => 'CBC-BLK-OS',
                'color' => 'Black',
                'size' => 'One Size',
                'price' => 149000,
                'stock' => 25,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $cap->id,
                'sku' => 'CBC-BGE-OS',
                'color' => 'Beige',
                'size' => 'One Size',
                'price' => 149000,
                'stock' => 20,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $kidsTshirt->id,
                'sku' => 'KGT-BLU-8Y',
                'color' => 'Blue',
                'size' => '8Y',
                'price' => 159000,
                'stock' => 20,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'product_id' => $kidsTshirt->id,
                'sku' => 'KGT-BLU-10Y',
                'color' => 'Blue',
                'size' => '10Y',
                'price' => 159000,
                'stock' => 18,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}