<?php

namespace App\Jobs;

use App\Models\Import;
use App\Models\Offer;
use App\Models\Property;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessImportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly Import $import) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->import->update(['status' => 'processing']);

        try {
            DB::transaction(function () {
                $processed = 0;

                foreach ($this->import->payload as $offer) {
                    $property = Property::firstOrCreate(
                        ['code' => $offer['property']['code']],
                        [
                            'name' => $offer['property']['name'],
                            'city' => $offer['property']['city'],
                        ],
                    );

                    Offer::updateOrCreate(
                        [
                            'supplier_id' => $this->import->supplier_id,
                            'external_id' => $offer['external_id'],
                        ],
                        [
                            'property_id' => $property->id,
                            'check_in' => $offer['check_in'],
                            'check_out' => $offer['check_out'],
                            'max_guests' => $offer['max_guests'],
                            'price' => $offer['price'],
                            'currency' => $offer['currency'],
                            'available_units' => $offer['available_units'],
                            'expires_at' => $offer['expires_at'],
                        ],
                    );

                    $processed++;
                }

                $this->import->update([
                    'processed_offers' => $processed,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            });
        } catch (Throwable $e) {
            $this->import->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
