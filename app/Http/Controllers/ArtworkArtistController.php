<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkArtist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ArtworkArtistController extends Controller
{
    public function index(Artwork $artwork): JsonResponse
    {
        $artists = $artwork->artists;

        return response()->json([
            'data' => $artists,
        ]);
    }

    public function store(Request $request, Artwork $artwork): JsonResponse
    {
        $request->validate([
            'artist_id' => 'required|integer|exists:artists,id',
            'tipo_autoria' => 'required|string|in:confirmada,atribuida',
        ]);

        $artist = Artist::findOrFail($request->artist_id);

        if ($artist->isSystem()) {
            throw ValidationException::withMessages([
                'artist_id' => 'No se puede asignar AUTOR DESCONOCIDO desde este endpoint. Use el endpoint específico.',
            ]);
        }

        $exists = ArtworkArtist::where('artwork_id', $artwork->id)
            ->where('artist_id', $request->artist_id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'artist_id' => 'Este artista ya está asociado a esta obra.',
            ]);
        }

        $artworkArtist = ArtworkArtist::create([
            'artwork_id' => $artwork->id,
            'artist_id' => $request->artist_id,
            'tipo_autoria' => $request->tipo_autoria,
        ]);

        return response()->json([
            'data' => $artworkArtist->load('artist'),
        ], 201);
    }

    public function destroy(Artwork $artwork, Artist $artist): JsonResponse
    {
        $deleted = ArtworkArtist::where('artwork_id', $artwork->id)
            ->where('artist_id', $artist->id)
            ->delete();

        if (! $deleted) {
            return response()->json([
                'message' => 'Esta relación no existe.',
            ], 404);
        }

        return response()->json(null, 204);
    }

    public function assignUnknown(Artwork $artwork): JsonResponse
    {
        $autorDesconocido = Artist::autorDesconocido();

        $exists = ArtworkArtist::where('artwork_id', $artwork->id)
            ->where('artist_id', $autorDesconocido->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'data' => $artwork->artists->where('is_system', true)->first(),
                'message' => 'AUTOR DESCONOCIDO ya estaba asociado.',
            ]);
        }

        $artworkArtist = ArtworkArtist::create([
            'artwork_id' => $artwork->id,
            'artist_id' => $autorDesconocido->id,
            'tipo_autoria' => ArtworkArtist::TIPO_CONFIRMADA,
        ]);

        return response()->json([
            'data' => $artworkArtist->load('artist'),
        ], 201);
    }
}
