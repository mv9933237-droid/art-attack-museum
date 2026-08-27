<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;

class SalesController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService,
    ) {}

    public function index(): JsonResponse
    {
        $sales = Sale::with(['customer', 'saleDetails.artwork'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $sales,
        ]);
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->saleService->create($request->validated());
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => $sale,
        ], 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        $sale->load(['customer', 'saleDetails.artwork', 'payments']);

        return response()->json([
            'data' => $sale,
        ]);
    }

    public function confirm(Sale $sale): JsonResponse
    {
        try {
            $sale = $this->saleService->confirm($sale);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => $sale,
        ]);
    }

    public function annul(Sale $sale): JsonResponse
    {
        try {
            $sale = $this->saleService->annul($sale);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => $sale,
        ]);
    }
}
