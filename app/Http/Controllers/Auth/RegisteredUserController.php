<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest; // <- Importamos la nueva Request
use App\Models\Company;
use App\Models\User;
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

            event(new Registered($user));
            Auth::login($user);
        });

        return redirect(route('dashboard'));
    }
}
