<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function create(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            $artwork = Artwork::findOrFail($data['artwork_id']);
            $customer = Customer::findOrFail($data['customer_id']);

            if (! $artwork->isDisponible()) {
                throw new \InvalidArgumentException(
                    'La obra no está disponible para reserva.'
                );
            }

            $existingReservation = Reservation::where('artwork_id', $artwork->id)
                ->where('estado', Reservation::ESTADO_ACTIVA)
                ->first();

            if ($existingReservation) {
                throw new \InvalidArgumentException(
                    'La obra ya tiene una reserva activa.'
                );
            }

            $reservation = Reservation::create([
                'artwork_id' => $artwork->id,
                'customer_id' => $customer->id,
                'estado' => Reservation::ESTADO_ACTIVA,
            ]);

            $artwork->update(['estado_comercial' => Artwork::ESTADO_RESERVADA]);

            return $reservation;
        });
    }

    public function cancel(Reservation $reservation): Reservation
    {
        return DB::transaction(function () use ($reservation) {
            if (! $reservation->estaActiva()) {
                throw new \InvalidArgumentException(
                    'Solo se pueden cancelar reservas activas.'
                );
            }

            $reservation->update(['estado' => Reservation::ESTADO_CANCELADA]);

            $reservation->artwork->update(['estado_comercial' => Artwork::ESTADO_DISPONIBLE]);

            return $reservation;
        });
    }
}
