<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExhibitionRequest;
use App\Models\Exhibition;
use App\Services\ExhibitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExhibitionController extends Controller
{
    public function __construct(
        private readonly ExhibitionService $exhibitionService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $exhibitions = Exhibition::withCount('artworks')
            ->orderBy('start_date', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($exhibitions);
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

    public function changeStatus(Request $request, Exhibition $exhibition): JsonResponse
    {
        $request->validate([
            'estado' => 'required|string|in:programada,en_curso,finalizada,cancelada',
        ]);

        try {
            $exhibition = $this->exhibitionService->changeStatus(
                $exhibition,
                $request->estado,
            );
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'estado' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'data' => $exhibition,
        ]);
    }

    public function artworks(Exhibition $exhibition): JsonResponse
    {
        $artworks = $exhibition->artworks()->with(['artists', 'location'])->paginate(15);

        return response()->json($artworks);
    }
}
