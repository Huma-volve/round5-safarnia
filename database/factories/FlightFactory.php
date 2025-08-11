<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class FlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::where('title', 'Flight')->first()->id,
            'airline' => $this->faker->company,
            'from' => $this->faker->city,
            'to' => $this->faker->city,
            'departure_time' => $this->faker->dateTimeBetween('now', '+1 year'),
            'arrival_time' => $this->faker->dateTimeBetween('+1 year', '+2 years'),
            'price' => $this->faker->numberBetween(1000, 9000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
