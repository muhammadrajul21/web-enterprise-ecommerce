<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'staff_gudang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
