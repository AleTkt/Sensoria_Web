<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminCompanyController extends Controller
{
    public function index()
    {
        // Traemos todas las empresas junto con sus usuarios vinculados
        // para poder identificar quién es el PROPIETARIO en la tabla
        $companies = Company::with(['users' => function($query) {
            $query->wherePivot('company_role', 'PROPIETARIO');
        }])->latest()->get();

        return view('admin.companies.index', compact('companies'));
    }
}
