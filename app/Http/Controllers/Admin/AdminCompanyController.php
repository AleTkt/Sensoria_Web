<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminCompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with(['users' => function($query) {
            $query->wherePivot('company_role', 'PROPIETARIO');
        }])->latest()->get();

        return view('admin.companies.index', compact('companies'));
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
