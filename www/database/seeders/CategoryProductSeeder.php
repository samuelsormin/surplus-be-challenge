<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\CategoryProduct::truncate();

        \App\Models\CategoryProduct::insert([
            [
                "product_id" => 1,
                "category_id" => 1
            ],
            [
                "product_id" => 2,
                "category_id" => 1
            ],
        ]);
    }
}
