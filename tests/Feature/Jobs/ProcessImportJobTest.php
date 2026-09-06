<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessImportJob;
use App\Models\Import;
use App\Models\Offer;
use App\Models\Property;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProcessImportJobTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payloadItem(array $overrides = []): array
    {
        return array_merge([
            'external_id' => 'off-1',
            'property' => [
                'code' => 'PROP-1',
                'name' => 'Sunset Hotel',
                'city' => 'Barcelona',
            ],
            'check_in' => '2026-10-01',
            'check_out' => '2026-10-05',
            'max_guests' => 4,
            'price' => 199.99,
            'currency' => 'EUR',
            'available_units' => 3,
            'expires_at' => '2026-09-20 00:00:00',
        ], $overrides);
    }

    public function test_creates_properties_and_offers_and_marks_import_completed(): void
    {
        $supplier = Supplier::factory()->create();
        $import = Import::factory()->create([
            'supplier_id' => $supplier->id,
            'payload' => [$this->payloadItem()],
            'status' => 'pending',
            'total_offers' => 1,
            'processed_offers' => 0,
        ]);

        (new ProcessImportJob($import))->handle();

        $import->refresh();
        $this->assertSame('completed', $import->status);
        $this->assertSame(1, $import->processed_offers);
        $this->assertNotNull($import->completed_at);
        $this->assertDatabaseHas('properties', ['code' => 'PROP-1', 'city' => 'Barcelona']);
        $this->assertDatabaseHas('offers', [
            'supplier_id' => $supplier->id,
            'external_id' => 'off-1',
            'price' => 199.99,
        ]);
    }

    public function test_updates_existing_offer_instead_of_duplicating_when_external_id_repeats(): void
    {
        $supplier = Supplier::factory()->create();
        $property = Property::factory()->create(['code' => 'PROP-1']);
        $existingOffer = Offer::factory()->create([
            'supplier_id' => $supplier->id,
            'property_id' => $property->id,
            'external_id' => 'off-1',
            'price' => 100,
            'available_units' => 1,
        ]);

        $import = Import::factory()->create([
            'supplier_id' => $supplier->id,
            'payload' => [$this->payloadItem(['price' => 250.00, 'available_units' => 9])],
            'status' => 'pending',
            'total_offers' => 1,
            'processed_offers' => 0,
        ]);

        (new ProcessImportJob($import))->handle();

        $this->assertDatabaseCount('offers', 1);
        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseHas('offers', [
            'id' => $existingOffer->id,
            'price' => 250.00,
            'available_units' => 9,
        ]);
    }

    public function test_marks_import_failed_with_error_message_and_rolls_back_when_processing_throws(): void
    {
        $supplier = Supplier::factory()->create();
        $import = Import::factory()->create([
            'supplier_id' => $supplier->id,
            'payload' => [$this->payloadItem(['price' => 'not-a-number'])],
            'status' => 'pending',
            'total_offers' => 1,
            'processed_offers' => 0,
        ]);

        (new ProcessImportJob($import))->handle();

        $import->refresh();
        $this->assertSame('failed', $import->status);
        $this->assertNotNull($import->error);
        $this->assertSame(0, $import->processed_offers);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('properties', 0);
    }
}
