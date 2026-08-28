<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Models\Artwork;
use App\Models\Movement;
use App\Services\MovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MovementController extends Controller
{
    public function __construct(
        private readonly MovementService $movementService,
    ) {}

    public function store(StoreMovementRequest $request): JsonResponse
    {
        try {
            $movement = $this->movementService->create($request->validated());
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'destination_location_id' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'data' => $movement->load(['artwork', 'originLocation', 'destinationLocation']),
        ], 201);
    }

    public function history(Artwork $artwork): JsonResponse
    {
        $movements = Movement::where('artwork_id', $artwork->id)
            ->with(['originLocation', 'destinationLocation'])
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $movements,
        ]);
    }
}
