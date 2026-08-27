<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'artwork_id',
        'customer_id',
        'estado',
    ];

    public const ESTADO_ACTIVA = 'activa';

    public const ESTADO_CANCELADA = 'cancelada';

    public const ESTADO_CUMPLIDA = 'cumplida';

    public function estaActiva(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
