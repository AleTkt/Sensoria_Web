<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePaymentMethodRequest extends FormRequest
{
    /**
     * Determine si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Reglas de validación aplicables a la petición.
     */
    public function rules(): array
    {
        return [
            'cardholder_name' => ['required', 'string', 'max:255'],
            'card_number'     => ['required', 'string', 'regex:/^[\d\s\-]{15,22}$/'],
            'exp_month'       => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])$/'],
            'exp_year'        => [
                'required',
                'string',
                'regex:/^20\d{2}$/',
                // Validación para impedir años pasados y validar que la tarjeta no esté vencida
                function ($attribute, $value, $fail) {
                    $currentYear = (int) date('Y');
                    $currentMonth = (int) date('m');
                    $inputYear = (int) $value;
                    $inputMonth = (int) $this->input('exp_month');

                    if ($inputYear < $currentYear) {
                        $fail('La tarjeta está vencida (el año de expiración ya pasó).');
                    } elseif ($inputYear === $currentYear && $inputMonth < $currentMonth) {
                        $fail('La tarjeta está vencida (el mes y año de expiración ya pasaron).');
                    }
                },
            ],
        ];
    }

    /**
     * Mensajes personalizados de error.
     */
    public function messages(): array
    {
        return [
            'cardholder_name.required' => 'El nombre del titular es obligatorio.',
            'card_number.required'     => 'El número de tarjeta es obligatorio.',
            'card_number.regex'        => 'Formato de tarjeta inválido. Ingrese solo números.',
            'exp_month.regex'          => 'El mes de expiración debe ser entre 01 y 12.',
            'exp_year.required'        => 'El año de expiración es obligatorio.',
            'exp_year.regex'           => 'Formato de año inválido (ej. 2028).',
        ];
    }
}
