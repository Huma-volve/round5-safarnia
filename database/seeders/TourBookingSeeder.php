<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TourAvailabilitySlot;
use App\Models\TourBooking;
use Illuminate\Database\Seeder;

class TourBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(3)->get(); // Get first 3 users
        if ($users->isEmpty()) {
            $this->command->warn("No users found. Please create users first.");
            return;
        }

        $slots = TourAvailabilitySlot::where('start_time', '>', now())
                                    ->take(10)
                                    ->get();
        
        if ($slots->isEmpty()) {
            $this->command->warn("No tour slots found. Please create tour slots first.");
            return;
        }

        foreach ($slots as $slot) {
            $user = $users->random();
            $seatsCount = rand(1, 3);
            $totalPrice = $slot->tour->price * $seatsCount;
            
            TourBooking::create([
                'user_id' => $user->id,
                'tour_slot_id' => $slot->id,
                'status' => $this->getRandomStatus(),
                'seats_count' => $seatsCount,
                'total_price' => $totalPrice,
                'notes' => rand(0, 1) ? 'Special request: ' . $this->getRandomNote() : null,
            ]);
        }

        $this->command->info('Tour bookings seeded successfully.');
    }

    /**
     * Get random booking status
     */
    private function getRandomStatus()
    {
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        return $statuses[array_rand($statuses)];
    }

    /**
     * Get random note
     */
    private function getRandomNote()
    {
        $notes = [
            'Wheelchair accessible',
            'Vegetarian meal preference',
            'Early arrival requested',
            'Group booking',
            'Special photography needs'
        ];
        return $notes[array_rand($notes)];
    }
}