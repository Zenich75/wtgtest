<?php

namespace Database\Seeders;

use App\Models\Import;
use App\Models\Offer;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed demo imports, properties, offers, and reservations so the data
     * browser has content to show without needing to call the API first.
     */
    public function run(): void
    {
        $suppliers = Supplier::all()->push(
            Supplier::factory()->withCode('demo-supplier')->create(['name' => 'Demo Supplier'])
        );

        Import::factory()
            ->count(6)
            ->recycle($suppliers)
            ->create();

        $properties = Property::factory()->count(8)->create();

        $offers = Offer::factory()
            ->count(20)
            ->recycle($suppliers)
            ->recycle($properties)
            ->create();

        Reservation::factory()
            ->count(7)
            ->recycle($offers)
            ->create();

        Reservation::factory()
            ->cancelled()
            ->count(3)
            ->recycle($offers)
            ->create();
    }
}
