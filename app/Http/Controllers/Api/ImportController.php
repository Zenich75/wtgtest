<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImportRequest;
use App\Http\Resources\ImportResource;
use App\Models\Import;
use App\Services\ImportService;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    public function __construct(private readonly ImportService $importService) {}

    public function store(StoreImportRequest $request): JsonResponse
    {
        $import = $this->importService->import($request->validated());

        return response()->json([
            'id' => $import->id,
            'status' => $import->status,
        ], 202);
    }

    public function show(Import $import): ImportResource
    {
        return new ImportResource($import->loadMissing('supplier'));
    }
}
