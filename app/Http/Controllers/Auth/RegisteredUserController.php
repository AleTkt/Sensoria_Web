<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Company;
use App\Models\User;
use App\Models\Hub; // <-- Asegúrate de tener el modelo creado, si no, lo crearemos luego.
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'global_role' => 'CLIENTE',
            ]);

            $companyName = $validated['account_type'] === 'company'
                ? $validated['company_name']
                : 'Hogar de ' . $validated['name'];

            $sector = $validated['account_type'] === 'company' ? 'Comercio' : 'Residencial';

            $company = Company::create([
                'legal_name' => $companyName,
                'trade_name' => $companyName,
                'sector' => $sector,
                'email' => $validated['email'],
                'is_active' => true,
            ]);

            $user->companies()->attach($company->id, [
                'company_role' => 'PROPIETARIO',
                'is_active' => true
            ]);

            DB::table('iot.hubs')->insert([
                'company_id' => $company->id,
                'name' => 'Hub Principal',
                'status' => 'DESCONECTADO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            event(new Registered($user));
            Auth::login($user);

            session(['current_company_id' => $company->id]);
        });

        return redirect(route('dashboard'))
            ->with('success', '¡Registro completado! Tu entorno ha sido creado exitosamente.');
    }
}
