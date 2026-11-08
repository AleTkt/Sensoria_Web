<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Aceptar Invitación | Sensoría IoT</title>

    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/invitation.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="auth-container">
    <div class="brand-header">
        <h1>Sensoría <span>IoT</span></h1>
    </div>

    <div class="welcome-text">
        <i class="fa-solid fa-envelope-open-text" style="font-size: 24px; color: var(--color-primary, #2563eb); display: block; margin-bottom: 12px;"></i>
        Has sido invitado para unirte al entorno de trabajo de <strong>{{ $invitation->company->trade_name }}</strong> con el rol de <strong>{{ ucfirst(strtolower($invitation->role)) }}</strong>.
    </div>

    <form method="POST" action="{{ route('invitations.process', ['token' => $invitation->token]) }}">
        @csrf

        <!-- Correo (Solo lectura) -->
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-at"></i>
                <input type="email" id="email" value="{{ $invitation->email }}" readonly>
            </div>
            <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px; display: block;"><i class="fa-solid fa-link" style="font-size: 0.7rem; margin-right: 2px;"></i> La invitación está vinculada a este correo.</small>
        </div>

        <!-- Nombre -->
        <div class="form-group">
            <label for="name">Tu Nombre Completo</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-user"></i>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Ej. Juan Pérez">
            </div>
            @error('name')
            <span class="error-message"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
            @enderror
        </div>

        <!-- Contraseña -->
        <div class="form-group">
            <label for="password">Crea una Contraseña</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-key"></i>
                <input type="password" id="password" name="password" required placeholder="Mínimo 8 caracteres">
            </div>
            @error('password')
            <span class="error-message"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
            @enderror
        </div>

        <!-- Confirmar Contraseña -->
        <div class="form-group">
            <label for="password_confirmation">Confirma tu Contraseña</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repite la contraseña">
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-user-plus"></i> Completar Registro y Entrar
        </button>
    </form>
</div>

</body>
</html>
