<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // 1. NUEVO: Redirección prioritaria para el Staff de Sensoría
        if (in_array($user->global_role, ['ADMIN', 'SOPORTE'])) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        // 2. Lógica existente para Clientes B2B
        $companies = $user->companies()->wherePivot('is_active', true)->get();

        if ($companies->count() > 1) {
            return redirect()->route('company.select');
        }

        session(['current_company_id' => $companies->first()?->id]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
