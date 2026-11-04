<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

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

    public function create(): View
    {
        return view('company.team.create');
    }

    /**
     * Procesa la invitación y vincula al usuario a la empresa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $company = Auth::user()->companies()->findOrFail(session('current_company_id'));

        // 1. Buscar si el usuario existe en Sensoría
        $userToInvite = User::where('email', $request->email)->first();

        if (!$userToInvite) {
            return back()->withErrors([
                'email' => 'No encontramos ningún usuario con ese correo. Pídele que cree una cuenta en Sensoría primero.'
            ])->withInput();
        }

        // 2. Verificar si ya pertenece a la empresa
        if ($company->users()->where('users.id', $userToInvite->id)->exists()) {
            return back()->withErrors([
                'email' => 'Este usuario ya es miembro de tu equipo.'
            ])->withInput();
        }

        // 3. Vincular al usuario con rol de TÉCNICO (o el que decidan usar por defecto)
        // Nota: Asegúrate de que tu tabla pivote (customer_account_users) tenga estos campos,
        // o ajusta el attach() a la estructura de tu base de datos.
        $company->users()->attach($userToInvite->id, [
            'company_role' => 'TECNICO',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('company.team')->with('status', 'Miembro agregado exitosamente.');
    }
}
