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
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(10000, 100000),
            'category_id' => \App\Models\Category::factory(),
            'img' => $this->faker->imageUrl(),
            'is_active' => true,
        ];
    }
}
