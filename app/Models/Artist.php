<?php

namespace App\Models;

use Database\Factories\ArtistFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nombre', 'apellido', 'nacionalidad', 'estado', 'fecha_nacimiento', 'fecha_fallecimiento', 'biografia'])]
#[Hidden([])]
class Artist extends Model
{
    /** @use HasFactory<ArtistFactory> */
    use HasFactory;

    public const ESTADO_ACTIVO = 'activo';

    public const ESTADO_INACTIVO = 'inactivo';

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_fallecimiento' => 'date',
            'is_system' => 'boolean',
        ];
    }

    public function isSystem(): bool
    {
        return (bool) $this->is_system;
    }

    public function isActive(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function scopeNoSistema(Builder $query): Builder
    {
        return $query->where('is_system', false);
    }

    public static function autorDesconocido(): static
    {
        $artist = static::where('is_system', true)->first();

        if ($artist) {
            return $artist;
        }

        $artist = new static;
        $artist->forceFill([
            'nombre' => 'AUTOR',
            'apellido' => 'DESCONOCIDO',
            'nacionalidad' => 'No especificada',
            'estado' => self::ESTADO_ACTIVO,
            'biografia' => 'Registro especial que representa una autoría históricamente no identificada.',
            'is_system' => true,
        ]);
        $artist->save();

        return $artist;
    }

    public function artworks(): BelongsToMany
    {
        return $this->belongsToMany(Artwork::class, 'artwork_artists')
            ->withPivot('tipo_autoria')
            ->withTimestamps();
    }
}
