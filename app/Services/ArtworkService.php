<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\ArtworkStatusHistory;
use Illuminate\Support\Facades\DB;

class ArtworkService
{
    private const TRANSICIONES_PERMITIDAS = [
        Artwork::ESTADO_DISPONIBLE => [
            Artwork::ESTADO_RESERVADA,
            Artwork::ESTADO_VENDIDA,
            Artwork::ESTADO_NO_DISPONIBLE,
        ],
        Artwork::ESTADO_RESERVADA => [
            Artwork::ESTADO_DISPONIBLE,
            Artwork::ESTADO_VENDIDA,
        ],
        Artwork::ESTADO_VENDIDA => [
            Artwork::ESTADO_DISPONIBLE,
        ],
        Artwork::ESTADO_NO_DISPONIBLE => [
            Artwork::ESTADO_DISPONIBLE,
        ],
    ];

    public function create(array $data): Artwork
    {
        return DB::transaction(function () use ($data) {
            $artwork = Artwork::create([
                'titulo' => $data['titulo'],
                'descripcion' => $data['descripcion'] ?? null,
                'naturaleza' => $data['naturaleza'],
                'estado_comercial' => Artwork::ESTADO_DISPONIBLE,
                'dimensiones' => $data['dimensiones'] ?? null,
                'tecnica' => $data['tecnica'] ?? null,
                'anio_creacion' => $data['anio_creacion'] ?? null,
                'current_location_id' => $data['current_location_id'] ?? null,
            ]);

            return $artwork;
        });
    }

    public function update(Artwork $artwork, array $data): Artwork
    {
        $artwork->update($data);

        return $artwork;
    }

    public function changeStatus(Artwork $artwork, string $nuevoEstado, ?string $responsable = null): Artwork
    {
        if (! $this->puedeTransicionar($artwork->estado_comercial, $nuevoEstado)) {
            throw new \InvalidArgumentException(
                "Transición no permitida: {$artwork->estado_comercial} → {$nuevoEstado}"
            );
        }

        return DB::transaction(function () use ($artwork, $nuevoEstado, $responsable) {
            $estadoAnterior = $artwork->estado_comercial;

            $artwork->update(['estado_comercial' => $nuevoEstado]);

            ArtworkStatusHistory::create([
                'artwork_id' => $artwork->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $nuevoEstado,
                'responsable' => $responsable,
            ]);

            return $artwork;
        });
    }

    public function delete(Artwork $artwork): bool
    {
        return $artwork->delete();
    }

    public function puedeTransicionar(string $estadoActual, string $nuevoEstado): bool
    {
        $permitidos = self::TRANSICIONES_PERMITIDAS[$estadoActual] ?? [];

        return in_array($nuevoEstado, $permitidos);
    }
}
