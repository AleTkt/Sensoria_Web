<x-app-layout>
    <x-slot name="header">
        Gestión de Equipo
    </x-slot>

    <!-- Contenedor principal con Glassmorphism -->
    <div class="glass-surface" style="padding: 32px; max-width: 1000px; margin: 0 auto 32px auto;">

        <!-- Cabecera de la sección -->
        <header style="margin-bottom: 24px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--color-tertiary);">
                    Miembros de {{ $company->trade_name }}
                </h2>
                <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                    Administra los accesos y roles de las personas en este espacio de trabajo.
                </p>
            </div>

            <!-- Botón Invitar -->
            <a href="{{ route('company.team.create') }}" style="background-color: var(--color-primary); color: white; padding: 10px 20px; text-decoration: none; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; box-shadow: 0 4px 10px rgba(88, 148, 69, 0.2); transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                + Invitar Miembro
            </a>
        </header>

        <!-- Tabla de Usuarios -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                <tr style="border-bottom: 1px solid var(--glass-border); color: var(--color-tertiary);">
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Nombre</th>
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Correo Electrónico</th>
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Rol</th>
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($teamMembers as $member)
                    <tr style="border-bottom: 1px solid rgba(27, 61, 26, 0.05); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(88, 148, 69, 0.05)'" onmouseout="this.style.backgroundColor='transparent'">

                        <td style="padding: 16px; color: var(--color-tertiary); font-weight: 500; display: flex; align-items: center; gap: 12px;">
                            <!-- Avatar circular con la inicial -->
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: var(--color-secondary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: bold;">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            {{ $member->name }}

                            @if($member->id === Auth::id())
                                <span style="font-size: 0.75rem; color: #718096; font-weight: normal;">(Tú)</span>
                            @endif
                        </td>

                        <td style="padding: 16px; color: #718096; font-size: 0.9rem;">
                            {{ $member->email }}
                        </td>

                        <td style="padding: 16px;">
                            <!-- Badge de Rol -->
                            <span style="background-color: rgba(88, 148, 69, 0.1); color: var(--color-secondary); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                                    Miembro
                                </span>
                        </td>

                        <td style="padding: 16px;">
                            <button style="background: none; border: none; color: var(--color-secondary); cursor: pointer; font-size: 0.875rem; font-weight: 600; padding: 0;">
                                Editar
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
