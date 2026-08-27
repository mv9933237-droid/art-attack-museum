<?php

namespace Database\Factories;

use App\Models\Artwork;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtworkFactory extends Factory
{
    protected $model = Artwork::class;

    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(3),
            'descripcion' => fake()->paragraph(),
            'naturaleza' => fake()->randomElement([
                Artwork::NATURALEZA_ORIGINAL,
                Artwork::NATURALEZA_REPLICA,
                Artwork::NATURALEZA_REPRODUCCION,
            ]),
            'estado_comercial' => Artwork::ESTADO_DISPONIBLE,
            'dimensiones' => fake()->numerify('##cm x ##cm'),
            'tecnica' => fake()->randomElement(['Óleo', 'Acuarela', 'Escultura', 'Fotografía']),
            'anio_creacion' => fake()->year(),
        ];
    }
}
