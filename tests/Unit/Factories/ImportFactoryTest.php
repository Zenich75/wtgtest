<?php

namespace Tests\Unit\Factories;

use App\Models\Import;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ImportFactoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_failed_state_sets_status_to_failed(): void
    {
        $import = Import::factory()->failed()->create();

        $this->assertSame('failed', $import->status);
    }
}
