<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SegmentSeeder::class,
            CategorySeeder::class,
            CollectionSeeder::class,

            ProductSeeder::class,
            ProductVariantSeeder::class,
            ProductCollectionSeeder::class,
            ProductImageSeeder::class,
        ]);
    }
}