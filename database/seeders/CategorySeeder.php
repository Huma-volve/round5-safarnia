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


            }

        }

