<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservationService,
    ) {}

    public function index(): JsonResponse
    {
        $reservations = Reservation::with(['artwork', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $reservations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'artwork_id' => 'required|integer|exists:artworks,id',
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        try {
            $reservation = $this->reservationService->create([
                'artwork_id' => $request->artwork_id,
                'customer_id' => $request->customer_id,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => $reservation->load(['artwork', 'customer']),
        ], 201);
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $reservation->load(['artwork', 'customer']);

        return response()->json([
            'data' => $reservation,
        ]);
    }

    public function cancel(Reservation $reservation): JsonResponse
    {
        try {
            $reservation = $this->reservationService->cancel($reservation);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => $reservation->load(['artwork', 'customer']),
        ]);
    }
}
