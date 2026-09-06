<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexPropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Services\PropertySearchService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PropertyController extends Controller
{
    public function __construct(private readonly PropertySearchService $propertySearchService) {}

    public function index(IndexPropertyRequest $request): AnonymousResourceCollection
    {
        $properties = $this->propertySearchService->search($request->validated());

        return PropertyResource::collection($properties);
    }
}
