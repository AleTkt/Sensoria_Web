<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\CompanyActivityLog;

class CompanyTeamController extends Controller
{
    /**
     * Muestra la lista de miembros de la empresa actual.
     */
    public function index(Request $request): View
    {
        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);
        $teamMembers = $company->users;

        return view('company.team.index', [
            'company' => $company,
            'teamMembers' => $teamMembers,
        ]);
    }

    /**
     * Procesa la invitación y vincula al usuario a la empresa.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'in:ADMINISTRADOR,TECNICO,MIEMBRO'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'role.in' => 'El rol seleccionado no es válido.',
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

        // 3. Vincular al usuario con el rol seleccionado
        $company->users()->attach($userToInvite->id, [
            'company_role' => $request->role,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Guardar Log de Actividad
        CompanyActivityLog::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'category' => 'EQUIPO',
            'action' => 'Invitación de miembro',
            'description' => "Se agregó a {$userToInvite->name} ({$userToInvite->email}) al espacio de trabajo con el rol de {$request->role}.",
        ]);

        return redirect()->route('company.team')->with('success', 'Miembro agregado exitosamente.');
    }

    /**
     * Actualiza el rol de un miembro de la empresa.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'in:PROPIETARIO,ADMINISTRADOR,TECNICO,MIEMBRO'],
        ]);

        $company = Auth::user()->companies()->findOrFail(session('current_company_id'));

        // Evitar cambiar rol si no existe el miembro en la empresa
        $member = $company->users()->findOrFail($id);

        $company->users()->updateExistingPivot($id, [
            'company_role' => $request->role,
            'updated_at' => now(),
        ]);

        // Guardar Log de Actividad
        CompanyActivityLog::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'category' => 'EQUIPO',
            'action' => 'Rol actualizado',
            'description' => "Se actualizó el rol de {$member->name} a {$request->role}.",
        ]);

        return redirect()->route('company.team')->with('success', 'Rol de integrante actualizado.');
    }

    /**
     * Remueve a un usuario de la empresa.
     */
    public function destroy(int $id): RedirectResponse
    {
        $company = Auth::user()->companies()->findOrFail(session('current_company_id'));

        // No permitir que el usuario se elimine a sí mismo desde este panel
        if ($id == Auth::id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo de la empresa.');
        }

        // Obtener datos del miembro antes de desvincularlo para el log
        $member = $company->users()->findOrFail($id);

        $company->users()->detach($id);

        // Guardar Log de Actividad
        CompanyActivityLog::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'category' => 'EQUIPO',
            'action' => 'Miembro removido',
            'description' => "Se removió a {$member->name} ({$member->email}) del espacio de trabajo.",
        ]);

        return redirect()->route('company.team')->with('success', 'El miembro ha sido removido de la empresa.');
    }
}
