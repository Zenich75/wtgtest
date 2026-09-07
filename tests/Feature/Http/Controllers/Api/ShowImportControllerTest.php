<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Import;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowImportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_import_resource_with_supplier_code_and_status(): void
    {
        $supplier = Supplier::factory()->create(['code' => 'supplier-a']);
        $import = Import::factory()->create([
            'supplier_id' => $supplier->id,
            'external_import_id' => 'batch-001',
            'status' => 'completed',
            'total_offers' => 5,
            'processed_offers' => 5,
        ]);

        $response = $this->getJson("/api/imports/{$import->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $import->id);
        $response->assertJsonPath('data.supplier_code', 'supplier-a');
        $response->assertJsonPath('data.external_import_id', 'batch-001');
        $response->assertJsonPath('data.status', 'completed');
        $response->assertJsonPath('data.total_offers', 5);
        $response->assertJsonPath('data.processed_offers', 5);
    }

    public function test_returns_404_when_import_does_not_exist(): void
    {
        $response = $this->getJson('/api/imports/999999');

        $response->assertNotFound();
    }
}
