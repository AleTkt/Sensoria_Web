<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Ignoramos esta restricción si es el Staff, para que puedan moverse libremente
            if (in_array($user->global_role, ['ADMIN', 'SOPORTE'])) {
                return $next($request);
            }

            // A. Si ya tiene una empresa en sesión, auditamos que siga activa
            if (session()->has('current_company_id')) {
                $activeCompany = $user->companies()
                    ->where('companies.id', session('current_company_id'))
                    ->where('companies.is_active', true) // ¿Sigue activa globalmente?
                    ->wherePivot('is_active', true)      // ¿Sigue siendo usuario válido ahí?
                    ->first();

                if (!$activeCompany) {
                    // Si se suspendió, le quitamos la sesión de la empresa y lo bloqueamos
                    session()->forget('current_company_id');
                    abort(403, 'El acceso a esta empresa ha sido suspendido por la administración de Sensoría.');
                }
            }
            // B. Si no tiene empresa en sesión, le asignamos la primera DISPONIBLE y ACTIVA
            else {
                $defaultCompany = $user->companies()
                    ->where('companies.is_active', true)
                    ->wherePivot('is_active', true)
                    ->first();

                if ($defaultCompany) {
                    session(['current_company_id' => $defaultCompany->id]);
                } else {
                    abort(403, 'No tienes ninguna empresa activa asignada en este momento.');
                }
            }
        }

        return $next($request);
    }
}
