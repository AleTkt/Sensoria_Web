@extends('layouts.app')

@section('title', 'Ajustes del Espacio | Sensoría IoT')
@section('header', 'Configuración de la Empresa')

@section('content')

    @php
        // Verificamos de antemano si el usuario tiene permisos de edición
        $canEditSettings = Auth::user()->hasCompanyRole(['PROPIETARIO', 'ADMINISTRADOR']);
    @endphp

    <div style="max-width: 900px; margin: 0 auto; padding-bottom: 40px; display: flex; flex-direction: column; gap: 24px;">

        <!-- TARJETA 1: Información General del Workspace -->
        <div class="glass-surface" style="padding: 32px; border-radius: 12px;">
            <div style="margin-bottom: 20px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 16px;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">
                    Detalles del Espacio
                </h3>
                <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                    Identificador técnico y permisos de tu cuenta en este espacio de trabajo.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase;">Organización</span>
                    <span style="font-size: 1.05rem; font-weight: 600; color: var(--color-tertiary);">{{ $company->trade_name }}</span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase;">Tu Rol en la Empresa</span>
                    <span style="background: rgba(88, 148, 69, 0.1); color: var(--color-primary); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-block; margin-top: 4px;">
                    {{ $userRole }}
                </span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase;">ID de Empresa (Tenant ID)</span>
                    <span style="font-size: 0.95rem; font-family: monospace; color: var(--color-tertiary);">#{{ str_pad($company->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        <!-- TARJETA: Preferencias Técnicas -->
        <div class="glass-surface" style="padding: 32px; border-radius: 12px;">
            <div style="margin-bottom: 20px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 16px;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">
                    Preferencias Regionales
                </h3>
                <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                    Define la zona horaria para el registro de datos de los sensores IoT e idioma base de las notificaciones corporativas.
                </p>
            </div>

            <form method="POST" action="{{ route('company.settings.update') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <!-- Zona Horaria -->
                    <div>
                        <label for="timezone" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Zona Horaria</label>
                        <select name="timezone" id="timezone" {{ $canEditSettings ? '' : 'disabled' }} style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; {{ $canEditSettings ? '' : 'background: #f7fafc; cursor: not-allowed;' }}">
                            <option value="UTC" {{ $company->timezone == 'UTC' ? 'selected' : '' }}>UTC (Tiempo Universal Coordinado)</option>
                            <option value="America/El_Salvador" {{ $company->timezone == 'America/El_Salvador' ? 'selected' : '' }}>América / El Salvador (CST)</option>
                            <option value="America/Mexico_City" {{ $company->timezone == 'America/Mexico_City' ? 'selected' : '' }}>América / Ciudad de México</option>
                            <option value="Europe/Madrid" {{ $company->timezone == 'Europe/Madrid' ? 'selected' : '' }}>Europa / Madrid</option>
                        </select>
                    </div>

                    <!-- Idioma -->
                    <div>
                        <label for="locale" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Idioma Predeterminado</label>
                        <select name="locale" id="locale" {{ $canEditSettings ? '' : 'disabled' }} style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; {{ $canEditSettings ? '' : 'background: #f7fafc; cursor: not-allowed;' }}">
                            <option value="es" {{ $company->locale == 'es' ? 'selected' : '' }}>Español (ES)</option>
                            <option value="en" {{ $company->locale == 'en' ? 'selected' : '' }}>English (US)</option>
                        </select>
                    </div>
                </div>

                <!-- Botón de guardado solo si puede editar -->
                @if($canEditSettings)
                    <div style="text-align: right; margin-top: 12px;">
                        <button type="submit" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 24px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                            Guardar Preferencias
                        </button>
                    </div>
                @endif
            </form>
        </div>

        <!-- TARJETA 2: Zona de Peligro (Solo Propietarios) -->
        @if(Auth::user()->hasCompanyRole('PROPIETARIO'))
            <div class="glass-surface" style="padding: 32px; border-radius: 12px; border: 1px solid rgba(229, 62, 62, 0.3);">
                <div style="margin-bottom: 20px; border-bottom: 1px solid rgba(229, 62, 62, 0.2); padding-bottom: 16px;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #e53e3e; margin: 0;">
                        Zona de Peligro
                    </h3>
                    <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                        Acciones irreversibles sobre la propiedad y disponibilidad de esta organización.
                    </p>
                </div>

                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- Transferir Propiedad -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="display: block; color: var(--color-tertiary); font-size: 0.95rem;">Transferir Propiedad</strong>
                            <span style="color: #718096; font-size: 0.85rem;">Asigna el rol de propietario a otro miembro del equipo.</span>
                        </div>
                        <button type="button" onclick="openTransferModal()" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 8px 16px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                            Transferir
                        </button>
                    </div>

                    <div style="height: 1px; background: rgba(0,0,0,0.06);"></div>

                    <!-- Eliminar Espacio -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="display: block; color: #e53e3e; font-size: 0.95rem;">Eliminar Espacio de Trabajo</strong>
                            <span style="color: #718096; font-size: 0.85rem;">Elimina permanentemente esta empresa y desvincula a todos los miembros.</span>
                        </div>
                        <button type="button" onclick="openDestroyModal()" style="background-color: #e53e3e; color: white; border: none; padding: 8px 16px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                            Eliminar Empresa
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- Modales de Zona de Peligro (Cargados SOLO si es Propietario) -->
    @if(Auth::user()->hasCompanyRole('PROPIETARIO'))
        <!-- MODAL 1: Transferir Propiedad -->
        <div id="transfer-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
            <div class="glass-surface" style="width: 100%; max-width: 480px; padding: 32px; border-radius: 12px; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 12px;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Transferir Propiedad</h3>
                    <button type="button" onclick="closeTransferModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #718096;">&times;</button>
                </div>

                <form method="POST" action="{{ route('company.transfer') }}" style="display: flex; flex-direction: column; gap: 16px;">
                    @csrf
                    <div>
                        <label for="new_owner_id" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Selecciona el Nuevo Propietario</label>
                        <select id="new_owner_id" name="new_owner_id" required style="width: 100%; padding: 10px; border: 1px solid rgba(27,61,26,0.2); border-radius: var(--radius-button); outline: none;">
                            <option value="" disabled selected>Selecciona un integrante...</option>
                            @foreach($teamMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                        <button type="button" onclick="closeTransferModal()" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">Cancelar</button>
                        <button type="submit" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">Confirmar Transferencia</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: Eliminar Empresa -->
        <div id="destroy-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
            <div class="glass-surface" style="width: 100%; max-width: 480px; padding: 32px; border-radius: 12px; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid rgba(229, 62, 62, 0.2); padding-bottom: 12px;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: #e53e3e; margin: 0;">¿Eliminar Espacio de Trabajo?</h3>
                    <button type="button" onclick="closeDestroyModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #718096;">&times;</button>
                </div>

                <p style="color: #4a5568; font-size: 0.9rem; line-height: 1.5; margin-bottom: 16px;">
                    Para confirmar la eliminación, escribe exactamente el nombre comercial de la empresa: <strong style="color: var(--color-tertiary);">{{ $company->trade_name }}</strong>
                </p>

                <form method="POST" action="{{ route('company.destroy') }}" style="display: flex; flex-direction: column; gap: 16px;">
                    @csrf
                    @method('DELETE')

                    <div>
                        <input type="text" name="confirm_company_name" required placeholder="{{ $company->trade_name }}" style="width: 100%; padding: 10px; border: 1px solid rgba(27,61,26,0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                        @error('confirm_company_name')
                        <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                        <button type="button" onclick="closeDestroyModal()" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">Cancelar</button>
                        <button type="submit" style="background-color: #e53e3e; color: white; border: none; padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">Sí, Eliminar Espacio</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    @if(Auth::user()->hasCompanyRole('PROPIETARIO'))
        <script>
            function openTransferModal() { document.getElementById('transfer-modal').style.display = 'flex'; }
            function closeTransferModal() { document.getElementById('transfer-modal').style.display = 'none'; }
            function openDestroyModal() { document.getElementById('destroy-modal').style.display = 'flex'; }
            function closeDestroyModal() { document.getElementById('destroy-modal').style.display = 'none'; }

            window.addEventListener('click', function(e) {
                if (e.target === document.getElementById('transfer-modal')) closeTransferModal();
                if (e.target === document.getElementById('destroy-modal')) closeDestroyModal();
            });

            @if ($errors->has('confirm_company_name'))
            openDestroyModal();
            @endif
        </script>
    @endif
@endpush
