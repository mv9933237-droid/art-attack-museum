<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exhibition extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'url',
        'start_date',
        'end_date',
        'estado',
    ];

    public const TIPO_PHYSICAL = 'physical';

    public const TIPO_VIRTUAL = 'virtual';

    public const ESTADO_PROGRAMADA = 'programada';

    public const ESTADO_EN_CURSO = 'en_curso';

    public const ESTADO_FINALIZADA = 'finalizada';

    public const ESTADO_CANCELADA = 'cancelada';

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function esFisica(): bool
    {
        return $this->tipo === self::TIPO_PHYSICAL;
    }

    public function esVirtual(): bool
    {
        return $this->tipo === self::TIPO_VIRTUAL;
    }

    public function estaActiva(): bool
    {
        return $this->estado === self::ESTADO_EN_CURSO;
    }

    public function artworks(): BelongsToMany
    {
        return $this->belongsToMany(Artwork::class, 'exhibition_artwork')
            ->withTimestamps();
    }

    public function exhibitionArtworks(): HasMany
    {
        return $this->hasMany(ExhibitionArtwork::class);
    }
}
