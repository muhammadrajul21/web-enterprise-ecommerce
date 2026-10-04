<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->get()->keyBy('slug');

        DB::table('product_images')->insert([
            [
                'product_id' => $products['oversized-t-shirt']->id,
                'variant_id' => null,
                'image_path' => 'images/products/oversized-t-shirt.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $products['casual-oxford-shirt']->id,
                'variant_id' => null,
                'image_path' => 'images/products/casual-oxford-shirt.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $products['relaxed-pants']->id,
                'variant_id' => null,
                'image_path' => 'images/products/relaxed-pants.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $products['lightweight-jacket']->id,
                'variant_id' => null,
                'image_path' => 'images/products/lightweight-jacket.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $products['everyday-sneakers']->id,
                'variant_id' => null,
                'image_path' => 'images/products/everyday-sneakers.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $products['minimal-tote-bag']->id,
                'variant_id' => null,
                'image_path' => 'images/products/minimal-tote-bag.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}