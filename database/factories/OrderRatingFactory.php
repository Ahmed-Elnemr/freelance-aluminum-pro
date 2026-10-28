<?php

namespace Database\Factories;

use App\Enum\OrderRatingTag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderRating>
 */
class OrderRatingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'maintenance_id' => null,
            'stars' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional()->sentence(),
            'tags' => [OrderRatingTag::OnTime->value],
            'is_hidden' => false,
        ];
    }
}
