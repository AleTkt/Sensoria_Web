<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Company;
use App\Models\User;
use App\Services\CorreoService; // <-- Importamos tu servicio
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $company = null; // Variables para usarlas fuera del scope de la transacción
        $user = null;

        DB::transaction(function () use ($validated, &$company, &$user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'global_role' => 'CLIENTE',
            ]);

            $companyName = $validated['account_type'] === 'company'
                ? $validated['company_name']
                : 'Hogar de ' . $validated['name'];

            $sector = $validated['account_type'] === 'company' ? 'Comercio' : 'Residencial';

            $company = Company::create([
                'legal_name' => $companyName,
                'trade_name' => $companyName,
                'sector' => $sector,
                'email' => $validated['email'],
                'is_active' => true,
            ]);

            $user->companies()->attach($company->id, [
                'company_role' => 'PROPIETARIO',
                'is_active' => true
            ]);

            DB::table('iot.hubs')->insert([
                'company_id' => $company->id,
                'name' => 'Hub Principal',
                'status' => 'DESCONECTADO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            event(new Registered($user));
            Auth::login($user);

            session(['current_company_id' => $company->id]);
        });

        // =========================================================================
        // ENVÍO DE CORREO DE BIENVENIDA (ESTILO SENSORÍA)
        // =========================================================================
        if ($user && $company) {
            $primerNombre = explode(' ', trim($user->name))[0];
            $asunto = "¡Bienvenido a Sensoría IoT, {$primerNombre}!";

            $cuerpo = '
            <div style="background-color: #f8fafc; color: #334155; font-family: system-ui, -apple-system, sans-serif; padding: 40px 20px;">
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <div style="background-color: #1B3D1A; padding: 25px; text-align: center;">
                        <h2 style="color: #ffffff; margin: 0; font-size: 24px;">Sensoría <span style="color: #589445;">IoT</span></h2>
                        <span style="color: #cbd5e1; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Registro Exitoso</span>
                    </div>
                    <div style="padding: 30px;">
                        <h3 style="color: #1B3D1A; margin-top: 0; font-size: 20px;">¡Hola, ' . htmlspecialchars($primerNombre) . '!</h3>
                        <p style="color: #475569; line-height: 1.6;">Bienvenido a Sensoría IoT. Tu entorno de trabajo <strong>' . htmlspecialchars($company->trade_name) . '</strong> ha sido configurado exitosamente y ya está listo para operar.</p>

                        <div style="background: #f8fafc; border-left: 4px solid #589445; padding: 15px 20px; margin: 25px 0; border-radius: 0 8px 8px 0;">
                            <h4 style="margin: 0 0 10px 0; color: #1B3D1A; font-size: 16px;">¿Qué hemos preparado para ti?</h4>
                            <ul style="margin: 0; padding-left: 20px; color: #475569; font-size: 14px; line-height: 1.5;">
                                <li><strong>Tu Workspace:</strong> Un panel de control aislado y seguro exclusivo para tu empresa.</li>
                                <li><strong>Hub Principal:</strong> Hemos asignado un Hub digital a tu cuenta (actualmente en estado <em>Desconectado</em>).</li>
                                <li><strong>Catálogo B2B:</strong> Acceso completo a nuestra tienda de sensores y kits modulares.</li>
                            </ul>
                        </div>

                        <p style="color: #475569; line-height: 1.6;">Para comenzar a monitorizar, visita nuestro catálogo, adquiere tus primeros dispositivos y utiliza el <strong>código de activación</strong> de tu pedido en tu Workspace para despertar tu Hub.</p>

                        <div style="text-align: center; margin: 30px 0;">
                            <a href="' . route('dashboard') . '" style="background-color: #589445; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block;">Ir a mi Panel de Control</a>
                        </div>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 20px; color: #94a3b8; font-size: 12px;">
                    &copy; ' . date('Y') . ' Sensoría IoT. Todos los derechos reservados.<br>
                    Este es un correo automático, por favor no respondas a esta dirección.
                </div>
            </div>';

            CorreoService::enviarCorreo($user->email, $asunto, $cuerpo);
        }

        return redirect(route('dashboard'))
            ->with('success', '¡Registro completado! Tu entorno ha sido creado exitosamente.');
    }
}
