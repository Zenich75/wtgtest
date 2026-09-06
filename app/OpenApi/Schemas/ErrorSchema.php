<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Error',
    title: 'Error',
    description: 'A generic error response.',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Resource not found.'),
    ],
)]
class ErrorSchema
{
    //
}
