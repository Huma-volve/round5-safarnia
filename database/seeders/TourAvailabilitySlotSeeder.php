<?php
namespace Database\Seeders;
use Carbon\Carbon;

use App\Models\Tour;
use App\Models\TourAvailabilitySlot;
use Illuminate\Database\Seeder;

class TourAvailabilitySlotSeeder extends Seeder
{
    public function run(): void
    {
        $tours = Tour::all();

        foreach ($tours as $tour) {
            for ($i = 1; $i <= 5; $i++) {
                TourAvailabilitySlot::create([
                    'tour_id' => $tour->id,
                    'start_time' => Carbon::create(2025, 8, 10, 14, 0, 0), // 10 أغسطس 2025 الساعة 2:00
                    'end_time' => Carbon::create(2025, 8, 10, 16, 0, 0),   // 10 أغسطس 2025 الساعة 4
                    'available_seats' => rand(5, 20),
                ]);
            }
        }
    }
}