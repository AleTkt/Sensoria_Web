<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCompany
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Si el usuario no tiene una empresa activa en sesión, le asignamos la primera disponible
            if (!session()->has('current_company_id')) {
                $defaultCompany = $user->companies()->where('identity.company_user.is_active', true)->first();

                if ($defaultCompany) {
                    session(['current_company_id' => $defaultCompany->id]);
                }
            }
        }

        return $next($request);
    }
}
