<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->words(3, true),
            'descripcion' => fake()->optional()->sentence(),
            'capacidad' => fake()->numberBetween(1, 100),
            'estado' => Location::ESTADO_ACTIVA,
        ];
    }
}
