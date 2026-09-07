<?php

namespace Tests\Feature\Http\Controllers\Viewer;

use App\Models\Import;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexImportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_imports_with_supplier_and_status(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Acme Supplier']);
        Import::factory()->create([
            'supplier_id' => $supplier->id,
            'external_import_id' => 'ext-123',
            'status' => 'completed',
        ]);

        $response = $this->get('/viewer/imports');

        $response->assertOk();
        $response->assertSee('Acme Supplier');
        $response->assertSee('ext-123');
        $response->assertSee('completed');
    }

    public function test_paginates_imports(): void
    {
        Import::factory()->count(25)->create();

        $response = $this->get('/viewer/imports');

        $response->assertOk();
        $response->assertViewHas('imports', fn ($imports) => $imports->count() === 20 && $imports->hasMorePages());
    }
}
