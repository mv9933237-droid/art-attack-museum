<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'details' => 'required|array|min:1',
            'details.*.artwork_id' => 'required|integer|exists:artworks,id',
            'details.*.precio' => 'required|numeric|min:0.01',
            'details.*.impuesto' => 'nullable|numeric|min:0',
            'details.*.descuento' => 'nullable|numeric|min:0',
        ];
    }
}
