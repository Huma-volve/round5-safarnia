<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'price' => $this->faker->randomFloat(2, 50, 1000),
            'capacity' => $this->faker->numberBetween(1, 5),
            'bathroom_number' => $this->faker->numberBetween(1, 3),
            'area' => $this->faker->randomFloat(1, 10, 100),
            'description' => $this->faker->paragraph(),
        ];
    }
}
