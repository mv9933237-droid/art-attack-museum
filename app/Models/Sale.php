<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'estado',
        'subtotal',
        'impuesto_total',
        'descuento_total',
        'total',
        'moneda',
    ];

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_CONFIRMADA = 'confirmada';

    public const ESTADO_ANULADA = 'anulada';

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'impuesto_total' => 'decimal:2',
            'descuento_total' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function calcularTotales(): void
    {
        $detalles = $this->saleDetails;
        $subtotal = $detalles->sum('subtotal');
        $impuestoTotal = $detalles->sum('impuesto');
        $descuentoTotal = $detalles->sum('descuento');
        $total = $subtotal + $impuestoTotal - $descuentoTotal;

        $this->update([
            'subtotal' => $subtotal,
            'impuesto_total' => $impuestoTotal,
            'descuento_total' => $descuentoTotal,
            'total' => $total,
        ]);
    }

    public function confirmar(): void
    {
        $this->update(['estado' => self::ESTADO_CONFIRMADA]);
    }

    public function anular(): void
    {
        $this->update(['estado' => self::ESTADO_ANULADA]);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
