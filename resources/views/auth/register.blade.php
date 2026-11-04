@extends('layouts.auth')

@section('title', 'Registro de Cuenta | Sensoría IoT')

@section('content')
    <div class="glass-surface" style="max-width: 520px; margin: 0 auto; padding: 32px; border-radius: 12px;">

        <!-- Indicador de Pasos Nativo -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
            <span id="step-indicator-text" style="font-weight: 600; font-size: 0.875rem; color: var(--color-primary);">Paso 1 de 3</span>
            <div style="display: flex; gap: 8px;">
                <div id="dot-1" class="step-dot" style="width: 10px; height: 10px; border-radius: 50%; background-color: var(--color-primary);"></div>
                <div id="dot-2" class="step-dot" style="width: 10px; height: 10px; border-radius: 50%; background-color: rgba(0,0,0,0.1);"></div>
                <div id="dot-3" class="step-dot" style="width: 10px; height: 10px; border-radius: 50%; background-color: rgba(0,0,0,0.1);"></div>
            </div>
        </div>

        <form id="wizard-form" method="POST" action="{{ route('register') }}">
            @csrf

            <!-- PASO 1: Datos Personales -->
            <div id="step-1" class="wizard-step" style="display: flex; flex-direction: column; gap: 16px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Información Personal</h2>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Ej. Ana María Gómez" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-button); border: 1px solid rgba(27, 61, 26, 0.2); background: rgba(255,255,255,0.85); outline: none; box-sizing: border-box;">
                    @error('name')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="usuario@dominio.com" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-button); border: 1px solid rgba(27, 61, 26, 0.2); background: rgba(255,255,255,0.85); outline: none; box-sizing: border-box;">
                    @error('email')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="button" onclick="goToStep(2)" style="background-color: var(--color-primary); color: white; border: none; padding: 12px; border-radius: var(--radius-button); font-weight: 600; margin-top: 12px; cursor: pointer;">
                    Siguiente: Seguridad &rarr;
                </button>
            </div>

            <!-- PASO 2: Credenciales -->
            <div id="step-2" class="wizard-step" style="display: none; flex-direction: column; gap: 16px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Seguridad y Contacto</h2>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Contraseña</label>
                    <input type="password" name="password" id="password-field" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-button); border: 1px solid rgba(27, 61, 26, 0.2); background: rgba(255,255,255,0.85); outline: none; box-sizing: border-box;">

                    <!-- Guía visual de requerimientos de seguridad -->
                    <div style="background: rgba(27, 61, 26, 0.04); padding: 10px 12px; border-radius: 6px; margin-top: 8px; font-size: 0.78rem; color: #4a5568;">
                        <strong>Requisitos de la contraseña:</strong>
                        <ul style="margin: 4px 0 0 16px; padding: 0;">
                            <li>Mínimo 8 caracteres</li>
                            <li>Incluir al menos una mayúscula y una minúscula</li>
                            <li>Incluir al menos un número y un símbolo (!@#$%^&*)</li>
                        </ul>
                    </div>

                    @error('password')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password-confirm-field" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-button); border: 1px solid rgba(27, 61, 26, 0.2); background: rgba(255,255,255,0.85); outline: none; box-sizing: border-box;">
                </div>

                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button type="button" onclick="goToStep(1)" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 12px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; flex: 1;">
                        &larr; Volver
                    </button>
                    <button type="button" onclick="goToStep(3)" style="background-color: var(--color-primary); color: white; border: none; padding: 12px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; flex: 2;">
                        Siguiente: Perfil &rarr;
                    </button>
                </div>
            </div>

            <!-- PASO 3: Tipo de Cuenta -->
            <div id="step-3" class="wizard-step" style="display: none; flex-direction: column; gap: 16px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Tipo de Cuenta</h2>

                <div style="display: flex; gap: 12px;">
                    <label id="card-personal" style="flex: 1; border: 1px solid var(--color-primary); background: rgba(88,148,69,0.05); padding: 14px; border-radius: var(--radius-button); cursor: pointer; text-align: center;">
                        <input type="radio" name="account_type" value="personal" checked onchange="toggleAccountType('personal')" style="display: none;">
                        <div style="font-weight: 600; color: var(--color-tertiary);">Uso Personal</div>
                    </label>

                    <label id="card-company" style="flex: 1; border: 1px solid rgba(27,61,26,0.2); padding: 14px; border-radius: var(--radius-button); cursor: pointer; text-align: center;">
                        <input type="radio" name="account_type" value="company" onchange="toggleAccountType('company')" style="display: none;">
                        <div style="font-weight: 600; color: var(--color-tertiary);">Empresa (B2B)</div>
                    </label>
                </div>

                <div id="company-name-group" style="display: none;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px; margin-top: 8px;">Nombre de la Empresa</label>
                    <input type="text" id="company_name_input" name="company_name" placeholder="Ej. Mi Empresa S.A." style="width: 100%; padding: 10px 14px; border-radius: var(--radius-button); border: 1px solid rgba(27, 61, 26, 0.2); background: rgba(255,255,255,0.85); outline: none; box-sizing: border-box;">
                    @error('company_name')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button type="button" onclick="goToStep(2)" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 12px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; flex: 1;">
                        &larr; Volver
                    </button>
                    <button type="submit" style="background-color: var(--color-primary); color: white; border: none; padding: 12px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; flex: 2; box-shadow: var(--shadow-soft);">
                        Finalizar Registro
                    </button>
                </div>
            </div>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid rgba(27, 61, 26, 0.08); font-size: 0.875rem; color: #718096;">
            ¿Ya tienes una cuenta? <a href="{{ route('login') }}" style="color: var(--color-primary); text-decoration: none; font-weight: 700;">Inicia Sesión</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentStep = 1;

        document.getElementById('wizard-form').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                return false;
            }
        });

        function goToStep(targetStep) {
            if (targetStep > currentStep) {
                const currentStepEl = document.getElementById('step-' + currentStep);
                const requiredInputs = currentStepEl.querySelectorAll('input[required]');
                let isValid = true;

                requiredInputs.forEach(input => {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        isValid = false;
                    }
                });

                if (!isValid) return;
            }

            document.getElementById('step-' + currentStep).style.display = 'none';

            const targetEl = document.getElementById('step-' + targetStep);
            targetEl.style.display = 'flex';

            currentStep = targetStep;

            document.getElementById('step-indicator-text').innerText = `Paso ${currentStep} de 3`;

            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('dot-' + i);
                if (i <= currentStep) {
                    dot.style.backgroundColor = 'var(--color-primary)';
                } else {
                    dot.style.backgroundColor = 'rgba(0,0,0,0.1)';
                }
            }
        }

        function toggleAccountType(type) {
            const personalCard = document.getElementById('card-personal');
            const companyCard = document.getElementById('card-company');
            const companyGroup = document.getElementById('company-name-group');
            const companyInput = document.getElementById('company_name_input');

            if (type === 'company') {
                companyCard.style.borderColor = 'var(--color-primary)';
                companyCard.style.background = 'rgba(88,148,69,0.05)';

                personalCard.style.borderColor = 'rgba(27,61,26,0.2)';
                personalCard.style.background = 'transparent';

                companyGroup.style.display = 'block';
                companyInput.setAttribute('required', 'required');
            } else {
                personalCard.style.borderColor = 'var(--color-primary)';
                personalCard.style.background = 'rgba(88,148,69,0.05)';

                companyCard.style.borderColor = 'rgba(27,61,26,0.2)';
                companyCard.style.background = 'transparent';

                companyGroup.style.display = 'none';
                companyInput.removeAttribute('required');
                companyInput.value = '';
            }
        }

        @if ($errors->has('password') || $errors->has('password_confirmation'))
        goToStep(2);
        @elseif ($errors->has('company_name') || $errors->has('account_type'))
        goToStep(3);
        @endif
    </script>
@endpush
