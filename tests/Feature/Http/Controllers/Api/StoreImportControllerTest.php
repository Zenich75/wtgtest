<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Jobs\ProcessImportJob;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StoreImportControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'supplier' => 'supplier-a',
            'external_import_id' => 'batch-001',
            'sent_at' => '2026-09-06T10:00:00Z',
            'offers' => [
                [
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
                    'expires_at' => '2026-09-20T00:00:00Z',
                ],
            ],
        ], $overrides);
    }

    public function test_valid_payload_creates_pending_import_and_dispatches_job_returns_202(): void
    {
        Queue::fake([ProcessImportJob::class]);
        Supplier::factory()->create(['code' => 'supplier-a']);

        $response = $this->postJson('/api/imports', $this->validPayload());

        $response->assertStatus(202);
        $response->assertJsonPath('status', 'pending');
        $this->assertDatabaseHas('imports', [
            'external_import_id' => 'batch-001',
            'status' => 'pending',
        ]);
        Queue::assertPushed(ProcessImportJob::class, 1);
    }

    public function test_duplicate_supplier_and_external_import_id_returns_existing_import_without_redispatching_job(): void
    {
        Queue::fake([ProcessImportJob::class]);
        Supplier::factory()->create(['code' => 'supplier-a']);

        $first = $this->postJson('/api/imports', $this->validPayload());
        $second = $this->postJson('/api/imports', $this->validPayload());

        $second->assertStatus(202);
        $this->assertSame($first->json('id'), $second->json('id'));
        $this->assertSame($first->json('status'), $second->json('status'));
        $this->assertDatabaseCount('imports', 1);
        Queue::assertPushed(ProcessImportJob::class, 1);
    }

    public function test_missing_required_fields_returns_422(): void
    {
        $response = $this->postJson('/api/imports', []);

        $response->assertStatus(422);
        $response->assertInvalid(['supplier', 'external_import_id', 'sent_at', 'offers']);
        $this->assertDatabaseCount('imports', 0);
    }

    public function test_unknown_supplier_code_returns_422(): void
    {
        $response = $this->postJson('/api/imports', $this->validPayload(['supplier' => 'does-not-exist']));

        $response->assertStatus(422);
        $response->assertInvalid(['supplier' => 'The selected supplier is invalid.']);
    }

    public function test_missing_nested_offer_property_field_returns_422(): void
    {
        $payload = $this->validPayload();
        unset($payload['offers'][0]['property']['city']);

        $response = $this->postJson('/api/imports', $payload);

        $response->assertStatus(422);
        $response->assertInvalid(['offers.0.property.city' => 'The offers.0.property.city field is required.']);
    }
}
