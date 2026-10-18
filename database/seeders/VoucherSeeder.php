<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vouchers')->insert([
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount 10%',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_order_amount' => 150000,
                'max_discount_amount' => 50000,
                'usage_limit' => 100,
                'used_count' => 1,
                'starts_at' => now()->subMonth(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'LIFESTYLE50',
                'name' => 'Lifestyle Discount Rp 50.000',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_order_amount' => 500000,
                'max_discount_amount' => null,
                'usage_limit' => 50,
                'used_count' => 0,
                'starts_at' => now()->subMonth(),
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'WEEKEND15',
                'name' => 'Weekend Special 15%',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'min_order_amount' => 300000,
                'max_discount_amount' => 75000,
                'usage_limit' => 75,
                'used_count' => 0,
                'starts_at' => now()->subDays(7),
                'expires_at' => now()->addMonth(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}