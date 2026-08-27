<?php

namespace Database\Factories;

use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleDetailFactory extends Factory
{
    protected $model = SaleDetail::class;

    public function definition(): array
    {
        return [
            'sale_id' => SaleFactory::new(),
            'artwork_id' => ArtworkFactory::new(),
            'precio' => fake()->randomFloat(2, 100, 10000),
            'impuesto' => fake()->randomFloat(2, 0, 500),
            'descuento' => fake()->randomFloat(2, 0, 200),
            'subtotal' => 0,
        ];
    }
}
