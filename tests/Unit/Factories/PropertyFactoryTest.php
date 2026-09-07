<?php

namespace Tests\Unit\Factories;

use App\Models\Property;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PropertyFactoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_in_city_state_sets_the_given_city(): void
    {
        $property = Property::factory()->inCity('Barcelona')->create();

        $this->assertSame('Barcelona', $property->city);
    }
}
