<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockLogSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where(
            'email',
            'staff@lifestyle.test'
        )->firstOrFail();


        $variants = ProductVariant::query()
            ->where('status', 'active')
            ->get();


        foreach ($variants as $variant) {
            DB::table('stock_logs')->insert([
                'variant_id' =>
                    $variant->id,

                'type' =>
                    'IN',

                'quantity' =>
                    $variant->stock,

                'stock_before' =>
                    0,

                'stock_after' =>
                    $variant->stock,

                'reference_type' =>
                    'initial_stock',

                'reference_id' =>
                    null,

                'note' =>
                    'Initial demo stock.',

                'created_by' =>
                    $staff->id,

                'created_at' =>
                    now()->subDays(14),

                'updated_at' =>
                    now()->subDays(14),
            ]);
        }
    }
}