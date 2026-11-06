@extends('layouts.public')

@section('title', 'Ajustes de Cuenta | Sensoría IoT')
@section('header', 'Mi Perfil Personal')

@section('content')
    <div style="max-width: 900px; margin: 0 auto; padding-bottom: 40px; display: flex; flex-direction: column; gap: 24px;">

        <!-- Alertas de Éxito Globales -->
        @if (session('status') === 'profile-updated')
            <div style="background-color: rgba(88, 148, 69, 0.1); border: 1px solid var(--color-primary); color: var(--color-tertiary); padding: 12px 16px; border-radius: var(--radius-button); font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="icon-check"></i> Tu información personal ha sido actualizada correctamente.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div style="background-color: rgba(88, 148, 69, 0.1); border: 1px solid var(--color-primary); color: var(--color-tertiary); padding: 12px 16px; border-radius: var(--radius-button); font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="icon-check"></i> Tu contraseña ha sido actualizada de forma segura.
            </div>
        @endif

        <!-- TARJETA 1: Información Personal (Solo Lectura) -->
        <div class="glass-surface" style="padding: 32px; border-radius: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 16px;">
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">
                        Información Personal
                    </h3>
                    <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                        Tus datos básicos de identificación y credenciales de acceso al sistema.
                    </p>
                </div>

                <button type="button" onclick="openProfileModal()" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="icon-edit"></i> Editar Datos
                </button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px;">
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Nombre Completo</span>
                    <span style="font-size: 1.05rem; font-weight: 600; color: var(--color-tertiary);">{{ $user->name }}</span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Correo Electrónico (Acceso)</span>
                    <span style="font-size: 1.05rem; font-weight: 600; color: var(--color-tertiary);">{{ $user->email }}</span>
                </div>
            </div>
        </div>

        <!-- TARJETA 2: Seguridad y Contraseña -->
        <div class="glass-surface" style="padding: 32px; border-radius: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 16px;">
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">
                        Seguridad de la Cuenta
                    </h3>
                    <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                        Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.
                    </p>
                </div>

                <button type="button" onclick="openPasswordModal()" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="icon-lock"></i> Cambiar Contraseña
                </button>
            </div>

            <div style="color: #4a5568; font-size: 0.95rem;">
                <p><strong>Recomendación:</strong> Utiliza letras mayúsculas, minúsculas, números y símbolos especiales (!@#$%) para mayor protección.</p>
            </div>
        </div>

    </div>

    <!-- ==========================================
         MODAL 1: Editar Perfil
         ========================================== -->
    <div id="edit-profile-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass-surface" style="width: 100%; max-width: 500px; padding: 32px; border-radius: 12px; background: #ffffff;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 12px;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Actualizar Información</h3>
                <button type="button" onclick="closeProfileModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #718096;">&times;</button>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('patch')

                <!-- Nombre -->
                <div>
                    <label for="name" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Nombre Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                    @error('name')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                    @error('email')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botones -->
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                    <button type="button" onclick="closeProfileModal()" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 24px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================
         MODAL 2: Cambiar Contraseña
         ========================================== -->
    <div id="change-password-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass-surface" style="width: 100%; max-width: 500px; padding: 32px; border-radius: 12px; background: #ffffff;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 12px;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Actualizar Contraseña</h3>
                <button type="button" onclick="closePasswordModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #718096;">&times;</button>
            </div>

            <form method="POST" action="{{ route('password.update') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('put')

                <!-- Contraseña Actual -->
                <div>
                    <label for="update_password_current_password" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Contraseña Actual</label>
                    <input type="password" id="update_password_current_password" name="current_password" required autocomplete="current-password" style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                    @error('current_password', 'updatePassword')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nueva Contraseña -->
                <div>
                    <label for="update_password_password" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Nueva Contraseña</label>
                    <input type="password" id="update_password_password" name="password" required autocomplete="new-password" style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                    @error('password', 'updatePassword')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="update_password_password_confirmation" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Confirmar Nueva Contraseña</label>
                    <input type="password" id="update_password_password_confirmation" name="password_confirmation" required autocomplete="new-password" style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                </div>

                <!-- Botones -->
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                    <button type="button" onclick="closePasswordModal()" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 24px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Modal de Perfil
        function openProfileModal() {
            document.getElementById('edit-profile-modal').style.display = 'flex';
        }

        function closeProfileModal() {
            document.getElementById('edit-profile-modal').style.display = 'none';
        }

        // Modal de Contraseña
        function openPasswordModal() {
            document.getElementById('change-password-modal').style.display = 'flex';
        }

        function closePasswordModal() {
            document.getElementById('change-password-modal').style.display = 'none';
        }

        // Cerrar modales clicando fuera
        window.addEventListener('click', function(e) {
            const profileModal = document.getElementById('edit-profile-modal');
            const passwordModal = document.getElementById('change-password-modal');

            if (e.target === profileModal) closeProfileModal();
            if (e.target === passwordModal) closePasswordModal();
        });

        // Control dinámico de errores devueltos por Laravel Breeze
        // Laravel por defecto usa ErrorBags para separar la actualización de contraseña ('updatePassword')
        @if ($errors->has('name') || $errors->has('email'))
        openProfileModal();
        @endif

        @if ($errors->updatePassword->any())
        openPasswordModal();
        @endif
    </script>
@endpush
