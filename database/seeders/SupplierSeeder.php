<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::factory()->withCode('supplier-a')->create([
            'name' => 'Supplier A',
        ]);

        Supplier::factory()->withCode('supplier-b')->create([
            'name' => 'Supplier B',
        ]);
    }
}
