<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanyTeamController extends Controller
{
    /**
     * Muestra la lista de miembros de la empresa actual.
     */
    public function index(Request $request): View
    {
        // 1. Obtener el ID de la empresa activa desde la sesión
        $companyId = session('current_company_id');

        // 2. Validar que la empresa pertenece al usuario logueado
        $company = Auth::user()->companies()->findOrFail($companyId);

        // 3. Obtener todos los usuarios vinculados a esta empresa
        $teamMembers = $company->users;

        return view('company.team.index', [
            'company' => $company,
            'teamMembers' => $teamMembers,
        ]);
    }
}
