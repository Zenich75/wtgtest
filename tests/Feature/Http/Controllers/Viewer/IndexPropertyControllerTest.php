<?php

namespace Tests\Feature\Http\Controllers\Viewer;

use App\Models\Offer;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexPropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_properties_with_offer_count(): void
    {
        $property = Property::factory()->create(['code' => 'P1', 'name' => 'Casa Sol', 'city' => 'Madrid']);
        Offer::factory()->count(3)->create(['property_id' => $property->id]);

        $response = $this->get('/viewer/properties');

        $response->assertOk();
        $response->assertSee('P1');
        $response->assertSee('Casa Sol');
        $response->assertSee('Madrid');
        $response->assertViewHas('properties', fn ($properties) => $properties->first()->offers_count === 3);
    }
}
