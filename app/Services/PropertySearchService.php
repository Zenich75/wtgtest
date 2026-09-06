<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PropertySearchService
{
    /**
     * Search properties with an available offer matching the given filters, returning
     * only the cheapest valid offer per property.
     *
     * @param  array{city?: string|null, check_in: string, check_out: string, guests: int}  $filters
     */
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $rankedOffers = DB::table('offers')
            ->join('properties', 'properties.id', '=', 'offers.property_id')
            ->join('suppliers', 'suppliers.id', '=', 'offers.supplier_id')
            ->where('offers.check_in', $filters['check_in'])
            ->where('offers.check_out', $filters['check_out'])
            ->where('offers.max_guests', '>=', $filters['guests'])
            ->where('offers.available_units', '>', 0)
            ->where('offers.expires_at', '>', now())
            ->when(
                $filters['city'] ?? null,
                fn ($query, string $city) => $query->where('properties.city', $city),
            )
            ->select([
                'properties.code as property_code',
                'properties.name as property_name',
                'properties.city as property_city',
                'offers.id as offer_id',
                'suppliers.code as offer_supplier',
                'offers.price as offer_price',
                'offers.currency as offer_currency',
                'offers.available_units as offer_available_units',
                'offers.expires_at as offer_expires_at',
                DB::raw('ROW_NUMBER() OVER (PARTITION BY offers.property_id ORDER BY offers.price ASC) as rn'),
            ]);

        return DB::query()
            ->fromSub($rankedOffers, 'ranked_offers')
            ->where('rn', 1)
            ->orderBy('property_code')
            ->paginate($perPage)
            ->withQueryString();
    }
}
