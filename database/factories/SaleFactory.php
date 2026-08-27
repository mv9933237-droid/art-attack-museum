<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'customer_id' => CustomerFactory::new(),
            'estado' => Sale::ESTADO_PENDIENTE,
            'subtotal' => 0,
            'impuesto_total' => 0,
            'descuento_total' => 0,
            'total' => 0,
            'moneda' => 'BOB',
        ];
    }
}
