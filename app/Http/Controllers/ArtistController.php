<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ArtistController extends Controller
{
    public function index(): JsonResponse
    {
        $artists = Artist::noSistema()
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'data' => $artists,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'nacionalidad' => 'required|string|max:255',
            'estado' => 'required|string|in:activo,inactivo',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_fallecimiento' => 'nullable|date|after_or_equal:fecha_nacimiento',
            'biografia' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $artist = Artist::create($validator->validated());

        return response()->json([
            'data' => $artist,
        ], 201);
    }

    public function show(Artist $artist): JsonResponse
    {
        return response()->json([
            'data' => $artist,
        ]);
    }
}
