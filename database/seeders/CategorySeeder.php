<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['cat_name' => 'Makanan', 'description' => 'Ketegori makananan'],
            ['cat_name' => 'Minuman', 'description' => 'Kategori minuman'],
        ];

        DB::table('categories')->insert($categories);
    }
}