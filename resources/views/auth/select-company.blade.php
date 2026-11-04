@extends('layouts.auth')

@section('title', 'Selecciona tu Entorno | Sensoría IoT')

@section('content')
    <div style="max-width: 520px; margin: 0 auto;">

        <div class="glass-surface" style="padding: 40px 32px; border-radius: 12px;">

            <!-- Header -->
            <div style="text-align: center; margin-bottom: 32px;">
                <h1 style="font-size: 1.65rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 8px;">
                    Selecciona tu Entorno
                </h1>
                <p style="color: #718096; font-size: 0.95rem; margin: 0;">
                    Tu cuenta está asociada a múltiples organizaciones. Elige el espacio al que deseas ingresar.
                </p>
            </div>

            <!-- Formulario de Selección -->
            <form method="POST" action="{{ route('company.select.store') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($companies as $company)
                        @php
                            $role = strtoupper($company->pivot->company_role ?? 'MIEMBRO');
                        @endphp

                        <label class="company-card-option" style="border: 1px solid rgba(27, 61, 26, 0.2); padding: 16px; border-radius: var(--radius-button); cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: rgba(255, 255, 255, 0.7); transition: all 0.2s ease;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <input type="radio" name="company_id" value="{{ $company->id }}" required style="width: 18px; height: 18px; accent-color: var(--color-primary); cursor: pointer;">

                                <div>
                                <span style="display: block; font-weight: 700; color: var(--color-tertiary); font-size: 1rem;">
                                    {{ $company->trade_name }}
                                </span>
                                    <span style="font-size: 0.8rem; color: #718096;">
                                    {{ $company->sector ?? 'Espacio de trabajo' }}
                                </span>
                                </div>
                            </div>

                            <!-- Badge de Rol del usuario en esa empresa -->
                            <span style="background: rgba(88, 148, 69, 0.1); color: var(--color-primary); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                            {{ $role }}
                        </span>
                        </label>
                    @endforeach
                </div>

                @error('company_id')
                <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; display: block; text-align: center;">
                    {{ $message }}
                </span>
                @enderror

                <button type="submit" style="width: 100%; background-color: var(--color-primary); color: white; border: none; padding: 13px; border-radius: var(--radius-button); font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: var(--shadow-soft); margin-top: 12px;">
                    Ingresar al Espacio &rarr;
                </button>
            </form>

        </div>
    </div>
@endsection
