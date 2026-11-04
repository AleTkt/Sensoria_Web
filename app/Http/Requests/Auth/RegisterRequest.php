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
            // Validamos email con TLD obligatorio (requiere .com, .net, etc.)
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:'.User::class],
            // Regla de contraseña estricta (Mínimo 8 caracteres, números, mayúsculas y símbolos)
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
            // Exigimos que el nombre de la empresa no sean solo espacios vacíos si el tipo es company
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
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password' => 'La contraseña debe incluir mayúsculas, minúsculas, números y al menos un símbolo.',
            'company_name.required_if' => 'El nombre de la empresa es obligatorio para cuentas B2B.',
        ];
    }
}
