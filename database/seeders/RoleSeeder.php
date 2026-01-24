<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'admin', 'role_description' => 'Administrator'],
            ['role_name' => 'cashier', 'role_description' => 'Cashier'],
            ['role_name' => 'chef', 'role_description' => 'Chef'],
            ['role_name' => 'customer', 'role_description' => 'Customer'],
        ];

        DB::table('roles')->insert($roles);
    }
}