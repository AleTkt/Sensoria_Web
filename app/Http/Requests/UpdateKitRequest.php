<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el ID del kit de la URL para ignorarlo en la validación de unicidad
        $kitId = $this->route('id');

        return [
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.commerce.kits', 'sku')->ignore($kitId),
            ],
            'name'          => 'required|string|max:255',
            'target_sector' => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'is_active'     => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique'   => 'Este SKU ya está registrado en otro kit.',
            'name.required' => 'El nombre del kit es obligatorio.',
            'target_sector.required' => 'Debes especificar el sector objetivo.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric'  => 'El precio debe ser un número válido.',
            'price.min'      => 'El precio no puede ser negativo.',
        ];
    }
}
