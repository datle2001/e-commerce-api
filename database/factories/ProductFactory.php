<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->realText($maxNbChars = 20),
            'description' => $this->faker->unique()->paragraph(2),
            'price' => $this->faker->numberBetween(1, 1000),
            'quantity_available' => $this->faker->numberBetween(0, 20),
            'photo' => $this->faker->image()
        ];
    }
}
