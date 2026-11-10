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
        $cuerpo = "
            <h2>Hola, {$user->name}</h2>
            <p>Recibimos una solicitud para restablecer tu contraseña. Si no fuiste tú, puedes ignorar este correo sin problemas.</p>
            <p>Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
            <p><a href='{$url}' style='background: #589445; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>Restablecer Contraseña</a></p>
            <p style='font-size: 0.85em; color: #666;'>O copia y pega esta URL en tu navegador: <br>{$url}</p>
        ";

        CorreoService::enviarCorreo($user->email, $asunto, $cuerpo);

        return back()->with('status', 'Te hemos enviado el enlace de restablecimiento a tu correo.');
    }
}
