@extends('layouts.auth')

@section('title', 'Recuperar Contraseña | Sensoría')

@section('content')
    <div style="display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 20px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); max-width: 450px; width: 100%;">

            <div style="text-align: center; margin-bottom: 24px;">
                <h2 style="margin: 0 0 8px 0; font-size: 1.5rem; color: #1B3D1A;">Recuperar Contraseña</h2>
                <p style="color: #64748b; font-size: 0.9rem; margin: 0; line-height: 1.5;">
                    ¿Olvidaste tu contraseña? No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para que puedas crear una nueva.
                </p>
            </div>

            <!-- Mensaje de éxito al enviar el correo -->
            @if(session('status'))
                <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.85rem; font-weight: 600; border: 1px solid #bbf7d0;">
                    ✅ {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; font-size: 0.85rem; font-weight: 600; color: #0f172a; margin-bottom: 6px;">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           style="width: 100%; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;"
                           placeholder="ejemplo@empresa.com">

                    @error('email')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 6px; font-weight: 600;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <button type="submit" style="width: 100%; background: #589445; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer;">
                    Enviar Enlace de Recuperación
                </button>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="{{ route('login') }}" style="color: #589445; font-size: 0.85rem; text-decoration: none; font-weight: 600;">Volver al inicio de sesión</a>
                </div>
            </form>
        </div>
    </div>
@endsection
