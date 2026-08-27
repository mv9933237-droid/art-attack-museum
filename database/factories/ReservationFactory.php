<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'artwork_id' => Artwork::factory(),
            'customer_id' => Customer::factory(),
            'estado' => Reservation::ESTADO_ACTIVA,
        ];
    }
}
