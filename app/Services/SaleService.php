<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::findOrFail($data['customer_id']);

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'estado' => Sale::ESTADO_PENDIENTE,
                'moneda' => 'BOB',
            ]);

            foreach ($data['details'] as $detail) {
                $artwork = Artwork::findOrFail($detail['artwork_id']);

                if (! $artwork->isDisponible()) {
                    throw new \InvalidArgumentException(
                        "La obra '{$artwork->titulo}' no está disponible para venta."
                    );
                }

                if ($artwork->naturaleza === Artwork::NATURALEZA_ORIGINAL) {
                    $existingSale = Sale::whereHas('saleDetails', function ($query) use ($artwork) {
                        $query->where('artwork_id', $artwork->id);
                    })
                        ->where('estado', Sale::ESTADO_CONFIRMADA)
                        ->exists();

                    if ($existingSale) {
                        throw new \InvalidArgumentException(
                            "La obra original '{$artwork->titulo}' ya tiene una venta confirmada."
                        );
                    }
                }

                $impuesto = $detail['impuesto'] ?? 0;
                $descuento = $detail['descuento'] ?? 0;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'artwork_id' => $artwork->id,
                    'precio' => $detail['precio'],
                    'impuesto' => $impuesto,
                    'descuento' => $descuento,
                    'subtotal' => $detail['precio'],
                ]);
            }

            $sale->calcularTotales();

            return $sale->load(['customer', 'saleDetails.artwork']);
        });
    }

    public function confirm(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            if ($sale->estado !== Sale::ESTADO_PENDIENTE) {
                throw new \InvalidArgumentException(
                    'Solo se pueden confirmar ventas en estado PENDIENTE.'
                );
            }

            $sale->confirmar();

            foreach ($sale->saleDetails as $detail) {
                $detail->artwork->update(['estado_comercial' => Artwork::ESTADO_VENDIDA]);
            }

            return $sale->load(['customer', 'saleDetails.artwork']);
        });
    }

    public function annul(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            if ($sale->estado !== Sale::ESTADO_CONFIRMADA) {
                throw new \InvalidArgumentException(
                    'Solo se pueden anular ventas en estado CONFIRMADA.'
                );
            }

            $sale->anular();

            foreach ($sale->saleDetails as $detail) {
                $detail->artwork->update(['estado_comercial' => Artwork::ESTADO_DISPONIBLE]);
            }

            return $sale->load(['customer', 'saleDetails.artwork']);
        });
    }
}
