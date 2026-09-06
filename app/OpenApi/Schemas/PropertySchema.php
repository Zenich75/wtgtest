<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Property',
    title: 'Property',
    description: 'A property with its cheapest offer matching the search filters.',
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'PROP-1'),
        new OA\Property(property: 'name', type: 'string', example: 'Sunset Hotel'),
        new OA\Property(property: 'city', type: 'string', example: 'Barcelona'),
        new OA\Property(property: 'best_offer', ref: OfferSchema::class, type: 'object'),
    ],
)]
class PropertySchema
{
    //
}
