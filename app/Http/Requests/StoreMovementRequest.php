<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'artwork_id' => 'required|integer|exists:artworks,id',
            'origin_location_id' => 'required|integer|exists:locations,id',
            'destination_location_id' => 'required|integer|exists:locations,id',
            'fecha' => 'required|date|before_or_equal:today',
            'motivo' => 'required|string|max:5000',
            'responsable' => 'required|string|max:255',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->origin_location_id && $this->destination_location_id
                && $this->origin_location_id === $this->destination_location_id
            ) {
                $validator->errors()->add(
                    'destination_location_id',
                    'La ubicación de origen y destino no pueden ser la misma.'
                );
            }
        });
    }
}
