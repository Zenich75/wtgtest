<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Reservation',
    title: 'Reservation',
    description: 'A confirmed booking against an offer.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'offer_id', type: 'integer', example: 1),
        new OA\Property(property: 'client_reference', type: 'string', example: 'RES-001'),
        new OA\Property(property: 'customer_name', type: 'string', example: 'Jane Doe'),
        new OA\Property(property: 'customer_email', type: 'string', format: 'email', example: 'jane@example.com'),
        new OA\Property(property: 'status', type: 'string', example: 'confirmed'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ],
)]
class ReservationSchema
{
    //
}
