<?php

namespace Tests\Feature\Http\Controllers\Viewer;

use App\Models\Offer;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_reservations(): void
    {
        $offer = Offer::factory()->create();
        Reservation::factory()->create([
            'offer_id' => $offer->id,
            'client_reference' => 'ref-123',
            'customer_name' => 'Jane Doe',
            'status' => 'confirmed',
        ]);
        Reservation::factory()->cancelled()->create([
            'offer_id' => $offer->id,
            'client_reference' => 'ref-456',
            'customer_name' => 'John Smith',
        ]);

        $response = $this->get('/viewer/reservations');

        $response->assertOk();
        $response->assertSee('ref-123');
        $response->assertSee('Jane Doe');
        $response->assertSee('confirmed');
        $response->assertSee('ref-456');
        $response->assertSee('John Smith');
        $response->assertSee('cancelled');
    }
}
