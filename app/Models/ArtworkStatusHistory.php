<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'artwork_status_history';

    protected $fillable = [
        'artwork_id',
        'estado_anterior',
        'estado_nuevo',
        'responsable',
    ];

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}
