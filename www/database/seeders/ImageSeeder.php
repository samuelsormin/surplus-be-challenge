<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Image::truncate();

        \App\Models\Image::insert([
            [
                "name" => "Phone",
                "file" => storage_path('app/public/seed_images/phone.jpg'),
                "enable" => 1,
            ],
            [
                "name" => "Laptop",
                "file" => storage_path('app/public/seed_images/laptop.jpg'),
                "enable" => 1,
            ],
        ]);
    }
}
