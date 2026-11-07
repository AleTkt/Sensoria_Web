<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStaffRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificamos que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Verificamos que su rol global sea parte del Staff
        if (!in_array(Auth::user()->global_role, ['ADMIN', 'SOPORTE'])) {
            // Si es un cliente o tiene otro rol no autorizado, bloqueamos el acceso
            abort(403, 'Acceso denegado. Esta área es exclusiva para el Staff de Sensoría.');
        }

        return $next($request);
    }
}
