<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Offer;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StoreReservationControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'client_reference' => 'RES-001',
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
        ], $overrides);
    }

    public function test_valid_payload_creates_confirmed_reservation_and_decrements_available_units_returns_201(): void
    {
        $offer = Offer::factory()->create(['available_units' => 3]);

        $response = $this->postJson("/api/offers/{$offer->id}/reservations", $this->validPayload());

        $response->assertStatus(201);
        $response->assertJsonPath('data.client_reference', 'RES-001');
        $response->assertJsonPath('data.status', 'confirmed');
        $this->assertDatabaseHas('reservations', [
            'offer_id' => $offer->id,
            'client_reference' => 'RES-001',
            'status' => 'confirmed',
        ]);
        $this->assertSame(2, $offer->fresh()->available_units);
    }

    public function test_returns_409_and_does_not_create_reservation_when_offer_has_zero_available_units(): void
    {
        $offer = Offer::factory()->create(['available_units' => 0]);

        $response = $this->postJson("/api/offers/{$offer->id}/reservations", $this->validPayload());

        $response->assertStatus(409);
        $this->assertDatabaseCount('reservations', 0);
        $this->assertSame(0, $offer->fresh()->available_units);
    }

    /**
     * Simulates a concurrent second booking attempt. PHPUnit executes both requests
     * sequentially on one connection, so this cannot exercise true parallel database
     * transactions, but it proves the availability check and decrement are applied
     * per request rather than computed once up front: the second attempt on a
     * just-depleted offer is correctly rejected instead of oversold. This is the
     * same guard that lockForUpdate() enforces against real concurrent transactions.
     */
    public function test_second_request_returns_409_after_first_request_exhausts_the_last_available_unit(): void
    {
        $offer = Offer::factory()->create(['available_units' => 1]);

        $first = $this->postJson("/api/offers/{$offer->id}/reservations", $this->validPayload(['client_reference' => 'RES-A']));
        $second = $this->postJson("/api/offers/{$offer->id}/reservations", $this->validPayload(['client_reference' => 'RES-B']));

        $first->assertStatus(201);
        $second->assertStatus(409);
        $this->assertDatabaseCount('reservations', 1);
        $this->assertSame(0, $offer->fresh()->available_units);
    }

    public function test_duplicate_client_reference_returns_422(): void
    {
        $offer = Offer::factory()->create(['available_units' => 5]);
        Reservation::factory()->create(['offer_id' => $offer->id, 'client_reference' => 'RES-001']);

        $response = $this->postJson("/api/offers/{$offer->id}/reservations", $this->validPayload());

        $response->assertStatus(422);
        $response->assertInvalid(['client_reference' => 'already been taken']);
    }

    public function test_missing_required_fields_returns_422(): void
    {
        $offer = Offer::factory()->create();

        $response = $this->postJson("/api/offers/{$offer->id}/reservations", []);

        $response->assertStatus(422);
        $response->assertInvalid(['client_reference', 'customer_name', 'customer_email']);
    }

    public function test_returns_404_for_nonexistent_offer(): void
    {
        $response = $this->postJson('/api/offers/999999/reservations', $this->validPayload());

        $response->assertNotFound();
    }
}
