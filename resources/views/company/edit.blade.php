<x-app-layout>
    <x-slot name="header">
        Perfil de la Empresa / Espacio
    </x-slot>

    <!-- Contenedor principal con Glassmorphism -->
    <div class="glass-surface" style="padding: 32px; max-width: 800px; margin: 0 auto 32px auto;">

        <header style="margin-bottom: 24px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--color-tertiary);">
                Información del Negocio
            </h2>
            <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                Actualiza los datos fiscales y de contacto de tu empresa o espacio personal.
            </p>
        </header>

        <form method="post" action="{{ route('company.update') }}" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            @method('patch')

            <!-- Nombre Comercial -->
            <div>
                <label for="trade_name" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 8px;">Nombre Comercial</label>
                <input type="text" id="trade_name" name="trade_name" value="{{ old('trade_name', $company->trade_name) }}" required autofocus autocomplete="organization"
                       style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); background: rgba(250, 250, 247, 0.8); color: var(--color-tertiary); font-family: inherit; outline: none; transition: border-color 0.2s;">
                <x-input-error class="mt-2" :messages="$errors->get('trade_name')" />
            </div>

            <!-- Razón Social -->
            <div>
                <label for="legal_name" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 8px;">Razón Social (Legal)</label>
                <input type="text" id="legal_name" name="legal_name" value="{{ old('legal_name', $company->legal_name) }}" required
                       style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); background: rgba(250, 250, 247, 0.8); color: var(--color-tertiary); font-family: inherit; outline: none;">
                <x-input-error class="mt-2" :messages="$errors->get('legal_name')" />
            </div>

            <!-- Sector -->
            <div>
                <label for="sector" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 8px;">Sector / Industria</label>
                <input type="text" id="sector" name="sector" value="{{ old('sector', $company->sector) }}" required
                       style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); background: rgba(250, 250, 247, 0.8); color: var(--color-tertiary); font-family: inherit; outline: none;">
                <x-input-error class="mt-2" :messages="$errors->get('sector')" />
            </div>

            <!-- Teléfono -->
            <div>
                <label for="phone" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 8px;">Teléfono de Contacto</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $company->phone) }}"
                       style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); background: rgba(250, 250, 247, 0.8); color: var(--color-tertiary); font-family: inherit; outline: none;">
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Dirección -->
            <div>
                <label for="address" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 8px;">Dirección Principal</label>
                <textarea id="address" name="address" rows="3"
                          style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); background: rgba(250, 250, 247, 0.8); color: var(--color-tertiary); font-family: inherit; outline: none; resize: vertical;">{{ old('address', $company->address) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>

            <!-- Botones y Feedback -->
            <div style="display: flex; items-center: gap 16px; margin-top: 16px;">
                <button type="submit" style="background-color: var(--color-primary); color: white; padding: 12px 28px; border: none; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; box-shadow: 0 4px 10px rgba(88, 148, 69, 0.2); transition: opacity 0.2s;">
                    Guardar Cambios
                </button>

                @if (session('status') === 'company-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 3000)"
                        style="color: var(--color-secondary); font-weight: 600; display: flex; align-items: center;"
                    >✓ Cambios guardados</p>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>
