<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'sale_id' => SaleFactory::new(),
            'monto' => fake()->randomFloat(2, 10, 1000),
            'metodo_pago' => fake()->randomElement(['efectivo', 'transferencia']),
            'comprobante' => fake()->optional()->sentence(),
            'estado' => Payment::ESTADO_REGISTRADO,
        ];
    }
}
