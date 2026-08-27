<?php

namespace App\Http\Requests;

use App\Models\Artwork;
use Illuminate\Foundation\Http\FormRequest;

class StoreArtworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:5000',
            'naturaleza' => 'required|string|in:'.Artwork::NATURALEZA_ORIGINAL.','.Artwork::NATURALEZA_REPLICA.','.Artwork::NATURALEZA_REPRODUCCION,
            'dimensiones' => 'nullable|string|max:100',
            'tecnica' => 'nullable|string|max:100',
            'anio_creacion' => 'nullable|integer|min:1|max:'.date('Y'),
            'current_location_id' => 'nullable|integer|exists:locations,id',
        ];
    }
}
