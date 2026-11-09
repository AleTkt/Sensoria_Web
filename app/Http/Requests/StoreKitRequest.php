<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKitRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta petición.
     */
    public function authorize(): bool
    {
        // Como ya estás protegido por el middleware 'staff' en la ruta,
        // podemos devolver true aquí.
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.commerce.kits', 'sku')
            ],
            'name'          => 'required|string|max:255',
            'target_sector' => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'is_active'     => 'nullable|boolean',
        ];
    }

    /**
     * Mensajes de error personalizados para cada regla.
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique'   => 'Este SKU ya está registrado. Por favor, usa uno diferente.',
            'sku.max'      => 'El SKU no debe exceder los 255 caracteres.',

            'name.required' => 'El nombre del kit es obligatorio.',

            'target_sector.required' => 'Debes especificar el sector objetivo.',

            'price.required' => 'El precio es obligatorio.',
            'price.numeric'  => 'El precio debe ser un número válido.',
            'price.min'      => 'El precio no puede ser negativo.',
        ];
    }
}
