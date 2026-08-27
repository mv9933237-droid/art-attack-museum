<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artwork extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titulo',
        'descripcion',
        'naturaleza',
        'estado_comercial',
        'dimensiones',
        'tecnica',
        'anio_creacion',
        'current_location_id',
    ];

    public const NATURALEZA_ORIGINAL = 'original';

    public const NATURALEZA_REPLICA = 'replica';

    public const NATURALEZA_REPRODUCCION = 'reproduccion';

    public const ESTADO_DISPONIBLE = 'disponible';

    public const ESTADO_RESERVADA = 'reservada';

    public const ESTADO_VENDIDA = 'vendida';

    public const ESTADO_NO_DISPONIBLE = 'no_disponible';

    protected function casts(): array
    {
        return [
            'anio_creacion' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function isDisponible(): bool
    {
        return $this->estado_comercial === self::ESTADO_DISPONIBLE;
    }

    public function isReservada(): bool
    {
        return $this->estado_comercial === self::ESTADO_RESERVADA;
    }

    public function isVendida(): bool
    {
        return $this->estado_comercial === self::ESTADO_VENDIDA;
    }

    public function cambiarEstado(string $nuevoEstado): void
    {
        $this->update(['estado_comercial' => $nuevoEstado]);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'artwork_artists')
            ->withPivot('tipo_autoria')
            ->withTimestamps();
    }

    public function artworkArtists(): HasMany
    {
        return $this->hasMany(ArtworkArtist::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function exhibitions(): BelongsToMany
    {
        return $this->belongsToMany(Exhibition::class, 'exhibition_artwork')
            ->withTimestamps();
    }

    public function exhibitionArtworks(): HasMany
    {
        return $this->hasMany(ExhibitionArtwork::class);
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ArtworkStatusHistory::class);
    }
}
