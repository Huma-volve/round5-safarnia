<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run()
    {
        // Truncate tables to avoid duplicates (optional)
        DB::table('cars')->delete();
        DB::table('car_categories')->delete();

        // Create Car Categories
        $categories = [
            [
                'name' => 'SUV',
                'description' => 'Spacious and perfect for families or road trips.',
                'image_url' => 'https://via.placeholder.com/300x200?text=SUV',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sedan',
                'description' => 'Comfortable and fuel-efficient for city driving.',
                'image_url' => 'https://via.placeholder.com/300x200?text=Sedan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Economy',
                'description' => 'Affordable and great for short trips or budget travelers.',
                'image_url' => 'https://via.placeholder.com/300x200?text=Economy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Luxury',
                'description' => 'Premium comfort, advanced features, and top-tier brands.',
                'image_url' => 'https://via.placeholder.com/300x200?text=Luxury',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('car_categories')->insert($categories);

        // Get category IDs
        $suvId = DB::table('car_categories')->where('name', 'SUV')->value('id');
        $sedanId = DB::table('car_categories')->where('name', 'Sedan')->value('id');
        $economyId = DB::table('car_categories')->where('name', 'Economy')->value('id');
        $luxuryId = DB::table('car_categories')->where('name', 'Luxury')->value('id');

        // Sample Cars
        $cars = [
            // SUV
            [
                'model' => 'Toyota RAV4',
                'brand' => 'Toyota',
                'daily_rate' => 95.00,
                'seats' => 5,
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'has_ac' => true,
                'category_id' => $suvId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model' => 'Honda CR-V',
                'brand' => 'Honda',
                'daily_rate' => 88.00,
                'seats' => 5,
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'has_ac' => true,
                'category_id' => $suvId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Sedan
            [
                'model' => 'Honda Civic',
                'brand' => 'Honda',
                'daily_rate' => 65.00,
                'seats' => 4,
                'transmission' => 'Automatic',
                'fuel_type' => 'Hybrid',
                'has_ac' => true,
                'category_id' => $sedanId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model' => 'Toyota Camry',
                'brand' => 'Toyota',
                'daily_rate' => 75.00,
                'seats' => 5,
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'has_ac' => true,
                'category_id' => $sedanId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Economy
            [
                'model' => 'Toyota Corolla',
                'brand' => 'Toyota',
                'daily_rate' => 50.00,
                'seats' => 4,
                'transmission' => 'Manual',
                'fuel_type' => 'Gasoline',
                'has_ac' => true,
                'category_id' => $economyId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model' => 'Nissan Micra',
                'brand' => 'Nissan',
                'daily_rate' => 48.00,
                'seats' => 4,
                'transmission' => 'Manual',
                'fuel_type' => 'Gasoline',
                'has_ac' => true,
                'category_id' => $economyId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Luxury
            [
                'model' => 'BMW 5 Series',
                'brand' => 'BMW',
                'daily_rate' => 180.00,
                'seats' => 5,
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'has_ac' => true,
                'category_id' => $luxuryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model' => 'Mercedes E-Class',
                'brand' => 'Mercedes-Benz',
                'daily_rate' => 200.00,
                'seats' => 5,
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'has_ac' => true,
                'category_id' => $luxuryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cars')->insert($cars);
    }
}