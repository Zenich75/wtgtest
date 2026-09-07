<?php

namespace Tests\Feature\Http\Controllers\Viewer;

use App\Models\Offer;
use App\Models\Property;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class IndexOfferControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_lists_offers(): void
    {
        $supplier = Supplier::factory()->create(['code' => 'sup-1', 'name' => 'Supplier One']);
        $property = Property::factory()->create(['code' => 'P1', 'city' => 'Madrid']);
        Offer::factory()->create(['supplier_id' => $supplier->id, 'property_id' => $property->id]);

        $response = $this->get('/viewer/offers');

        $response->assertOk();
        $response->assertSee('Supplier One');
        $response->assertSee('P1');
    }

    public function test_filters_by_supplier_code(): void
    {
        $matching = Supplier::factory()->create(['code' => 'match-sup', 'name' => 'Matching Supplier']);
        $other = Supplier::factory()->create(['code' => 'other-sup', 'name' => 'Other Supplier']);
        Offer::factory()->create(['supplier_id' => $matching->id]);
        Offer::factory()->create(['supplier_id' => $other->id]);

        $response = $this->get('/viewer/offers?supplier=match-sup');

        $response->assertOk();
        $response->assertSee('Matching Supplier');
        $response->assertDontSee('Other Supplier');
    }

    public function test_filters_by_city(): void
    {
        $barcelona = Property::factory()->create(['code' => 'BCN', 'city' => 'Barcelona']);
        $madrid = Property::factory()->create(['code' => 'MAD', 'city' => 'Madrid']);
        Offer::factory()->create(['property_id' => $barcelona->id]);
        Offer::factory()->create(['property_id' => $madrid->id]);

        $response = $this->get('/viewer/offers?city=Barcelona');

        $response->assertOk();
        $response->assertSee('BCN');
        $response->assertDontSee('MAD');
    }
}
