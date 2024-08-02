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
            'name' => $this->faker->unique()->realText(20),
            'description' => $this->faker->unique()->paragraph(2),
            'price' => $this->faker->numberBetween(10, 200),
            'quantity' => $this->faker->numberBetween(20, 100),
            'photo_key' => 'gummy.png'
        ];
    }
}
