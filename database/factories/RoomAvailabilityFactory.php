<?php

namespace Database\Factories;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomAvailability>
 */
class RoomAvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::now()->addDays(rand(0, 30));
        $endDate = (clone $startDate)->addDays(rand(1, 7));

        return [
            'room_id' => Room::inRandomOrder()->first()?->id ?? Room::factory(),
            'available_from' => $startDate->toDateString(),
            'available_to' => $endDate->toDateString(),
        ];
    }
}
