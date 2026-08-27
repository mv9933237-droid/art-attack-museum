<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Exhibition;
use App\Services\ExhibitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExhibitionArtworkController extends Controller
{
    public function __construct(
        private readonly ExhibitionService $exhibitionService,
    ) {}

    public function store(Request $request, Exhibition $exhibition): JsonResponse
    {
        $request->validate([
            'artwork_id' => 'required|integer|exists:artworks,id',
        ]);

        $artwork = Artwork::findOrFail($request->artwork_id);

        try {
            $exhibitionArtwork = $this->exhibitionService->assignArtwork($exhibition, $artwork);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => $exhibitionArtwork->load(['exhibition', 'artwork']),
        ], 201);
    }

    public function destroy(Exhibition $exhibition, Artwork $artwork): JsonResponse
    {
        $deleted = $this->exhibitionService->removeArtwork($exhibition, $artwork);

        if (! $deleted) {
            return response()->json([
                'message' => 'La obra no está asignada a esta exposición.',
            ], 404);
        }

        return response()->json([
            'message' => 'Obra removida de la exposición.',
        ]);
    }
}
