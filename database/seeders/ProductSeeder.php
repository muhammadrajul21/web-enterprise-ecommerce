<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $men = DB::table('segments')
            ->where('slug', 'men')
            ->first();

        $women = DB::table('segments')
            ->where('slug', 'women')
            ->first();

        $unisex = DB::table('segments')
            ->where('slug', 'unisex')
            ->first();

        $kids = DB::table('segments')
            ->where('slug', 'kids')
            ->first();


        $tshirts = DB::table('categories')
            ->where('slug', 't-shirts')
            ->first();

        $shirts = DB::table('categories')
            ->where('slug', 'shirts')
            ->first();

        $pants = DB::table('categories')
            ->where('slug', 'pants')
            ->first();

        $outerwear = DB::table('categories')
            ->where('slug', 'outerwear')
            ->first();

        $footwear = DB::table('categories')
            ->where('slug', 'footwear')
            ->first();

        $bags = DB::table('categories')
            ->where('slug', 'bags')
            ->first();

        $accessories = DB::table('categories')
            ->where('slug', 'accessories')
            ->first();


        DB::table('products')->insert([
            [
                'segment_id' => $unisex->id,
                'category_id' => $tshirts->id,
                'name' => 'Oversized T-Shirt',
                'slug' => 'oversized-t-shirt',
                'description' =>
                    'Kaos oversized berbahan nyaman dengan desain minimalis untuk gaya casual sehari-hari.',
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
                'description' =>
                    'Kemeja Oxford casual dengan tampilan clean untuk aktivitas santai maupun semi formal.',
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
                'description' =>
                    'Celana relaxed fit dengan potongan nyaman untuk aktivitas harian.',
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
                'description' =>
                    'Jaket ringan dengan desain sederhana yang cocok digunakan untuk berbagai aktivitas.',
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
                'description' =>
                    'Sneakers casual bergaya modern yang nyaman digunakan untuk aktivitas sehari-hari.',
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
                'description' =>
                    'Tote bag minimalis dengan ruang yang cukup untuk kebutuhan lifestyle sehari-hari.',
                'status' => 'active',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'segment_id' => $unisex->id,
                'category_id' => $accessories->id,
                'name' => 'Classic Baseball Cap',
                'slug' => 'classic-baseball-cap',
                'description' =>
                    'Topi baseball klasik dengan desain clean sebagai pelengkap gaya casual.',
                'status' => 'active',
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'segment_id' => $kids->id,
                'category_id' => $tshirts->id,
                'name' => 'Kids Graphic T-Shirt',
                'slug' => 'kids-graphic-t-shirt',
                'description' =>
                    'Kaos anak dengan desain grafis casual dan bahan nyaman untuk aktivitas sehari-hari.',
                'status' => 'active',
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}