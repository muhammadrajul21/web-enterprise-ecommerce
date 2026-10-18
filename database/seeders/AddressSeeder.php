<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $rizky = User::where(
            'email',
            'rizky@lifestyle.test'
        )->firstOrFail();

        $nabila = User::where(
            'email',
            'nabila@lifestyle.test'
        )->firstOrFail();

        $dimas = User::where(
            'email',
            'dimas@lifestyle.test'
        )->firstOrFail();


        DB::table('addresses')->insert([
            [
                'user_id' => $rizky->id,
                'label' => 'Home',
                'recipient_name' => 'Rizky Maulana',
                'phone' => '081234560101',
                'address' => 'Jl. Merdeka No. 12',
                'city' => 'Banda Aceh',
                'province' => 'Aceh',
                'postal_code' => '23116',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => $rizky->id,
                'label' => 'Office',
                'recipient_name' => 'Rizky Maulana',
                'phone' => '081234560101',
                'address' => 'Jl. T. Nyak Arief No. 25',
                'city' => 'Banda Aceh',
                'province' => 'Aceh',
                'postal_code' => '23115',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => $nabila->id,
                'label' => 'Home',
                'recipient_name' => 'Nabila Rahma',
                'phone' => '081234560102',
                'address' => 'Jl. Malikussaleh No. 8',
                'city' => 'Lhokseumawe',
                'province' => 'Aceh',
                'postal_code' => '24351',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => $dimas->id,
                'label' => 'Home',
                'recipient_name' => 'Dimas Pratama',
                'phone' => '081234560103',
                'address' => 'Jl. Medan Banda Aceh No. 21',
                'city' => 'Bireuen',
                'province' => 'Aceh',
                'postal_code' => '24251',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}