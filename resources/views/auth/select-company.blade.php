@extends('layouts.auth')

@section('title', 'Selecciona tu Entorno | Sensoría IoT')

@section('content')
    <div style="max-width: 520px; margin: 0 auto;">

        <div class="glass-surface" style="padding: 40px 32px; border-radius: 12px;">

            <!-- Header -->
            <div style="text-align: center; margin-bottom: 32px;">
                <div style="background: rgba(88, 148, 69, 0.1); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--color-primary);">
                    <i class="fa-solid fa-network-wired" style="font-size: 24px;"></i>
                </div>
                <h1 style="font-size: 1.65rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 8px;">
                    Selecciona tu Entorno
                </h1>
                <p style="color: #718096; font-size: 0.95rem; margin: 0;">
                    Tu cuenta está asociada a múltiples organizaciones. Elige el espacio al que deseas ingresar.
                </p>
            </div>

            <!-- Formulario de Selección -->
            <form method="POST" action="{{ route('company.select.store') }}" class="form-with-loading" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($companies as $company)
                        @php
                            $role = strtoupper($company->pivot->company_role ?? 'MIEMBRO');
                            // Asignamos un icono visual dependiendo del rol o por defecto
                            $roleIcon = $role === 'ADMIN' || $role === 'OWNER' ? 'fa-user-tie' : 'fa-user-group';
                        @endphp

                        <label class="company-card-option" style="border: 1px solid rgba(27, 61, 26, 0.2); padding: 16px; border-radius: var(--radius-button); cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: rgba(255, 255, 255, 0.7); transition: all 0.2s ease;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <input type="radio" name="company_id" value="{{ $company->id }}" required style="width: 18px; height: 18px; accent-color: var(--color-primary); cursor: pointer;">

                                <div style="background: rgba(27, 61, 26, 0.05); padding: 10px; border-radius: 8px; color: var(--color-primary);">
                                    <i class="fa-solid fa-building"></i>
                                </div>

                                <div>
                                    <span style="display: block; font-weight: 700; color: var(--color-tertiary); font-size: 1rem;">
                                        {{ $company->trade_name }}
                                    </span>
                                    <span style="font-size: 0.8rem; color: #718096;">
                                        <i class="fa-solid fa-tag" style="font-size: 0.7rem; margin-right: 4px;"></i>{{ $company->sector ?? 'Espacio de trabajo' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Badge de Rol del usuario en esa empresa -->
                            <span style="background: rgba(88, 148, 69, 0.1); color: var(--color-primary); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid {{ $roleIcon }}"></i> {{ $role }}
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('company_id')
                <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </span>
                @enderror

                <button type="submit" class="btn-submit-loading" style="width: 100%; background-color: var(--color-primary); color: white; border: none; padding: 13px; border-radius: var(--radius-button); font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: var(--shadow-soft); margin-top: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    Ingresar al Espacio <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </form>

        </div>
    </div>
@endsection
