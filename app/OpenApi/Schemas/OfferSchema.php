<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Offer',
    title: 'Offer',
    description: 'The cheapest available offer for a property matching the search filters.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'supplier', type: 'string', description: 'Supplier code.', example: 'supplier-a'),
        new OA\Property(property: 'price', type: 'string', description: 'Decimal price, serialized as a string.', example: '199.99'),
        new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
        new OA\Property(property: 'available_units', type: 'integer', example: 3),
        new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', example: '2026-09-20T00:00:00.000000Z'),
    ],
)]
class OfferSchema
{
    //
}
