<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Property;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('now', '+3 months');
        $checkOut = fake()->dateTimeBetween($checkIn, (clone $checkIn)->modify('+14 days'));

        return [
            'supplier_id' => Supplier::factory(),
            'property_id' => Property::factory(),
            'external_id' => fake()->unique()->uuid(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'max_guests' => fake()->numberBetween(1, 8),
            'price' => fake()->randomFloat(2, 50, 2000),
            'currency' => fake()->randomElement(['EUR', 'USD', 'GBP']),
            'available_units' => fake()->numberBetween(0, 20),
            'expires_at' => fake()->dateTimeBetween('now', '+3 months'),
        ];
    }

    /**
     * Indicate that the offer has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => fake()->dateTimeBetween('-3 months', '-1 day'),
        ]);
    }
}
