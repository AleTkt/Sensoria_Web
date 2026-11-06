<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check() || !Auth::user()->hasCompanyRole($roles)) {
            abort(403, 'No tienes permisos suficientes para realizar esta acción en este espacio de trabajo.');
        }

        return $next($request);
    }
}
