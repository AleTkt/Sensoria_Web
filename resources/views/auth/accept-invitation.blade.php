<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Aceptar Invitación | Sensoría IoT</title>

    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .auth-container {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 40px;
            box-sizing: border-box;
        }
        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .brand-header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 1.8rem;
        }
        .brand-header h1 span {
            color: var(--color-primary, #2563eb);
        }
        .welcome-text {
            text-align: center;
            color: #475569;
            font-size: 0.95rem;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .welcome-text strong {
            color: #0f172a;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
        }
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--color-primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .form-group input[readonly] {
            background-color: #f1f5f9;
            color: #64748b;
            cursor: not-allowed;
        }
        .error-message {
            color: #dc2626;
            font-size: 0.8rem;
            margin-top: 4px;
            display: block;
        }
        .btn-submit {
            width: 100%;
            background-color: var(--color-primary, #2563eb);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="brand-header">
        <h1>Sensoría <span>IoT</span></h1>
    </div>

    <div class="welcome-text">
        Has sido invitado para unirte al entorno de trabajo de <strong>{{ $invitation->company->trade_name }}</strong> con el rol de <strong>{{ ucfirst(strtolower($invitation->role)) }}</strong>.
    </div>

    <form method="POST" action="{{ route('invitations.process', ['token' => $invitation->token]) }}">
        @csrf

        <!-- Correo (Solo lectura) -->
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" value="{{ $invitation->email }}" readonly>
            <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px; display: block;">La invitación está vinculada a este correo.</small>
        </div>

        <!-- Nombre -->
        <div class="form-group">
            <label for="name">Tu Nombre Completo</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Ej. Juan Pérez">
            @error('name')
            <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <!-- Contraseña -->
        <div class="form-group">
            <label for="password">Crea una Contraseña</label>
            <input type="password" id="password" name="password" required placeholder="Mínimo 8 caracteres">
            @error('password')
            <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <!-- Confirmar Contraseña -->
        <div class="form-group">
            <label for="password_confirmation">Confirma tu Contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repite la contraseña">
        </div>

        <button type="submit" class="btn-submit">
            Completar Registro y Entrar
        </button>
    </form>
</div>

</body>
</html>
