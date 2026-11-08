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
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // El LoginRequest de Laravel ya maneja automáticamente el RateLimiting
        // y devuelve el Toast de Error si las credenciales fallan, no hay que tocarlo.
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tu cuenta ha sido suspendida. Contacta a soporte para más información.',
            ]);
        }

        // 1. Redirección prioritaria para el Staff de Sensoría
        if (in_array($user->global_role, ['ADMIN', 'SOPORTE'])) {
            return redirect()->intended(route('admin.dashboard', absolute: false))
                ->with('info', "Has iniciado sesión como {$user->global_role}.");
        }

        // 2. Lógica para Clientes B2B
        $companies = $user->companies()
            ->where('companies.is_active', true)
            ->wherePivot('is_active', true)
            ->get();

        if ($companies->count() > 1) {
            // El usuario tiene varios entornos, le pedimos que seleccione uno
            // No enviamos Toast aquí para no saturar, la vista en sí misma es un paso natural.
            return redirect()->route('company.select');
        }

        // Validamos si no tiene empresas activas (suspendido)
        if ($companies->count() === 0) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Enviamos un Toast de Error explicando por qué no puede entrar
            return redirect('/')->with('error', 'Tu entorno de trabajo se encuentra suspendido temporalmente. Contacta a soporte.');
        }

        // Si solo tiene una empresa, lo asignamos y lo mandamos al dashboard
        session(['current_company_id' => $companies->first()->id]);

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('success', '¡Bienvenido de nuevo, ' . explode(' ', trim($user->name))[0] . '!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Has cerrado sesión correctamente. ¡Hasta pronto!');
    }
}
