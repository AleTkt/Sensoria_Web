<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCompanyTeamController extends Controller
{
    public function store(Request $request, Company $company)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:PROPIETARIO,ADMINISTRADOR,TECNICO,MIEMBRO',
        ]);

        // Verificar si el usuario ya pertenece a esta empresa
        if ($company->users()->where('users.id', $request->user_id)->exists()) {
            return back()->withErrors(['team_error' => 'El usuario ya es miembro de esta empresa.']);
        }

        // Asignar el usuario a la empresa
        $company->users()->attach($request->user_id, [
            'company_role' => $request->role,
            'is_active' => true,
        ]);

        return back()->with('status', 'Usuario agregado al equipo de la empresa exitosamente.');
    }

    public function update(Request $request, Company $company, User $user)
    {
        $request->validate([
            'role' => 'required|in:PROPIETARIO,ADMINISTRADOR,TECNICO,MIEMBRO',
        ]);

        // Actualizar el rol en la tabla pivote
        $company->users()->updateExistingPivot($user->id, [
            'company_role' => $request->role,
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Rol actualizado correctamente.');
    }

    public function destroy(Company $company, User $user)
    {
        // Evitar que una empresa se quede sin al menos un PROPIETARIO (opcional, pero recomendado)
        $isOwner = $company->users()->wherePivot('company_role', 'PROPIETARIO')->where('users.id', $user->id)->exists();
        $totalOwners = $company->users()->wherePivot('company_role', 'PROPIETARIO')->count();

        if ($isOwner && $totalOwners <= 1) {
            return back()->withErrors(['team_error' => 'No puedes eliminar al único propietario de la empresa. Asigna otro propietario primero.']);
        }

        $company->users()->detach($user->id);

        return back()->with('status', 'Usuario removido de la empresa.');
    }
}
