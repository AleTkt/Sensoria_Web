<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\CompanyActivityLog;
use App\Models\User;

class CompanyController extends Controller
{
    public function edit(Request $request): View
    {
        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);

        return view('company.edit', [
            'company' => $company,
        ]);
    }

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

        // Guardar Log de Actividad
        CompanyActivityLog::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'category' => 'AJUSTES',
            'action' => 'Datos de empresa actualizados',
            'description' => 'Se modificó la información general y fiscal del espacio de trabajo.',
        ]);

        return redirect()->route('company.edit')->with('success', 'Información de la empresa actualizada.');
    }

    /**
     * Muestra la configuración técnica del espacio y la zona de peligro.
     */
    public function settings(Request $request): View
    {
        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);

        // Obtener el rol del usuario en la empresa activa
        $userRole = $company->pivot->company_role ?? 'MIEMBRO';
        $teamMembers = $company->users()->where('users.id', '!=', Auth::id())->get();

        return view('company.settings', [
            'company' => $company,
            'userRole' => strtoupper($userRole),
            'teamMembers' => $teamMembers,
        ]);
    }

    /**
     * Actualiza las preferencias del espacio (Zona Horaria e Idioma).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timezone' => ['required', 'string'],
            'locale' => ['required', 'string', 'in:es,en'],
        ]);

        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);

        $company->update($validated);

        // Guardar Log de Actividad
        CompanyActivityLog::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'category' => 'AJUSTES',
            'action' => 'Preferencias actualizadas',
            'description' => "Se actualizó la zona horaria a {$validated['timezone']} y el idioma a {$validated['locale']}.",
        ]);

        return redirect()->route('company.settings')->with('success', 'Preferencias del espacio guardadas correctamente.');
    }

    /**
     * Transfiere la propiedad de la empresa a otro miembro.
     */
    public function transferOwnership(Request $request): RedirectResponse
    {
        $request->validate([
            'new_owner_id' => ['required', 'exists:users,id'],
        ]);

        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);

        // Verificar que quien hace la solicitud sea el PROPIETARIO
        if (strtoupper($company->pivot->company_role ?? '') !== 'PROPIETARIO') {
            return back()->with('error', 'Solo el propietario actual puede transferir la empresa.');
        }

        // Asignar rol PROPIETARIO al nuevo usuario
        $company->users()->updateExistingPivot($request->new_owner_id, [
            'company_role' => 'PROPIETARIO',
            'updated_at' => now(),
        ]);

        // Degradado del usuario actual a ADMINISTRADOR
        $company->users()->updateExistingPivot(Auth::id(), [
            'company_role' => 'ADMINISTRADOR',
            'updated_at' => now(),
        ]);

        $newOwner = User::find($request->new_owner_id);

        // Guardar Log de Actividad
        CompanyActivityLog::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'category' => 'SEGURIDAD',
            'action' => 'Transferencia de Propiedad',
            'description' => "La propiedad del espacio fue transferida a {$newOwner->name} ({$newOwner->email}).",
        ]);

        return redirect()->route('company.settings')->with('success', 'Propiedad de la empresa transferida exitosamente.');
    }

    /**
     * Elimina el espacio de trabajo.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);

        if (strtoupper($company->pivot->company_role ?? '') !== 'PROPIETARIO') {
            return back()->with('error', 'Solo el propietario puede eliminar el espacio.');
        }

        $request->validate([
            'confirm_company_name' => ['required', 'string', 'in:' . $company->trade_name],
        ], [
            'confirm_company_name.in' => 'El nombre ingresado no coincide con el nombre de la empresa.',
        ]);

        // Desvincular miembros y eliminar empresa
        $company->users()->detach();
        $company->delete();

        // Limpiar sesión de empresa actual
        session()->forget('current_company_id');

        return redirect()->route('company.select')->with('success', 'El espacio de trabajo ha sido eliminado.');
    }

    /**
     * Muestra el historial de actividad y bitácora de auditoría.
     */
    public function activity(Request $request): View
    {
        $companyId = session('current_company_id');
        $company = Auth::user()->companies()->findOrFail($companyId);

        // Consulta Real a la Base de Datos con Eloquent
        $activities = CompanyActivityLog::with('user')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('company.activity', [
            'company' => $company,
            'activities' => $activities,
        ]);
    }
}
