<?php

namespace Database\Factories;

use App\Models\Exhibition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExhibitionFactory extends Factory
{
    protected $model = Exhibition::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 month', '+6 months');
        $endDate = fake()->dateTimeBetween('+2 months', '+7 months');

        return [
            'nombre' => fake()->unique()->words(3, true),
            'descripcion' => fake()->sentence(),
            'tipo' => fake()->randomElement([Exhibition::TIPO_PHYSICAL, Exhibition::TIPO_VIRTUAL]),
            'url' => null,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'estado' => Exhibition::ESTADO_PROGRAMADA,
        ];
    }

    public function physical(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => Exhibition::TIPO_PHYSICAL,
        ]);
    }

    public function virtual(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => Exhibition::TIPO_VIRTUAL,
            'url' => fake()->url(),
        ]);
    }
}
