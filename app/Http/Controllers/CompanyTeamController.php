<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\CompanyActivityLog;
use App\Models\Invitation;
use App\Services\CorreoService;

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
     * Procesa la invitación y envía el correo electrónico al usuario.
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

        // 1. Verificar si el usuario ya es miembro de la empresa
        $userExists = User::where('email', $request->email)->first();
        if ($userExists && $company->users()->where('users.id', $userExists->id)->exists()) {
            return back()->withErrors([
                'email' => 'Este usuario ya es miembro de tu equipo.'
            ])->withInput();
        }

        // 2. Verificar si ya hay una invitación pendiente enviada previamente
        $pendingInvitation = Invitation::where('email', $request->email)
            ->where('company_id', $company->id)
            ->first();
        if ($pendingInvitation) {
            return back()->withErrors([
                'email' => 'Ya se ha enviado una invitación a este correo previamente.'
            ])->withInput();
        }

        // 3. Crear el token único y guardar la invitación en la base de datos
        $token = Str::random(40);

        Invitation::create([
            'email' => $request->email,
            'token' => $token,
            'company_id' => $company->id,
            'role' => $request->role,
        ]);

        // 4. Configurar y enviar el correo (Adaptado de tu lógica de Aurora Nexus)
        $invitationLink = route('invitations.accept', ['token' => $token]);
        $asunto = "Invitación al entorno de " . $company->trade_name . " - Sensoría IoT";

        $cuerpo = '
        <div style="background-color: #f8fafc; color: #334155; font-family: system-ui, -apple-system, sans-serif; padding: 40px 20px;">
            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">

                <!-- Encabezado -->
                <div style="background-color: #0f172a; padding: 25px; text-align: center;">
                    <h2 style="color: #ffffff; margin: 0; font-size: 24px;">Sensoría <span>IoT</span></h2>
                    <span style="color: #cbd5e1; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Invitación de Equipo</span>
                </div>

                <!-- Contenido Principal -->
                <div style="padding: 30px;">
                    <h3 style="color: #0f172a; margin-top: 0;">¡Hola!</h3>
                    <p style="color: #475569; line-height: 1.6;">Has sido invitado a unirte al entorno de trabajo de <strong>' . htmlspecialchars($company->trade_name) . '</strong> en la plataforma Sensoría IoT con el rol de <strong>' . $request->role . '</strong>.</p>

                    <p style="color: #475569; line-height: 1.6;">Para aceptar la invitación y configurar tu cuenta, haz clic en el siguiente botón:</p>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . $invitationLink . '" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block;">Aceptar Invitación</a>
                    </div>

                    <p style="color: #64748b; font-size: 13px;">Si no solicitaste esto o no conoces a esta empresa, puedes ignorar este correo de forma segura. El enlace expirará automáticamente.</p>
                </div>
            </div>
        </div>';

        // Enviar el correo usando tu servicio
        CorreoService::enviarCorreo($request->email, $asunto, $cuerpo);

        // 5. Guardar Log de Actividad
        CompanyActivityLog::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'category' => 'EQUIPO',
            'action' => 'Invitación enviada',
            'description' => "Se envió una invitación a {$request->email} para unirse al espacio de trabajo con el rol de {$request->role}.",
        ]);

        return redirect()->route('company.team')->with('success', 'Invitación enviada exitosamente a ' . $request->email);
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
        $member = $company->users()->findOrFail($id);

        $company->users()->updateExistingPivot($id, [
            'company_role' => $request->role,
            'updated_at' => now(),
        ]);

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

        if ($id == Auth::id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo de la empresa.');
        }

        $member = $company->users()->findOrFail($id);
        $company->users()->detach($id);

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
