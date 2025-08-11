<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

class RecommdedTourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tours = [
            [
                'category_id' => 3,
                'title' => 'The Pyramids',
                'description' => 'The pyramids of Egypt are one of the seven wonders of the world.',
                'price' => 350,
                'rating' => 4.5,
                'image' => 'as.webp',
                'views' => 1500,
                'location' => 'Egypt, Giza',
                'is_recommended' => true,
            ],
            [
                'category_id' => 3,
                'title' => 'Citadel of Salah El-Din',
                'description' => 'A medieval Islamic fortification in Cairo.',
                'price' => 200,
                'rating' => 3.2,
                'image' => 'R.jpg',
                'views' => 1100,
                'location' => 'Egypt, Cairo',
                'is_recommended' => true,
            ],
            [
                'category_id' => 3,
                'title' => 'Fayoum',
                'description' => 'Beautiful natural landscapes and historical sites.',
                'price' => 350,
                'rating' => 4.5,
                'image' => 'fa.jpg',
                'views' => 3000,
                'location' => 'Egypt, Fayoum',
                'is_recommended' => true,
            ],
            [
                'category_id' => 3,
                'title' => 'Dahab',
                'description' => 'Popular diving destination on the Red Sea.',
                'price' => 5000,
                'rating' => 4.9,
                'image' => 'da.webp',
                'views' => 2000,
                'location' => 'Egypt, Sinai',
                'is_recommended' => true,
            ],
            [
                'category_id' => 3,
                'title' => 'Luxor',
                'description' => 'Famous for ancient temples and tombs.',
                'price' => 2500,
                'rating' => 4.7,
                'image' => 'lu.webp',
                'views' => 1700,
                'location' => 'Egypt, Luxor',
                'is_recommended' => true,
            ],
        ];

        foreach ($tours as $tour) {
            Tour::create($tour);
        }
    }
}
