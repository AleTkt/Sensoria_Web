<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Limpiamos espacios en blanco al inicio y final de las cadenas
        $this->merge([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'company_name' => $this->company_name ? trim($this->company_name) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],

            // EL CAMBIO ESTÁ AQUÍ:
            // 1. Quitamos 'dns' de 'email:rfc'
            // 2. Agregamos una expresión regular para forzar que el dominio tenga un punto (ej: .com, .edu.sv)
            'email' => [
                'required',
                'string',
                'email:rfc',
                'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/', // Obliga estructura texto@texto.texto
                'max:255',
                'unique:'.User::class
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'account_type' => ['required', 'in:personal,company'],

            'company_name' => [
                'required_if:account_type,company',
                'nullable',
                'string',
                'min:2',
                'max:255'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido (ejemplo: usuario@dominio.com).',
            'email.regex' => 'El correo debe tener un formato válido con un dominio (ejemplo: @sensoria.com).',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password' => 'La contraseña debe incluir mayúsculas, minúsculas, números y al menos un símbolo.',
            'company_name.required_if' => 'El nombre de la empresa es obligatorio para cuentas B2B.',
        ];
    }
}
