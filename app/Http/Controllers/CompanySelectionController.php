<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CompanySelectionController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $companies = $user->companies()->wherePivot('is_active', true)->get();

        if ($companies->count() <= 1) {
            $companyId = $companies->first()?->id;
            session(['current_company_id' => $companyId]);

            return redirect()->route('dashboard');
        }

        return view('auth.select-company', [
            'companies' => $companies
        ]);
    }

    public function setCompany(Request $request): RedirectResponse
    {
        $request->validate([
            'company_id' => ['required', 'integer'],
        ], [
            // Mensaje personalizado para el Toast si manipulan el HTML
            'company_id.required' => 'Debes seleccionar un espacio de trabajo para continuar.'
        ]);

        $user = Auth::user();

        // Si intentan inyectar un ID de empresa que no les pertenece
        $company = $user->companies()->where('companies.id', $request->company_id)->first();

        if (!$company) {
            return back()->with('error', 'No tienes permiso para acceder a este entorno.');
        }

        session(['current_company_id' => $company->id]);

        // Al cambiar de empresa, les mostramos un Toast informativo
        return redirect()->route('dashboard')
            ->with('info', "Has ingresado al entorno de {$company->trade_name}.");
    }
}
