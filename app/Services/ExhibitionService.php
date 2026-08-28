<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\Exhibition;
use App\Models\ExhibitionArtwork;

class ExhibitionService
{
    private const TRANSICIONES_PERMITIDAS = [
        Exhibition::ESTADO_PROGRAMADA => [
            Exhibition::ESTADO_EN_CURSO,
            Exhibition::ESTADO_CANCELADA,
        ],
        Exhibition::ESTADO_EN_CURSO => [
            Exhibition::ESTADO_FINALIZADA,
            Exhibition::ESTADO_CANCELADA,
        ],
        Exhibition::ESTADO_FINALIZADA => [],
        Exhibition::ESTADO_CANCELADA => [],
    ];

    public function create(array $data): Exhibition
    {
        return Exhibition::create($data);
    }

    public function changeStatus(Exhibition $exhibition, string $nuevoEstado): Exhibition
    {
        if (! $this->puedeTransicionar($exhibition->estado, $nuevoEstado)) {
            throw new \InvalidArgumentException(
                "Transicion no permitida: {$exhibition->estado} -> {$nuevoEstado}"
            );
        }

        $exhibition->update(['estado' => $nuevoEstado]);

        return $exhibition;
    }

    public function puedeTransicionar(string $estadoActual, string $nuevoEstado): bool
    {
        $permitidos = self::TRANSICIONES_PERMITIDAS[$estadoActual] ?? [];

        return in_array($nuevoEstado, $permitidos);
    }

    public function assignArtwork(Exhibition $exhibition, Artwork $artwork): ExhibitionArtwork
    {
        if ($exhibition->esFisica() && $this->hasPhysicalOverlap($artwork, $exhibition)) {
            throw new \InvalidArgumentException(
                'La obra no puede participar en dos exposiciones físicas con períodos solapados.'
            );
        }

        $existing = ExhibitionArtwork::where('exhibition_id', $exhibition->id)
            ->where('artwork_id', $artwork->id)
            ->first();

        if ($existing) {
            throw new \InvalidArgumentException(
                'La obra ya está asignada a esta exposición.'
            );
        }

        return ExhibitionArtwork::create([
            'exhibition_id' => $exhibition->id,
            'artwork_id' => $artwork->id,
        ]);
    }

    public function removeArtwork(Exhibition $exhibition, Artwork $artwork): bool
    {
        return ExhibitionArtwork::where('exhibition_id', $exhibition->id)
            ->where('artwork_id', $artwork->id)
            ->delete() > 0;
    }

    private function hasPhysicalOverlap(Artwork $artwork, Exhibition $newExhibition): bool
    {
        $overlappingExhibitions = Exhibition::whereHas('artworks', function ($query) use ($artwork) {
            $query->where('artwork_id', $artwork->id);
        })
            ->where('tipo', Exhibition::TIPO_PHYSICAL)
            ->where('id', '!=', $newExhibition->id)
            ->where(function ($query) use ($newExhibition) {
                $query->whereBetween('start_date', [$newExhibition->start_date, $newExhibition->end_date])
                    ->orWhereBetween('end_date', [$newExhibition->start_date, $newExhibition->end_date])
                    ->orWhere(function ($q) use ($newExhibition) {
                        $q->where('start_date', '<=', $newExhibition->start_date)
                            ->where('end_date', '>=', $newExhibition->end_date);
                    });
            })
            ->exists();

        return $overlappingExhibitions;
    }
}
