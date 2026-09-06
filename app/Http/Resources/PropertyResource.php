<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->property_code,
            'name' => $this->property_name,
            'city' => $this->property_city,
            'best_offer' => [
                'id' => $this->offer_id,
                'supplier' => $this->offer_supplier,
                'price' => $this->offer_price,
                'currency' => $this->offer_currency,
                'available_units' => $this->offer_available_units,
                'expires_at' => $this->offer_expires_at,
            ],
        ];
    }
}
