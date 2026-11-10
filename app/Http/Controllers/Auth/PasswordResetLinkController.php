<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CorreoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', Rule::exists(User::class, 'email')],
        ]);

        // 1. Obtenemos al usuario y le generamos el token oficial de Laravel
        $user = User::where('email', $request->email)->first();
        $token = Password::broker()->createToken($user);

        // 2. Construimos la URL de restablecimiento (usando la ruta de Breeze)
        $url = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // 3. Enviamos el correo con nuestro servicio
        $asunto = "Restablece tu contraseña - Sensoría IoT";

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
        .btn-action { background-color: #589445; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 700; display: inline-block; margin: 10px 0 20px 0; text-align: center; box-shadow: 0 4px 12px rgba(88, 148, 69, 0.2); }
        .url-box { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-top: 20px; word-break: break-all; }
        .url-box p { font-size: 12px; color: #64748b; margin: 0 0 5px 0; font-weight: 600; text-transform: uppercase; }
        .url-box a { font-size: 13px; color: #0284c7; text-decoration: none; font-family: monospace; }
        .email-footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <h1>Sensoría <span>IoT</span></h1>
                <p>Seguridad y Acceso</p>
            </div>
            <div class="email-body">
                <h2>¡Hola, ' . e($user->name) . '!</h2>
                <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta. Si no realizaste esta solicitud, puedes ignorar este correo de forma segura; tu contraseña actual no sufrirá ningún cambio.</p>
                <p>Para establecer una nueva contraseña, haz clic en el siguiente botón:</p>

                <div style="text-align: center;">
                    <a href="' . e($url) . '" class="btn-action">Restablecer Contraseña</a>
                </div>

                <div class="url-box">
                    <p>¿Tienes problemas con el botón? Copia y pega el siguiente enlace en tu navegador:</p>
                    <a href="' . e($url) . '">' . e($url) . '</a>
                </div>
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

        return back()->with('status', 'Te hemos enviado el enlace de restablecimiento a tu correo.');
    }
}
