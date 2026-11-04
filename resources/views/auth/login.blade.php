@extends('layouts.auth')

@section('title', 'Iniciar Sesión | Sensoría IoT')

@section('content')
    <div style="max-width: 440px; margin: 0 auto;">

        <!-- Tarjeta Principal con Glassmorphism -->
        <div class="glass-surface" style="padding: 40px 32px; border-radius: 12px;">

            <!-- Header / Título -->
            <div style="text-align: center; margin-bottom: 32px;">
                <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 8px;">
                    ¡Bienvenido de nuevo!
                </h1>
                <p style="color: #718096; font-size: 0.95rem; margin: 0;">
                    Ingresa tus credenciales para acceder a la plataforma.
                </p>
            </div>

            <!-- Mensaje de Estado de Sesión -->
            @if (session('status'))
                <div style="background-color: rgba(88, 148, 69, 0.1); border: 1px solid var(--color-primary); color: var(--color-tertiary); padding: 12px 16px; border-radius: var(--radius-button); font-size: 0.875rem; margin-bottom: 24px;">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Formulario de Inicio de Sesión -->
            <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf

                <!-- Correo Electrónico -->
                <div>
                    <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">
                        Correo Electrónico
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="tu.correo@empresa.com"
                           style="width: 100%; padding: 11px 14px; border-radius: var(--radius-button); border: 1px solid rgba(27, 61, 26, 0.2); background: rgba(255, 255, 255, 0.85); font-size: 0.95rem; color: var(--color-tertiary); outline: none; box-sizing: border-box;">

                    @error('email')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 6px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label for="password" style="font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary);">
                            Contraseña
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="color: var(--color-primary); font-size: 0.8rem; font-weight: 600; text-decoration: none;">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••"
                           style="width: 100%; padding: 11px 14px; border-radius: var(--radius-button); border: 1px solid rgba(27, 61, 26, 0.2); background: rgba(255, 255, 255, 0.85); font-size: 0.95rem; color: var(--color-tertiary); outline: none; box-sizing: border-box;">

                    @error('password')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 6px; display: block;">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <!-- Recordarme -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    <input id="remember_me"
                           type="checkbox"
                           name="remember"
                           style="width: 16px; height: 16px; accent-color: var(--color-primary); cursor: pointer;">
                    <label for="remember_me" style="font-size: 0.875rem; color: #718096; cursor: pointer; user-select: none;">
                        Mantener sesión iniciada
                    </label>
                </div>

                <!-- Botón de Envío -->
                <button type="submit" style="width: 100%; background-color: var(--color-primary); color: white; border: none; padding: 13px; border-radius: var(--radius-button); font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: var(--shadow-soft); margin-top: 8px;">
                    Iniciar Sesión
                </button>
            </form>

            <!-- Footer de Registro -->
            <div style="text-align: center; margin-top: 28px; padding-top: 20px; border-top: 1px solid rgba(27, 61, 26, 0.08); font-size: 0.875rem; color: #718096;">
                ¿No tienes una cuenta corporativa?
                <a href="{{ route('register') }}" style="color: var(--color-primary); text-decoration: none; font-weight: 700; margin-left: 4px;">
                    Regístrate aquí
                </a>
            </div>

        </div>
    </div>
@endsection
