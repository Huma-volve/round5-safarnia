<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static int $hotelCategoryId;

    public function run(): void
    {
        $hotelCategory = Category::create([
            'title' => 'Hotel',
            'description' => 'All hotel-related services',
            'image' => 'hotels.jpg'
        ]);
        self::$hotelCategoryId = $hotelCategory->id;
    }
}
