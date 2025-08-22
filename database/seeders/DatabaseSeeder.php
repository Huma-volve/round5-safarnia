<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users first
        User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        // Seed in proper order
        $this->call([
            // 1. Categories first
            CategorySeeder::class,

            // 2. Tours
            TourSeeder::class,

            // 3. Tour availability slots
            TourAvailabilitySlotSeeder::class,

            // 4. Tour bookings
            TourBookingSeeder::class,

            // 5. Profile data
            ProfileSeeder::class,
            HotelSeeder::class,

            // 6. Other services
            // HotelSeeder::class,
            FlightSeeder::class,
            CarSeeder::class,
        ]);
    }
}
