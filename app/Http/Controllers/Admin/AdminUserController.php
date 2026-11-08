<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule; // <-- Importación añadida
use App\Services\CorreoService;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('companies')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Usamos Rule::unique llamando al modelo en lugar del string con punto
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'global_role' => ['required', 'in:ADMIN,SOPORTE,CLIENTE'],
        ]);

        // Generamos una contraseña segura aleatoria inicial
        $password = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'global_role' => $request->global_role,
            'is_active' => true,
        ]);

        // Enviar correo de bienvenida con credenciales iniciales
        $asunto = "Bienvenido a Sensoría IoT - Tu cuenta ha sido creada";
        $cuerpo = "
            <h2>Hola, {$user->name}</h2>
            <p>Se ha creado una cuenta para ti en la plataforma Sensoría IoT con el rol de <strong>{$user->global_role}</strong>.</p>
            <p>Tus credenciales de acceso son:</p>
            <ul>
                <li><strong>Correo:</strong> {$user->email}</li>
                <li><strong>Contraseña temporal:</strong> {$password}</li>
            </ul>
            <p>Te recomendamos cambiar tu contraseña inmediatamente después de iniciar sesión.</p>
        ";

        CorreoService::enviarCorreo($user->email, $asunto, $cuerpo);

        return back()->with('success', 'Usuario creado exitosamente. Se han enviado las credenciales a su correo.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Ignoramos el email del propio usuario usando el método ->ignore()
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            'global_role' => ['required', 'in:ADMIN,SOPORTE,CLIENTE'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'global_role' => $request->global_role,
        ]);

        return back()->with('success', 'Los datos del usuario han sido actualizados.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes suspender tu propia cuenta de administrador.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $mensaje = $user->is_active
            ? 'Usuario reactivado. Ya puede acceder a la plataforma.'
            : 'Usuario suspendido. El acceso le ha sido denegado.';

        return back()->with('success', $mensaje);
    }

    public function sendPasswordReset(User $user)
    {
        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Se ha enviado un enlace de restablecimiento al correo del usuario.');
        }

        return back()->with('error', 'No se pudo enviar el enlace de restablecimiento.');
    }
}
