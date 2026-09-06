<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexPropertyRequest;
use App\Http\Resources\PropertyResource;
use App\OpenApi\Schemas\PropertySchema;
use App\OpenApi\Schemas\ValidationErrorSchema;
use App\Services\PropertySearchService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class PropertyController extends Controller
{
    public function __construct(private readonly PropertySearchService $propertySearchService) {}

    #[OA\Get(
        path: '/api/properties',
        summary: 'Search properties with an available offer',
        description: 'Returns properties with at least one offer matching the given dates, guest count, '
            .'and optional city, showing only the cheapest matching offer per property.',
        tags: ['Properties'],
        parameters: [
            new OA\QueryParameter(
                name: 'city',
                description: 'Filter by property city (exact match).',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Barcelona',
            ),
            new OA\QueryParameter(
                name: 'check_in',
                description: 'Requested check-in date.',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'date'),
                example: '2026-10-01',
            ),
            new OA\QueryParameter(
                name: 'check_out',
                description: 'Requested check-out date.',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'date'),
                example: '2026-10-05',
            ),
            new OA\QueryParameter(
                name: 'guests',
                description: 'Minimum number of guests the offer must accommodate.',
                required: true,
                schema: new OA\Schema(type: 'integer', minimum: 1),
                example: 2,
            ),
            new OA\QueryParameter(
                name: 'page',
                description: 'Page number.',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1),
                example: 1,
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of matching properties.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: PropertySchema::class),
                        ),
                        new OA\Property(
                            property: 'links',
                            properties: [
                                new OA\Property(property: 'first', type: 'string', nullable: true),
                                new OA\Property(property: 'last', type: 'string', nullable: true),
                                new OA\Property(property: 'prev', type: 'string', nullable: true),
                                new OA\Property(property: 'next', type: 'string', nullable: true),
                            ],
                            type: 'object',
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
                                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                                new OA\Property(property: 'to', type: 'integer', nullable: true, example: 1),
                                new OA\Property(property: 'total', type: 'integer', example: 1),
                            ],
                            type: 'object',
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
    public function index(IndexPropertyRequest $request): AnonymousResourceCollection
    {
        $properties = $this->propertySearchService->search($request->validated());

        return PropertyResource::collection($properties);
    }
}
