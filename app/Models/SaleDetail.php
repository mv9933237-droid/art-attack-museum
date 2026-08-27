<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'artwork_id',
        'precio',
        'impuesto',
        'descuento',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'descuento' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function calcularSubtotal(): void
    {
        $subtotal = $this->precio - $this->descuento + $this->impuesto;
        $this->update(['subtotal' => $subtotal]);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}
