<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\ProductImage::truncate();

        \App\Models\ProductImage::insert([
            [
                "product_id" => 1,
                "image_id" => 1
            ],
            [
                "product_id" => 2,
                "image_id" => 2
            ],
        ]);
    }
}
