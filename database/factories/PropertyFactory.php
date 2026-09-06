<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('PROP-####')),
            'name' => fake()->company().' '.fake()->randomElement(['Hotel', 'Resort', 'Apartments']),
            'city' => fake()->city(),
        ];
    }
}
