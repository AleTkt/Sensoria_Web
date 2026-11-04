<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CompanySelectionController extends Controller
{
    /**
     * Muestra la pantalla para elegir el entorno de trabajo.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $companies = $user->companies()->wherePivot('is_active', true)->get();

        // Si solo tiene una empresa, no hace falta mostrar la pantalla de selección
        if ($companies->count() <= 1) {
            $companyId = $companies->first()?->id;
            session(['current_company_id' => $companyId]);
            return redirect()->route('dashboard');
        }

        return view('auth.select-company', [
            'companies' => $companies
        ]);
    }

    /**
     * Establece la empresa elegida en la sesión activa.
     */
    public function setCompany(Request $request): RedirectResponse
    {
        $request->validate([
            'company_id' => ['required', 'integer'],
        ]);

        $user = Auth::user();

        // Validar que la empresa realmente pertenezca al usuario autenticado
        $company = $user->companies()->where('companies.id', $request->company_id)->firstOrFail();

        session(['current_company_id' => $company->id]);

        return redirect()->route('dashboard');
    }
}
