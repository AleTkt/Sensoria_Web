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
            $isStaff = in_array($user->global_role, ['ADMIN', 'SOPORTE']);

            // 1. Verificamos si hay una empresa en sesión
            if (session()->has('current_company_id')) {
                $companyId = session('current_company_id');

                // Si es Staff, solo validamos que la empresa exista y esté activa globalmente
                if ($isStaff) {
                    $activeCompany = \App\Models\Company::where('id', $companyId)
                        ->where('is_active', true)
                        ->first();
                }
                // Si es cliente, validamos que exista, esté activa y ÉL pertenezca a ella
                else {
                    $activeCompany = $user->companies()
                        ->where('companies.id', $companyId)
                        ->where('companies.is_active', true)
                        ->wherePivot('is_active', true)
                        ->first();
                }

                if (!$activeCompany) {
                    session()->forget('current_company_id');
                    $mensaje = $isStaff ? 'La empresa seleccionada ya no está activa.' : 'El acceso a esta empresa ha sido suspendido.';
                    abort(403, $mensaje);
                }
            }
            // 2. Si no hay empresa en sesión
            else {
                // Si es Staff y NO tiene empresa seleccionada:
                if ($isStaff) {
                    // Evitamos el bucle infinito: Si ya está en la vista de selección o en su panel de administración, lo dejamos pasar.
                    if ($request->routeIs('company.select') || $request->routeIs('company.select.store') || $request->routeIs('admin.*')) {
                        return $next($request);
                    }

                    // Si intenta ir a una ruta de cliente (como /workspace) sin empresa, lo mandamos a elegir.
                    return redirect()->route('company.select')->with('warning', 'Selecciona una empresa para visualizar su Workspace.');
                }

                // Si es cliente, le asignamos su primera empresa disponible
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
