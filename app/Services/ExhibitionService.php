<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\Exhibition;
use App\Models\ExhibitionArtwork;

class ExhibitionService
{
    public function create(array $data): Exhibition
    {
        return Exhibition::create($data);
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
