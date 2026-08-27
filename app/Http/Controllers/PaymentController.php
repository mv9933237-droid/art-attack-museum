<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Sale $sale): JsonResponse
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string|in:efectivo,transferencia',
            'comprobante' => 'nullable|string|max:5000',
        ]);

        $payment = Payment::create([
            'sale_id' => $sale->id,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'comprobante' => $request->comprobante,
            'estado' => Payment::ESTADO_REGISTRADO,
        ]);

        return response()->json([
            'data' => $payment,
        ], 201);
    }

    public function index(Sale $sale): JsonResponse
    {
        $payments = $sale->payments()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $payments,
        ]);
    }
}
