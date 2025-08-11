<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TourSlot;
use App\Models\TourBooking;

class TourBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); // تأكد أن هناك مستخدم على الأقل
        if (!$user) {
            $this->command->warn("No user found. Please create a user first.");
            return;
        }

        $slots = TourSlot::take(3)->get();
        if ($slots->isEmpty()) {
            $this->command->warn("No tour slots found. Please create tour slots first.");
            return;
        }

        foreach ($slots as $slot) {
            TourBooking::create([
                'user_id' => $user->id,
                'tour_slot_id' => $slot->id,
                'status' => 'pending'
            ]);
        }

        $this->command->info('Tour bookings seeded successfully.');
    }
}