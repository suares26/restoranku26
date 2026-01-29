<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'category_id' => $this->faker->numberBetween(1, 2),
            'price' => $this->faker->randomFloat(2, 10000, 100000),
            'description' => $this->faker->sentence(),
            'img' => fake()->randomElement([
                'https://images.unsplash.com/photo-1626497132810-f38eb29c5385',
                'https://plus.unsplash.com/premium_photo-1664360228159-437020f924c8',
                'https://images.unsplash.com/photo-1569718212165-3a8278d5f624',
            ]),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
