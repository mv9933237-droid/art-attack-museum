<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtworkRequest;
use App\Http\Requests\UpdateArtworkRequest;
use App\Models\Artwork;
use App\Services\ArtworkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ArtworkController extends Controller
{
    public function __construct(
        private readonly ArtworkService $artworkService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Artwork::with(['location', 'artists']);

        if ($request->filled('status')) {
            $query->where('estado_comercial', $request->status);
        }

        if ($request->filled('artist_id')) {
            $query->whereHas('artists', function ($q) use ($request) {
                $q->where('artists.id', $request->artist_id);
            });
        }

        if ($request->filled('location_id')) {
            $query->where('current_location_id', $request->location_id);
        }

        if ($request->filled('search')) {
            $query->where('titulo', 'like', '%'.$request->search.'%');
        }

        $artworks = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($artworks);
    }

    public function store(StoreArtworkRequest $request): JsonResponse
    {
        $artwork = $this->artworkService->create($request->validated());

        return response()->json([
            'data' => $artwork->load(['location', 'artists']),
        ], 201);
    }

    public function show(Artwork $artwork): JsonResponse
    {
        $artwork->load(['location', 'artists', 'statusHistory']);

        return response()->json([
            'data' => $artwork,
        ]);
    }

    public function update(UpdateArtworkRequest $request, Artwork $artwork): JsonResponse
    {
        $artwork = $this->artworkService->update($artwork, $request->validated());

        return response()->json([
            'data' => $artwork->load(['location', 'artists']),
        ]);
    }

    public function destroy(Artwork $artwork): JsonResponse
    {
        $this->artworkService->delete($artwork);

        return response()->json(null, 204);
    }

    public function changeStatus(Request $request, Artwork $artwork): JsonResponse
    {
        $request->validate([
            'estado_comercial' => 'required|string|in:disponible,reservada,vendida,no_disponible',
        ]);

        try {
            $artwork = $this->artworkService->changeStatus(
                $artwork,
                $request->estado_comercial,
                $request->responsable ?? null
            );
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'estado_comercial' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'data' => $artwork,
        ]);
    }

    public function exhibitions(Artwork $artwork): JsonResponse
    {
        $exhibitions = $artwork->exhibitions()->withCount('artworks')->get();

        return response()->json([
            'data' => $exhibitions,
        ]);
    }

    public function status(Artwork $artwork): JsonResponse
    {
        return response()->json([
            'data' => [
                'estado_comercial' => $artwork->estado_comercial,
            ],
        ]);
    }
}
