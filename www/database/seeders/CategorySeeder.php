<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Category::truncate();
        
        \App\Models\Category::insert([
            [
                "name" => "gadget",
                "enable" => 1
            ],
            [
                "name" => "furniture",
                "enable" => 1
            ],
            [
                "name" => "food",
                "enable" => 0
            ],
        ]);
    }
}
