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
            'account_type' => ['required', 'in:personal,company'], // <- Cambiado a company
            'company_name' => ['required_if:account_type,company', 'nullable', 'string', 'max:255'], // <- Cambiado a company
        ]);

        // DB::transaction...
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'global_role' => 'CLIENTE',
            ]);

            $companyName = $request->account_type === 'company'
                ? $request->company_name
                : 'Hogar de ' . $request->name;

            $sector = $request->account_type === 'company' ? 'Comercio' : 'Residencial';

            $company = Company::create([
                'legal_name' => $companyName,
                'trade_name' => $companyName,
                'sector' => $sector,
                'email' => $request->email,
                'is_active' => true,
            ]);

            $user->companies()->attach($company->id, [
                'company_role' => 'PROPIETARIO',
                'is_active' => true
            ]);

            event(new Registered($user));
            Auth::login($user);
        });

        return redirect(route('dashboard'));
    }
}
