<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'monto',
        'metodo_pago',
        'comprobante',
        'estado',
    ];

    public const ESTADO_REGISTRADO = 'registrado';

    public const ESTADO_VERIFICADO = 'verificado';

    public const ESTADO_RECHAZADO = 'rechazado';

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
