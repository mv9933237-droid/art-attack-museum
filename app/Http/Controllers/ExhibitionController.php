<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExhibitionRequest;
use App\Models\Exhibition;
use Illuminate\Http\JsonResponse;

class ExhibitionController extends Controller
{
    public function index(): JsonResponse
    {
        $exhibitions = Exhibition::withCount('artworks')
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'data' => $exhibitions,
        ]);
    }

    public function store(StoreExhibitionRequest $request): JsonResponse
    {
        $exhibition = Exhibition::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'url' => $request->url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'estado' => Exhibition::ESTADO_PROGRAMADA,
        ]);

        return response()->json([
            'data' => $exhibition,
        ], 201);
    }

    public function show(Exhibition $exhibition): JsonResponse
    {
        $exhibition->load('artworks');

        return response()->json([
            'data' => $exhibition,
        ]);
    }

    public function update(StoreExhibitionRequest $request, Exhibition $exhibition): JsonResponse
    {
        $exhibition->update($request->only([
            'nombre',
            'descripcion',
            'tipo',
            'url',
            'start_date',
            'end_date',
        ]));

        return response()->json([
            'data' => $exhibition,
        ]);
    }

    public function artworks(Exhibition $exhibition): JsonResponse
    {
        $artworks = $exhibition->artworks()->with(['artists', 'location'])->get();

        return response()->json([
            'data' => $artworks,
        ]);
    }
}
