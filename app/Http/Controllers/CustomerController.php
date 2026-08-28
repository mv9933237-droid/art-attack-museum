<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::orderBy('apellido')
            ->paginate(15);

        return response()->json($customers);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'documento' => 'required|string|max:50|unique:customers,documento',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
        ]);

        $customer = Customer::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'documento' => $request->documento,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'estado' => Customer::ESTADO_ACTIVO,
        ]);

        return response()->json([
            'data' => $customer,
        ], 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'data' => $customer,
        ]);
    }

    public function sales(Customer $customer): JsonResponse
    {
        $sales = $customer->sales()
            ->with(['saleDetails.artwork', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($sales);
    }
}
