<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function showAcceptForm($token)
    {
        // Si el token no existe o ya expiró, el firstOrFail lanzará un 404.
        // Si quisieras ser más amigable, podrías usar first() y redirigir con un Toast de error.
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('login')->with('error', 'El enlace de invitación es inválido o ya ha sido utilizado.');
        }

        return view('auth.accept-invitation', compact('invitation'));
    }

    public function accept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        // El Validator automático inyectará los errores en los Toasts de validación si falla
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $invitation->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'global_role' => 'CLIENTE',
            ]);
        }

        $user->companies()->attach($invitation->company_id, [
            'company_role' => $invitation->role,
            'is_active' => true,
        ]);

        $companyName = $invitation->company->trade_name;

        // Delete the used invitation
        $invitation->delete();

        // Establecemos el entorno en la sesión
        session(['current_company_id' => $invitation->company_id]);

        Auth::login($user);

        // Enviamos el Toast de éxito
        return redirect()->route('dashboard')
            ->with('success', "Te has unido exitosamente al espacio de trabajo de {$companyName}.");
    }
}
