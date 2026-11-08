<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCompanyController extends Controller
{
    public function index()
    {
        // Traemos a TODOS los usuarios asociados a la empresa para poder listarlos en el modal
        $companies = Company::with('users')->latest()->get();

        $clientes = User::where('global_role', 'CLIENTE')->orderBy('name')->get();

        return view('admin.companies.index', compact('companies', 'clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trade_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:255',
            'nrc' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'owner_id' => 'required|exists:users,id',
        ]);

        $company = Company::create($validated);

        // Vinculamos al usuario seleccionado como PROPIETARIO en la tabla pivote
        $company->users()->attach($request->owner_id, [
            'company_role' => 'PROPIETARIO',
            'is_active' => true,
        ]);

        return back()->with('status', 'Empresa registrada y propietario asignado exitosamente.');
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'trade_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:255',
            'nrc' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $company->update($validated);

        return back()->with('status', 'Datos de la empresa actualizados correctamente.');
    }

    public function toggleStatus(Request $request, Company $company)
    {
        $company->is_active = !$company->is_active;
        $company->save();

        $mensaje = $company->is_active
            ? 'La empresa ha sido reactivada. Sus usuarios ya pueden acceder al panel B2B.'
            : 'La empresa ha sido suspendida. Se ha bloqueado el acceso a sus usuarios.';

        return back()->with('status', $mensaje);
    }
}
