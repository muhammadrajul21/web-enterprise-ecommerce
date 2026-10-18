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
            AddressSeeder::class,

            SegmentSeeder::class,
            CategorySeeder::class,
            CollectionSeeder::class,

            ProductSeeder::class,
            ProductVariantSeeder::class,
            StockLogSeeder::class,
            ProductCollectionSeeder::class,
            ProductImageSeeder::class,

            VoucherSeeder::class,
            OrderSeeder::class,
        ]);
    }
}