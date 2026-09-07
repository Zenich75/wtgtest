<?php

namespace Tests\Unit\Factories;

use App\Models\Offer;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OfferFactoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_expired_state_sets_expires_at_in_the_past(): void
    {
        $offer = Offer::factory()->expired()->create();

        $this->assertTrue($offer->expires_at->isPast());
    }

    public function test_for_supplier_code_state_sets_the_supplier_code(): void
    {
        $offer = Offer::factory()->forSupplierCode('supplier-a')->create();

        $this->assertSame('supplier-a', $offer->supplier->code);
    }
}
