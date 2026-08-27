<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkArtist extends Model
{
    use HasFactory;

    protected $fillable = [
        'artwork_id',
        'artist_id',
        'tipo_autoria',
    ];

    public const TIPO_CONFIRMADA = 'confirmada';

    public const TIPO_ATRIBUIDA = 'atribuida';

    public function esConfirmada(): bool
    {
        return $this->tipo_autoria === self::TIPO_CONFIRMADA;
    }

    public function esAtribuida(): bool
    {
        return $this->tipo_autoria === self::TIPO_ATRIBUIDA;
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
