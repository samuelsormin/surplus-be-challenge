<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Product::truncate();

        \App\Models\Product::insert([
            [
                "name" => "Handphone",
                "description" => "Amazing phone",
                "enable" => 1
            ],
            [
                "name" => "Laptop",
                "description" => "Amazing laptop",
                "enable" => 1
            ],
        ]);
    }
}
