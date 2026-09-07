<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Offer;
use App\Models\Property;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class IndexPropertyControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const CHECK_IN = '2026-10-01';

    private const CHECK_OUT = '2026-10-05';

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function searchQuery(array $overrides = []): array
    {
        return array_merge([
            'check_in' => self::CHECK_IN,
            'check_out' => self::CHECK_OUT,
            'guests' => 2,
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function matchingOfferAttributes(array $overrides = []): array
    {
        return array_merge([
            'check_in' => self::CHECK_IN,
            'check_out' => self::CHECK_OUT,
            'max_guests' => 4,
            'available_units' => 2,
            'expires_at' => now()->addMonth(),
        ], $overrides);
    }

    public function test_selects_cheapest_offer_when_property_has_multiple_matching_offers(): void
    {
        $property = Property::factory()->create(['code' => 'P1']);
        $cheapSupplier = Supplier::factory()->withCode('cheap-supplier')->create();
        $expensiveSupplier = Supplier::factory()->withCode('expensive-supplier')->create();

        Offer::factory()->create($this->matchingOfferAttributes([
            'property_id' => $property->id,
            'supplier_id' => $expensiveSupplier->id,
            'external_id' => 'expensive',
            'price' => 200,
        ]));
        Offer::factory()->create($this->matchingOfferAttributes([
            'property_id' => $property->id,
            'supplier_id' => $cheapSupplier->id,
            'external_id' => 'cheap',
            'price' => 80,
        ]));

        $response = $this->getJson('/api/properties?'.http_build_query($this->searchQuery()));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.code', 'P1');
        $response->assertJsonPath('data.0.best_offer.supplier', 'cheap-supplier');
        $response->assertJsonPath('data.0.best_offer.price', '80.00');
    }

    public function test_only_includes_properties_with_available_units_greater_than_zero(): void
    {
        Offer::factory()->create($this->matchingOfferAttributes(['available_units' => 0]));

        $response = $this->getJson('/api/properties?'.http_build_query($this->searchQuery()));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_only_includes_offers_meeting_requested_guest_capacity(): void
    {
        Offer::factory()->create($this->matchingOfferAttributes(['max_guests' => 1]));

        $response = $this->getJson('/api/properties?'.http_build_query($this->searchQuery(['guests' => 4])));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_only_includes_non_expired_offers(): void
    {
        Offer::factory()->create($this->matchingOfferAttributes(['expires_at' => now()->subDay()]));

        $response = $this->getJson('/api/properties?'.http_build_query($this->searchQuery()));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_filters_results_by_city_when_provided(): void
    {
        $barcelona = Property::factory()->create(['code' => 'BCN', 'city' => 'Barcelona']);
        $madrid = Property::factory()->create(['code' => 'MAD', 'city' => 'Madrid']);
        Offer::factory()->create($this->matchingOfferAttributes(['property_id' => $barcelona->id]));
        Offer::factory()->create($this->matchingOfferAttributes(['property_id' => $madrid->id]));

        $response = $this->getJson('/api/properties?'.http_build_query($this->searchQuery(['city' => 'Barcelona'])));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.code', 'BCN');
    }

    public function test_paginates_results_and_returns_next_and_prev_links(): void
    {
        Offer::factory()->count(16)->create($this->matchingOfferAttributes());

        $firstPage = $this->getJson('/api/properties?'.http_build_query($this->searchQuery()));

        $firstPage->assertOk();
        $firstPage->assertJsonCount(15, 'data');
        $firstPage->assertJsonPath('meta.current_page', 1);
        $firstPage->assertJsonPath('meta.per_page', 15);
        $firstPage->assertJsonPath('meta.total', 16);
        $this->assertNull($firstPage->json('links.prev'));
        $this->assertNotNull($firstPage->json('links.next'));

        $secondPage = $this->getJson('/api/properties?'.http_build_query($this->searchQuery(['page' => 2])));

        $secondPage->assertOk();
        $secondPage->assertJsonCount(1, 'data');
        $secondPage->assertJsonPath('meta.current_page', 2);
        $this->assertNotNull($secondPage->json('links.prev'));
        $this->assertNull($secondPage->json('links.next'));
    }

    public function test_missing_required_query_parameters_returns_422(): void
    {
        $response = $this->getJson('/api/properties');

        $response->assertStatus(422);
        $response->assertInvalid(['check_in', 'check_out', 'guests']);
    }
}
