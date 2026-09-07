<?php

namespace Tests\Unit\Factories;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SupplierFactoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_with_code_state_sets_the_given_code(): void
    {
        $supplier = Supplier::factory()->withCode('supplier-a')->create();

        $this->assertSame('supplier-a', $supplier->code);
    }
}
