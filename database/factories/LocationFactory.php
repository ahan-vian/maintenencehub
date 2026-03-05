<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Location ' . $this->faker->unique()->numberBetween(1, 999),
            'building' => $this->faker->randomElement(['TULT', 'TOKO', 'Gedung A', 'Gedung B']),
            'floor' => (string) $this->faker->numberBetween(1, 21),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}