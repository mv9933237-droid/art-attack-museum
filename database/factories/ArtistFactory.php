<?php

namespace Database\Factories;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Artist>
 */
class ArtistFactory extends Factory
{
    protected $model = Artist::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'nacionalidad' => fake()->country(),
            'estado' => fake()->randomElement([Artist::ESTADO_ACTIVO, Artist::ESTADO_INACTIVO]),
            'fecha_nacimiento' => fake()->optional(0.7)->date('Y-m-d', '2000-01-01'),
            'fecha_fallecimiento' => null,
            'biografia' => fake()->optional(0.6)->text(200),
            'is_system' => false,
        ];
    }

    public function activo(): static
    {
        return $this->state(fn () => ['estado' => Artist::ESTADO_ACTIVO]);
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['estado' => Artist::ESTADO_INACTIVO]);
    }
}
