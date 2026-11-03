<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company; // <- Agregar esto
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <- Agregar esto
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', 'in:personal,business'],
            'company_name' => ['required_if:account_type,business', 'nullable', 'string', 'max:255'],
        ]);

        // Usamos una transacción para asegurar que ambos registros se creen o ninguno
        DB::transaction(function () use ($request) {
            // 1. Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'global_role' => 'CLIENTE',
            ]);

            // 2. Determinar el nombre del Espacio/Empresa
            $companyName = $request->account_type === 'business'
                ? $request->company_name
                : 'Hogar de ' . $request->name;

            $sector = $request->account_type === 'business' ? 'Comercio' : 'Residencial';

            // 3. Crear el tenant
            $company = Company::create([
                'legal_name' => $companyName,
                'trade_name' => $companyName,
                'sector' => $sector,
                'email' => $request->email,
                'is_active' => true,
            ]);

            // 4. Vincular al usuario con su empresa como PROPIETARIO
            $user->companies()->attach($company->id, [
                'company_role' => 'PROPIETARIO',
                'is_active' => true
            ]);

            event(new Registered($user));
            Auth::login($user);
        });

        // Redirigir al dashboard (ruta que maneja Breeze)
        return redirect(route('dashboard', absolute: false));
    }
}
