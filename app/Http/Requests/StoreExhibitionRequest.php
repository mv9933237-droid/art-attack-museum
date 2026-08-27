<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:5000',
            'tipo' => 'required|in:physical,virtual',
            'url' => 'required_if:tipo,virtual|nullable|url|max:5000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->start_date && $this->end_date && $this->end_date < $this->start_date) {
                $validator->errors()->add(
                    'end_date',
                    'La fecha de fin no puede ser anterior a la fecha de inicio.'
                );
            }
        });
    }
}
