<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CompanyController extends Controller
{
    /**
     * Muestra el formulario para editar la empresa actual.
     */
    public function edit(Request $request): View
    {
        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);

        return view('company.edit', [
            'company' => $company,
        ]);
    }

    /**
     * Actualiza la información de la empresa.
     */
    public function update(Request $request): RedirectResponse
    {
        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);

        $validated = $request->validate([
            'legal_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['required', 'string', 'max:255'],
            'sector' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $company->update($validated);

        return redirect()->route('company.edit')->with('status', 'company-updated');
    }
}
