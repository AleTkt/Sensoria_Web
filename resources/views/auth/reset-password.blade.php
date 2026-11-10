@extends('layouts.auth')

@section('title', 'Nueva Contraseña | Sensoría')

@section('content')
    <div style="display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 20px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); max-width: 450px; width: 100%;">

            <div style="text-align: center; margin-bottom: 24px;">
                <h2 style="margin: 0 0 8px 0; font-size: 1.5rem; color: #1B3D1A;">Crear Nueva Contraseña</h2>
                <p style="color: #64748b; font-size: 0.9rem; margin: 0;">
                    Ingresa y confirma tu nueva contraseña para acceder a tu cuenta.
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Token Oculto de Seguridad -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Correo (Solo lectura) -->
                <div style="margin-bottom: 16px;">
                    <label for="email" style="display: block; font-size: 0.85rem; font-weight: 600; color: #0f172a; margin-bottom: 6px;">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="username"
                           style="width: 100%; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box; background: #f8fafc; color: #64748b; cursor: not-allowed;" readonly>

                    @error('email')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 6px; font-weight: 600;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nueva Contraseña -->
                <div style="margin-bottom: 16px;">
                    <label for="password" style="display: block; font-size: 0.85rem; font-weight: 600; color: #0f172a; margin-bottom: 6px;">Nueva Contraseña</label>
                    <input id="password" type="password" name="password" required autofocus autocomplete="new-password"
                           style="width: 100%; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">

                    @error('password')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 6px; font-weight: 600;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div style="margin-bottom: 24px;">
                    <label for="password_confirmation" style="display: block; font-size: 0.85rem; font-weight: 600; color: #0f172a; margin-bottom: 6px;">Confirmar Contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           style="width: 100%; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">

                    @error('password_confirmation')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 6px; font-weight: 600;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" style="width: 100%; background: #589445; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer;">
                    Restablecer Contraseña
                </button>
            </form>
        </div>
    </div>
@endsection
