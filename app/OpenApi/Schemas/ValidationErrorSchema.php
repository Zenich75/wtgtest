<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ValidationError',
    title: 'ValidationError',
    description: 'Validation failure response.',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The supplier field is required. (and 1 more error)'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string'),
            ),
            example: ['supplier' => ['The supplier field is required.']],
        ),
    ],
)]
class ValidationErrorSchema
{
    //
}
