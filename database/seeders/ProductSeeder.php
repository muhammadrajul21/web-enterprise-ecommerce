<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $men = DB::table('segments')->where('slug', 'men')->first();
        $women = DB::table('segments')->where('slug', 'women')->first();
        $unisex = DB::table('segments')->where('slug', 'unisex')->first();

        $tshirts = DB::table('categories')->where('slug', 't-shirts')->first();
        $shirts = DB::table('categories')->where('slug', 'shirts')->first();
        $pants = DB::table('categories')->where('slug', 'pants')->first();
        $outerwear = DB::table('categories')->where('slug', 'outerwear')->first();
        $footwear = DB::table('categories')->where('slug', 'footwear')->first();
        $bags = DB::table('categories')->where('slug', 'bags')->first();

        DB::table('products')->insert([
            [
                'segment_id' => $unisex->id,
                'category_id' => $tshirts->id,
                'name' => 'Oversized T-Shirt',
                'slug' => 'oversized-t-shirt',
                'description' => 'Kaos oversized dengan desain minimal untuk gaya casual sehari-hari.',
                'status' => 'active',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'segment_id' => $men->id,
                'category_id' => $shirts->id,
                'name' => 'Casual Oxford Shirt',
                'slug' => 'casual-oxford-shirt',
                'description' => 'Kemeja casual berbahan nyaman untuk aktivitas harian.',
                'status' => 'active',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'segment_id' => $unisex->id,
                'category_id' => $pants->id,
                'name' => 'Relaxed Pants',
                'slug' => 'relaxed-pants',
                'description' => 'Celana relaxed fit untuk tampilan sederhana dan nyaman.',
                'status' => 'active',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'segment_id' => $unisex->id,
                'category_id' => $outerwear->id,
                'name' => 'Lightweight Jacket',
                'slug' => 'lightweight-jacket',
                'description' => 'Jaket ringan untuk penggunaan sehari-hari.',
                'status' => 'active',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'segment_id' => $unisex->id,
                'category_id' => $footwear->id,
                'name' => 'Everyday Sneakers',
                'slug' => 'everyday-sneakers',
                'description' => 'Sneakers casual yang cocok untuk aktivitas harian.',
                'status' => 'active',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'segment_id' => $women->id,
                'category_id' => $bags->id,
                'name' => 'Minimal Tote Bag',
                'slug' => 'minimal-tote-bag',
                'description' => 'Tote bag minimalis untuk kebutuhan lifestyle sehari-hari.',
                'status' => 'active',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}