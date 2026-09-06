<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImportRequest;
use App\Http\Resources\ImportResource;
use App\Models\Import;
use App\OpenApi\Schemas\ErrorSchema;
use App\OpenApi\Schemas\ImportSchema;
use App\OpenApi\Schemas\ValidationErrorSchema;
use App\Services\ImportService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ImportController extends Controller
{
    public function __construct(private readonly ImportService $importService) {}

    #[OA\Post(
        path: '/api/imports',
        summary: 'Submit a supplier offer import batch',
        description: 'Creates an import idempotently by supplier and external_import_id. A duplicate '
            .'request returns the existing import without dispatching a new processing job.',
        tags: ['Imports'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['supplier', 'external_import_id', 'sent_at', 'offers'],
                properties: [
                    new OA\Property(property: 'supplier', type: 'string', description: 'Supplier code.', example: 'supplier-a'),
                    new OA\Property(property: 'external_import_id', type: 'string', example: 'batch-001'),
                    new OA\Property(property: 'sent_at', type: 'string', format: 'date-time', example: '2026-09-06T10:00:00Z'),
                    new OA\Property(
                        property: 'offers',
                        type: 'array',
                        items: new OA\Items(
                            required: [
                                'external_id', 'property', 'check_in', 'check_out',
                                'max_guests', 'price', 'currency', 'available_units', 'expires_at',
                            ],
                            properties: [
                                new OA\Property(property: 'external_id', type: 'string', example: 'off-1'),
                                new OA\Property(
                                    property: 'property',
                                    type: 'object',
                                    required: ['code', 'name', 'city'],
                                    properties: [
                                        new OA\Property(property: 'code', type: 'string', example: 'PROP-1'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Sunset Hotel'),
                                        new OA\Property(property: 'city', type: 'string', example: 'Barcelona'),
                                    ],
                                ),
                                new OA\Property(property: 'check_in', type: 'string', format: 'date', example: '2026-10-01'),
                                new OA\Property(property: 'check_out', type: 'string', format: 'date', example: '2026-10-05'),
                                new OA\Property(property: 'max_guests', type: 'integer', example: 4),
                                new OA\Property(property: 'price', type: 'number', format: 'float', example: 199.99),
                                new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                                new OA\Property(property: 'available_units', type: 'integer', example: 3),
                                new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', example: '2026-09-20T00:00:00Z'),
                            ],
                        ),
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: 'Import accepted (either newly created and queued, or already existing).',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(
                            property: 'status',
                            type: 'string',
                            enum: ['pending', 'processing', 'completed', 'failed'],
                            example: 'pending',
                        ),
                    ],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error.',
                content: new OA\JsonContent(ref: ValidationErrorSchema::class),
            ),
        ],
    )]
    public function store(StoreImportRequest $request): JsonResponse
    {
        $import = $this->importService->import($request->validated());

        return response()->json([
            'id' => $import->id,
            'status' => $import->status,
        ], 202);
    }

    #[OA\Get(
        path: '/api/imports/{import}',
        summary: 'Get the status of an import',
        tags: ['Imports'],
        parameters: [
            new OA\PathParameter(name: 'import', description: 'Import ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The import.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: ImportSchema::class, type: 'object'),
                    ],
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Import not found.',
                content: new OA\JsonContent(ref: ErrorSchema::class),
            ),
        ],
    )]
    public function show(Import $import): ImportResource
    {
        return new ImportResource($import->loadMissing('supplier'));
    }
}
