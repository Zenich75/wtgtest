<?php

namespace App\Services;

use App\Jobs\ProcessImportJob;
use App\Models\Import;
use App\Models\Supplier;

class ImportService
{
    /**
     * Create the import for the given payload, dispatching processing only when it is newly created.
     *
     * @param  array<string, mixed>  $data
     */
    public function import(array $data): Import
    {
        $supplier = Supplier::where('code', $data['supplier'])->firstOrFail();

        $import = Import::firstOrCreate(
            [
                'supplier_id' => $supplier->id,
                'external_import_id' => $data['external_import_id'],
            ],
            [
                'payload' => $data['offers'],
                'sent_at' => $data['sent_at'],
                'status' => 'pending',
                'total_offers' => count($data['offers']),
            ],
        );

        if ($import->wasRecentlyCreated) {
            ProcessImportJob::dispatch($import);
        }

        return $import;
    }
}
