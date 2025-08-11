<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HotelReview>
 */
class HotelReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::inRandomOrder()->first()->id ?? Hotel::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'review_text' => $this->faker->paragraph,
        ];
    }
}
