<?php

namespace App\Services;

use App\Models\Artwork;
use App\Models\Location;
use App\Models\Movement;
use Illuminate\Support\Facades\DB;

class MovementService
{
    public function create(array $data): Movement
    {
        return DB::transaction(function () use ($data) {
            $artwork = Artwork::findOrFail($data['artwork_id']);
            $originLocation = Location::findOrFail($data['origin_location_id']);
            $destinationLocation = Location::findOrFail($data['destination_location_id']);

            if ($originLocation->id === $destinationLocation->id) {
                throw new \InvalidArgumentException(
                    'La ubicación de origen y destino no pueden ser la misma.'
                );
            }

            $movement = Movement::create([
                'artwork_id' => $artwork->id,
                'origin_location_id' => $originLocation->id,
                'destination_location_id' => $destinationLocation->id,
                'fecha' => $data['fecha'],
                'motivo' => $data['motivo'],
                'responsable' => $data['responsable'],
            ]);

            $artwork->update(['current_location_id' => $destinationLocation->id]);

            return $movement;
        });
    }
}
