<?php

namespace Tests\Unit\Factories;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReservationFactoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_cancelled_state_sets_status_to_cancelled(): void
    {
        $reservation = Reservation::factory()->cancelled()->create();

        $this->assertSame('cancelled', $reservation->status);
    }
}
