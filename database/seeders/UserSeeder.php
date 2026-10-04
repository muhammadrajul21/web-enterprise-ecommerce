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
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'phone' => '081234567890',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $staffRole->id,
                'name' => 'Staff Gudang',
                'email' => 'staff@example.com',
                'phone' => '081234567891',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $ownerRole->id,
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'phone' => '081234567892',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $customerRole->id,
                'name' => 'Customer Demo',
                'email' => 'customer@example.com',
                'phone' => '081234567893',
                'status' => 'active',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}