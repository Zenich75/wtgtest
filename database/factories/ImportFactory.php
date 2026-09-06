<?php

namespace Database\Factories;

use App\Models\Import;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Import>
 */
class ImportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalOffers = fake()->numberBetween(1, 500);

        return [
            'supplier_id' => Supplier::factory(),
            'external_import_id' => fake()->unique()->uuid(),
            'sent_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'failed']),
            'total_offers' => $totalOffers,
            'processed_offers' => fake()->numberBetween(0, $totalOffers),
            'error' => null,
            'completed_at' => null,
        ];
    }
}
