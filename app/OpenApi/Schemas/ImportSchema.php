<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Import',
    title: 'Import',
    description: 'The status of a supplier offer import batch.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'supplier_code', type: 'string', example: 'supplier-a'),
        new OA\Property(property: 'external_import_id', type: 'string', example: 'batch-001'),
        new OA\Property(property: 'sent_at', type: 'string', format: 'date-time'),
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: ['pending', 'processing', 'completed', 'failed'],
            example: 'pending',
        ),
        new OA\Property(property: 'total_offers', type: 'integer', example: 10),
        new OA\Property(property: 'processed_offers', type: 'integer', example: 0),
        new OA\Property(property: 'error', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true),
    ],
)]
class ImportSchema
{
    //
}
