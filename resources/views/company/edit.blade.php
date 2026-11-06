@extends('layouts.app')

@section('title', 'Mi Empresa | Sensoría IoT')
@section('header', 'Perfil de la Empresa')

@section('content')
    <div style="max-width: 900px; margin: 0 auto; padding-bottom: 40px;">

        <!-- Tarjeta Principal de Solo Lectura -->
        <div class="glass-surface" style="padding: 32px; border-radius: 12px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 16px;">
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">
                        {{ $company->trade_name }}
                    </h3>
                    <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                        Información general y datos fiscales de tu organización.
                    </p>
                </div>

                <!-- Botón de Edición: Solo visible para Propietarios y Administradores -->
                @if(Auth::user()->hasCompanyRole(['PROPIETARIO', 'ADMINISTRADOR']))
                    <button type="button" onclick="openEditModal()" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="icon-edit"></i> Editar Información
                    </button>
                @endif
            </div>

            <!-- Rejilla de Datos de Solo Lectura -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px;">
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Nombre Comercial</span>
                    <span style="font-size: 1.05rem; font-weight: 600; color: var(--color-tertiary);">{{ $company->trade_name ?? 'No especificado' }}</span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Razón Social (Legal)</span>
                    <span style="font-size: 1.05rem; font-weight: 600; color: var(--color-tertiary);">{{ $company->legal_name ?? 'No especificado' }}</span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Sector / Industria</span>
                    <span style="font-size: 1.05rem; font-weight: 600; color: var(--color-tertiary);">{{ $company->sector ?? 'No especificado' }}</span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Teléfono de Contacto</span>
                    <span style="font-size: 1.05rem; font-weight: 600; color: var(--color-tertiary);">{{ $company->phone ?? 'Sin registrar' }}</span>
                </div>

                <div style="grid-column: 1 / -1;">
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Dirección Principal</span>
                    <span style="font-size: 1.05rem; font-weight: 600; color: var(--color-tertiary);">{{ $company->address ?? 'Sin dirección registrada' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal de Edición (Cargado SOLO si tiene permisos) -->
    @if(Auth::user()->hasCompanyRole(['PROPIETARIO', 'ADMINISTRADOR']))
        <div id="company-edit-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
            <div class="glass-surface" style="width: 100%; max-width: 600px; padding: 32px; border-radius: 12px; max-height: 90vh; overflow-y: auto; background: #ffffff;">

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 12px;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Editar Datos de la Empresa</h3>
                    <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #718096;">&times;</button>
                </div>

                <form method="POST" action="{{ route('company.update') }}" style="display: flex; flex-direction: column; gap: 16px;">
                    @csrf
                    @method('PATCH')

                    <!-- Nombre Comercial -->
                    <div>
                        <label for="trade_name" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Nombre Comercial</label>
                        <input type="text" id="trade_name" name="trade_name" value="{{ old('trade_name', $company->trade_name) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                        @error('trade_name')
                        <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Razón Social -->
                    <div>
                        <label for="legal_name" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Razón Social (Legal)</label>
                        <input type="text" id="legal_name" name="legal_name" value="{{ old('legal_name', $company->legal_name) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                        @error('legal_name')
                        <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Sector -->
                    <div>
                        <label for="sector" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Sector / Industria</label>
                        <input type="text" id="sector" name="sector" value="{{ old('sector', $company->sector) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                        @error('sector')
                        <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Teléfono de Contacto</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $company->phone) }}" style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                        @error('phone')
                        <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div>
                        <label for="address" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Dirección Principal</label>
                        <textarea id="address" name="address" rows="3" style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; resize: vertical; box-sizing: border-box;">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                        <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                        <button type="button" onclick="closeEditModal()" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                            Cancelar
                        </button>
                        <button type="submit" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 24px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    @if(Auth::user()->hasCompanyRole(['PROPIETARIO', 'ADMINISTRADOR']))
        <script>
            function openEditModal() {
                const modal = document.getElementById('company-edit-modal');
                modal.style.display = 'flex';
            }

            function closeEditModal() {
                const modal = document.getElementById('company-edit-modal');
                modal.style.display = 'none';
            }

            document.getElementById('company-edit-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });

            @if ($errors->any())
            openEditModal();
            @endif
        </script>
    @endif
@endpush
