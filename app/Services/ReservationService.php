<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * Reserve a unit on the given offer, returning null when none are available.
     *
     * @param  array{client_reference: string, customer_name: string, customer_email: string}  $data
     */
    public function reserve(Offer $offer, array $data): ?Reservation
    {
        return DB::transaction(function () use ($offer, $data) {
            $lockedOffer = Offer::whereKey($offer->id)->lockForUpdate()->firstOrFail();

            if ($lockedOffer->available_units <= 0) {
                return null;
            }

            $lockedOffer->decrement('available_units');

            return Reservation::create([
                'offer_id' => $lockedOffer->id,
                'client_reference' => $data['client_reference'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'status' => 'confirmed',
            ]);
        });
    }
}
