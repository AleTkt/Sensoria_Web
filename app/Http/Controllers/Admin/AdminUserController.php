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

        $cuerpo = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0; color: #334155; }
        .email-wrapper { max-width: 600px; margin: 0 auto; padding: 20px; }
        .email-card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .email-header { background-color: #111827; padding: 30px; text-align: center; border-bottom: 3px solid #589445; }
        .email-header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px; }
        .email-header h1 span { color: #589445; }
        .email-header p { color: #9ca3af; margin: 8px 0 0 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .email-body { padding: 30px; background-color: #ffffff; }
        .email-body h2 { color: #1e293b; font-size: 20px; margin-top: 0; margin-bottom: 16px; }
        .email-body p { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .credentials-box { background-color: #f8fafc; border-left: 4px solid #589445; padding: 20px; border-radius: 0 8px 8px 0; margin: 24px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .credentials-box h3 { margin-top: 0; color: #1e293b; font-size: 15px; font-weight: 700; margin-bottom: 12px; }
        .credential-item { font-size: 14px; margin-bottom: 8px; color: #334155; }
        .credential-item strong { color: #1e293b; min-width: 140px; display: inline-block; }
        .credential-item span { font-family: monospace; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; color: #0f172a; font-weight: 600; }
        .role-badge { display: inline-block; background: rgba(88, 148, 69, 0.12); color: #166534; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 800; border: 1px solid rgba(88, 148, 69, 0.3); }
        .btn-action { background-color: #589445; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 700; display: inline-block; margin: 10px 0 10px 0; text-align: center; box-shadow: 0 4px 12px rgba(88, 148, 69, 0.2); }
        .email-footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <h1>Sensoría <span>IoT</span></h1>
                <p>Registro y Credenciales de Acceso</p>
            </div>
            <div class="email-body">
                <h2>¡Hola, ' . e($user->name) . '!</h2>
                <p>Se ha creado una cuenta para ti en la plataforma Sensoría IoT con el rol asignado de <span class="role-badge">' . e($user->global_role) . '</span>.</p>
                <p>Tus credenciales de acceso iniciales son las siguientes:</p>

                <div class="credentials-box">
                    <h3>Credenciales Temporales</h3>
                    <div class="credential-item"><strong>Correo:</strong> ' . e($user->email) . '</div>
                    <div class="credential-item"><strong>Contraseña:</strong> <span>' . e($password) . '</span></div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="' . url('/login') . '" class="btn-action">Iniciar Sesión en Sensoría</a>
                </div>

                <p style="margin-top: 25px; font-size: 13px; color: #64748b;">Te recomendamos cambiar tu contraseña inmediatamente después de iniciar sesión por primera vez.</p>
            </div>
            <div class="email-footer">
                &copy; ' . date('Y') . ' Sensoría IoT. Todos los derechos reservados.
            </div>
        </div>
    </div>
</body>
</html>
';

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
        // 1. Generamos una nueva contraseña temporal
        $newPassword = Str::random(12);

        // 2. Actualizamos al usuario
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // 3. Enviamos el correo
        $asunto = "Restablecimiento de contraseña - Sensoría IoT";

        $cuerpo = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0; color: #334155; }
        .email-wrapper { max-width: 600px; margin: 0 auto; padding: 20px; }
        .email-card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .email-header { background-color: #111827; padding: 30px; text-align: center; border-bottom: 3px solid #589445; }
        .email-header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px; }
        .email-header h1 span { color: #589445; }
        .email-header p { color: #9ca3af; margin: 8px 0 0 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .email-body { padding: 30px; background-color: #ffffff; }
        .email-body h2 { color: #1e293b; font-size: 20px; margin-top: 0; margin-bottom: 16px; }
        .email-body p { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 20px; }
        .credentials-box { background-color: #f8fafc; border-left: 4px solid #589445; padding: 20px; border-radius: 0 8px 8px 0; margin: 24px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .credentials-box h3 { margin-top: 0; color: #1e293b; font-size: 15px; font-weight: 700; margin-bottom: 12px; }
        .credential-item { font-size: 14px; margin-bottom: 8px; color: #334155; }
        .credential-item strong { color: #1e293b; min-width: 140px; display: inline-block; }
        .credential-item span { font-family: monospace; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; color: #0f172a; font-weight: 600; }
        .btn-action { background-color: #589445; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 700; display: inline-block; margin: 10px 0 10px 0; text-align: center; box-shadow: 0 4px 12px rgba(88, 148, 69, 0.2); }
        .email-footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <h1>Sensoría <span>IoT</span></h1>
                <p>Gestión de Seguridad</p>
            </div>
            <div class="email-body">
                <h2>¡Hola, ' . e($user->name) . '!</h2>
                <p>Un administrador ha restablecido tu contraseña en la plataforma Sensoría IoT.</p>
                <p>Tus nuevas credenciales de acceso son las siguientes:</p>

                <div class="credentials-box">
                    <h3>Credenciales Temporales Actualizadas</h3>
                    <div class="credential-item"><strong>Correo:</strong> ' . e($user->email) . '</div>
                    <div class="credential-item"><strong>Contraseña temporal:</strong> <span>' . e($newPassword) . '</span></div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="' . url('/login') . '" class="btn-action">Iniciar Sesión en Sensoría</a>
                </div>

                <p style="margin-top: 25px; font-size: 13px; color: #64748b;">Por seguridad, te recomendamos cambiar esta contraseña inmediatamente desde tu panel después de iniciar sesión.</p>
            </div>
            <div class="email-footer">
                &copy; ' . date('Y') . ' Sensoría IoT. Todos los derechos reservados.
            </div>
        </div>
    </div>
</body>
</html>
';

        CorreoService::enviarCorreo($user->email, $asunto, $cuerpo);

        return back()->with('success', 'Contraseña restablecida y enviada al correo del usuario exitosamente.');
    }
}
