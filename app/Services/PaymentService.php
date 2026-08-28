<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Sale;

class PaymentService
{
    private const TRANSICIONES_PERMITIDAS = [
        Payment::ESTADO_REGISTRADO => [
            Payment::ESTADO_VERIFICADO,
            Payment::ESTADO_RECHAZADO,
        ],
        Payment::ESTADO_VERIFICADO => [],
        Payment::ESTADO_RECHAZADO => [],
    ];

    public function create(array $data, Sale $sale): Payment
    {
        $this->validatePayment($data, $sale);

        return Payment::create([
            'sale_id' => $sale->id,
            'monto' => $data['monto'],
            'metodo_pago' => $data['metodo_pago'],
            'comprobante' => $data['comprobante'] ?? null,
            'estado' => Payment::ESTADO_REGISTRADO,
        ]);
    }

    public function changeStatus(Payment $payment, string $nuevoEstado): Payment
    {
        if (! $this->puedeTransicionar($payment->estado, $nuevoEstado)) {
            throw new \InvalidArgumentException(
                "Transicion no permitida: {$payment->estado} -> {$nuevoEstado}"
            );
        }

        $payment->update(['estado' => $nuevoEstado]);

        return $payment;
    }

    public function puedeTransicionar(string $estadoActual, string $nuevoEstado): bool
    {
        $permitidos = self::TRANSICIONES_PERMITIDAS[$estadoActual] ?? [];

        return in_array($nuevoEstado, $permitidos);
    }

    private function validatePayment(array $data, Sale $sale): void
    {
        $estadosValidos = [Sale::ESTADO_PENDIENTE, Sale::ESTADO_CONFIRMADA];

        if (! in_array($sale->estado, $estadosValidos, true)) {
            throw new \InvalidArgumentException(
                "No se pueden registrar pagos para una venta en estado {$sale->estado}."
            );
        }

        $monto = (float) $data['monto'];

        if ($monto <= 0) {
            throw new \InvalidArgumentException(
                'El monto del pago debe ser mayor a cero.'
            );
        }

        $totalPagado = $sale->payments()->sum('monto');
        $saldoDisponible = $sale->total - $totalPagado;

        if ($monto > $saldoDisponible) {
            throw new \InvalidArgumentException(
                "El monto excede el saldo pendiente de la venta. Saldo disponible: {$saldoDisponible}."
            );
        }
    }
}
