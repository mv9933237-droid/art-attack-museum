<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'capacidad',
        'estado',
    ];

    public const ESTADO_ACTIVA = 'activa';

    public const ESTADO_INACTIVA = 'inactiva';

    public function isActive(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    public function tieneCapacidad(): bool
    {
        return $this->capacidad > 0;
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class, 'current_location_id');
    }

    public function movementsAsOrigin(): HasMany
    {
        return $this->hasMany(Movement::class, 'origin_location_id');
    }

    public function movementsAsDestination(): HasMany
    {
        return $this->hasMany(Movement::class, 'destination_location_id');
    }
}
