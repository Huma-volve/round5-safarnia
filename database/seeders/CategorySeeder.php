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
    public function run(): void
    {

        // Create categories
        $categories = [
            [
                'title' => 'Flight',
                'description' => 'All flight-related services',
                'image' => 'flights.jpg',
            ],
            [
                'title' => 'Car Rental',
                'description' => 'All car rental services',
                'image' => 'car_rental.jpg',
            ],
            [
                'title' => 'Tour Package',
                'description' => 'All tour package services',
                'image' => 'tour_package.jpg',
            ],
            [
                'title' => 'hotel',
                'description' => 'All hotel-related services',
                'image' => 'hotels.jpg',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
        Category::create([
            "title"=>"Fright",
            "description"=>'comfortable and fast',
            "image"=>'ad.jpg'
        ]);

        Category::create([
            "title"=>"Cars",
            "description"=>'safety and fast',

            "image"=>'fg.jpg']);

            Category::create([
            "title"=>"Tours",
            "description"=>'enjoyment and recreation',

            "image"=>'kh.jpg']);

            Category::create([
            "title"=>"Hotel",
            "description"=>'luxury and extravagance',

            "image"=>'OIP.jpg']);

        // Historical Tours
        $categories['historical'] = Category::firstOrCreate(
            ['title' => 'Historical Tours'],
            [
                'description' => 'Explore ancient civilizations and historical landmarks',
                'image' => 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?w=800&h=600&fit=crop'
            ]
        );

        // Adventure Tours
        $categories['adventure'] = Category::firstOrCreate(
            ['title' => 'Adventure Tours'],
            [
                'description' => 'Thrilling outdoor adventures and desert experiences',
                'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop'
            ]
        );

        // Cultural Tours
        $categories['cultural'] = Category::firstOrCreate(
            ['title' => 'Cultural Tours'],
            [
                'description' => 'Immerse yourself in local culture and traditions',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop'
            ]
        );

        // Nature Tours
        $categories['nature'] = Category::firstOrCreate(
            ['title' => 'Nature Tours'],
            [
                'description' => 'Discover natural wonders and wildlife',
                'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop'
            ]
        );

        // Religious Tours
        $categories['religious'] = Category::firstOrCreate(
            ['title' => 'Religious Tours'],
            [
                'description' => 'Visit sacred sites and religious landmarks',
                'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop'
            ]
        );


            }

        }

