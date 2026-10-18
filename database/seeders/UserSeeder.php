<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::table('roles')
            ->where('name', 'admin')
            ->first();

        $staffRole = DB::table('roles')
            ->where('name', 'staff_gudang')
            ->first();

        $ownerRole = DB::table('roles')
            ->where('name', 'owner')
            ->first();

        $customerRole = DB::table('roles')
            ->where('name', 'customer')
            ->first();


        DB::table('users')->insert([
            [
                'role_id' => $adminRole->id,
                'name' => 'Admin Lifestyle',
                'email' => 'admin@lifestyle.test',
                'phone' => '081234560001',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'role_id' => $staffRole->id,
                'name' => 'Staff Gudang Lifestyle',
                'email' => 'staff@lifestyle.test',
                'phone' => '081234560002',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'role_id' => $ownerRole->id,
                'name' => 'Owner Lifestyle',
                'email' => 'owner@lifestyle.test',
                'phone' => '081234560003',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'role_id' => $customerRole->id,
                'name' => 'Rizky Maulana',
                'email' => 'rizky@lifestyle.test',
                'phone' => '081234560101',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'role_id' => $customerRole->id,
                'name' => 'Nabila Rahma',
                'email' => 'nabila@lifestyle.test',
                'phone' => '081234560102',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'role_id' => $customerRole->id,
                'name' => 'Dimas Pratama',
                'email' => 'dimas@lifestyle.test',
                'phone' => '081234560103',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}