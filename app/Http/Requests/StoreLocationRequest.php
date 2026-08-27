<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:locations,nombre',
            'descripcion' => 'nullable|string|max:5000',
            'capacidad' => 'required|integer|min:0',
        ];
    }
}
