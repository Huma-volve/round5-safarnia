<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Tour;
use App\Models\TourAvailabilitySlot;
use Illuminate\Database\Seeder;

class TourAvailabilitySlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tours = Tour::all();

        if ($tours->isEmpty()) {
            $this->command->warn("No tours found. Please create tours first.");
            return;
        }

        foreach ($tours as $tour) {
            // Create slots for the next 30 days
            for ($day = 1; $day <= 30; $day++) {
                // Morning slot (9 AM - 12 PM)
                TourAvailabilitySlot::create([
                    'tour_id' => $tour->id,
                    'start_time' => Carbon::now()->addDays($day)->setTime(9, 0, 0),
                    'end_time' => Carbon::now()->addDays($day)->setTime(12, 0, 0),
                    'available_seats' => rand(10, 25),
                    'max_seats' => rand(25, 40),
                ]);

                // Afternoon slot (2 PM - 5 PM)
                TourAvailabilitySlot::create([
                    'tour_id' => $tour->id,
                    'start_time' => Carbon::now()->addDays($day)->setTime(14, 0, 0),
                    'end_time' => Carbon::now()->addDays($day)->setTime(17, 0, 0),
                    'available_seats' => rand(8, 20),
                    'max_seats' => rand(20, 35),
                ]);

                // Evening slot (6 PM - 9 PM) - only for some tours
                if ($tour->id % 2 == 0) {
                    TourAvailabilitySlot::create([
                        'tour_id' => $tour->id,
                        'start_time' => Carbon::now()->addDays($day)->setTime(18, 0, 0),
                        'end_time' => Carbon::now()->addDays($day)->setTime(21, 0, 0),
                        'available_seats' => rand(5, 15),
                        'max_seats' => rand(15, 25),
                    ]);
                }
            }
        }

        $this->command->info('Tour availability slots seeded successfully.');
    }
}