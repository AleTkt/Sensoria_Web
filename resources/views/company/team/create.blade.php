<x-app-layout>
    <x-slot name="header">
        Invitar Miembro al Equipo
    </x-slot>

    <!-- Contenedor principal con Glassmorphism -->
    <div class="glass-surface" style="padding: 32px; max-width: 600px; margin: 0 auto 32px auto;">

        <header style="margin-bottom: 24px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--color-tertiary);">
                Agregar nuevo integrante
            </h2>
            <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                Ingresa el correo electrónico de la persona que deseas invitar. Debe tener una cuenta activa en Sensoría.
            </p>
        </header>

        <form method="post" action="{{ route('company.team.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf

            <!-- Correo Electrónico -->
            <div>
                <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 8px;">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ejemplo@correo.com"
                       style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); background: rgba(250, 250, 247, 0.8); color: var(--color-tertiary); font-family: inherit; outline: none; transition: border-color 0.2s;">
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <!-- Botones -->
            <div style="display: flex; items-center; gap: 16px; margin-top: 16px;">
                <button type="submit" style="background-color: var(--color-primary); color: white; padding: 12px 28px; border: none; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; box-shadow: 0 4px 10px rgba(88, 148, 69, 0.2); transition: opacity 0.2s;">
                    Enviar Invitación
                </button>

                <a href="{{ route('company.team') }}" style="color: #718096; text-decoration: none; font-weight: 600; padding: 12px 20px; display: inline-block;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
