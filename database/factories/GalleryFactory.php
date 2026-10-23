<?php

namespace Database\Factories;

use App\Enum\GalleryTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gallery>
 */
class GalleryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'ar' => fake()->words(3, true),
                'en' => fake()->words(3, true),
            ],
            'type' => GalleryTypeEnum::Image,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => GalleryTypeEnum::Video,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
