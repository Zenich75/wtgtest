<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Offer;
use App\OpenApi\Schemas\ErrorSchema;
use App\OpenApi\Schemas\ReservationSchema;
use App\OpenApi\Schemas\ValidationErrorSchema;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ReservationController extends Controller
{
    public function __construct(private readonly ReservationService $reservationService) {}

    #[OA\Post(
        path: '/api/offers/{offer}/reservations',
        summary: 'Reserve a unit on an offer',
        description: 'Locks the offer row, checks available_units, decrements it, and creates the '
            .'reservation inside a single database transaction.',
        tags: ['Reservations'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['client_reference', 'customer_name', 'customer_email'],
                properties: [
                    new OA\Property(property: 'client_reference', type: 'string', example: 'RES-001'),
                    new OA\Property(property: 'customer_name', type: 'string', example: 'Jane Doe'),
                    new OA\Property(property: 'customer_email', type: 'string', format: 'email', example: 'jane@example.com'),
                ],
            ),
        ),
        parameters: [
            new OA\PathParameter(name: 'offer', description: 'Offer ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Reservation created.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: ReservationSchema::class, type: 'object'),
                    ],
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Offer not found.',
                content: new OA\JsonContent(ref: ErrorSchema::class),
            ),
            new OA\Response(
                response: 409,
                description: 'No available units for this offer.',
                content: new OA\JsonContent(ref: ErrorSchema::class),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error.',
                content: new OA\JsonContent(ref: ValidationErrorSchema::class),
            ),
        ],
    )]
    public function store(StoreReservationRequest $request, Offer $offer): JsonResponse
    {
        $reservation = $this->reservationService->reserve($offer, $request->validated());

        if ($reservation === null) {
            return response()->json([
                'message' => 'No available units for this offer.',
            ], 409);
        }

        return (new ReservationResource($reservation))
            ->response()
            ->setStatusCode(201);
    }
}
