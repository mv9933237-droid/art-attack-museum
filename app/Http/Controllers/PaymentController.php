<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    public function store(Request $request, Sale $sale): JsonResponse
    {
        $data = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string|in:efectivo,transferencia',
            'comprobante' => 'nullable|string|max:5000',
        ]);

        try {
            $payment = $this->paymentService->create($data, $sale);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => $payment,
        ], 201);
    }

    public function index(Sale $sale, Request $request): JsonResponse
    {
        $payments = $sale->payments()->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($payments);
    }

    public function verify(Payment $payment): JsonResponse
    {
        try {
            $payment = $this->paymentService->changeStatus(
                $payment,
                Payment::ESTADO_VERIFICADO,
            );
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'estado' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'data' => $payment,
        ]);
    }

    public function reject(Payment $payment): JsonResponse
    {
        try {
            $payment = $this->paymentService->changeStatus(
                $payment,
                Payment::ESTADO_RECHAZADO,
            );
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'estado' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'data' => $payment,
        ]);
    }
}
