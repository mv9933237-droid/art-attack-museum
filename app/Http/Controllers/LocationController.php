<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::withCount('artworks')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'data' => $locations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:locations,nombre',
            'descripcion' => 'nullable|string|max:5000',
            'capacidad' => 'required|integer|min:0',
        ]);

        $location = Location::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion ?? null,
            'capacidad' => $request->capacidad,
            'estado' => Location::ESTADO_ACTIVA,
        ]);

        return response()->json([
            'data' => $location,
        ], 201);
    }

    public function show(Location $location): JsonResponse
    {
        $location->load('artworks');

        return response()->json([
            'data' => $location,
        ]);
    }

    public function update(Request $request, Location $location): JsonResponse
    {
        $request->validate([
            'nombre' => 'sometimes|required|string|max:255|unique:locations,nombre,'.$location->id,
            'descripcion' => 'nullable|string|max:5000',
            'capacidad' => 'sometimes|required|integer|min:0',
        ]);

        $location->update($request->only(['nombre', 'descripcion', 'capacidad']));

        return response()->json([
            'data' => $location,
        ]);
    }

    public function artworks(Location $location): JsonResponse
    {
        $artworks = $location->artworks()->with(['artists', 'location'])->get();

        return response()->json([
            'data' => $artworks,
        ]);
    }
}
