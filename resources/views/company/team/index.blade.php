@extends('layouts.app')

@section('title', 'Mi Equipo | Sensoría IoT')
@section('header', 'Gestión de Equipo')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding-bottom: 40px;">

        <!-- Contenedor principal con Glassmorphism -->
        <div class="glass-surface" style="padding: 32px; border-radius: 12px; margin-bottom: 24px;">

            <!-- Cabecera de la sección -->
            <div style="margin-bottom: 24px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">
                        Miembros de {{ $company->trade_name }}
                    </h3>
                    <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                        Administra los accesos y roles de las personas en este espacio de trabajo.
                    </p>
                </div>

                <!-- Botón Invitar Miembro (Abre Modal) -->
                @if(Auth::user()->hasCompanyRole(['PROPIETARIO', 'ADMINISTRADOR']))
                    <button type="button" onclick="openInviteModal()" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="icon-user-plus"></i> Invitar Miembro
                    </button>
                @endif
            </div>

            <!-- Tabla de Usuarios -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                    <tr style="border-bottom: 1px solid rgba(27, 61, 26, 0.1); color: var(--color-tertiary);">
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Nombre</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Correo Electrónico</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Rol</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem; text-align: right;">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($teamMembers as $member)
                        @php
                            $role = strtoupper($member->pivot->company_role ?? 'MIEMBRO');
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(27, 61, 26, 0.05);">
                            <td style="padding: 16px; color: var(--color-tertiary); font-weight: 500; display: flex; align-items: center; gap: 12px;">
                                <!-- Avatar circular con la inicial -->
                                <div style="width: 34px; height: 34px; border-radius: 50%; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span style="display: block; font-weight: 600;">{{ $member->name }}</span>
                                    @if($member->id === Auth::id())
                                        <span style="font-size: 0.75rem; color: #718096; font-weight: normal;">(Tú)</span>
                                    @endif
                                </div>
                            </td>

                            <td style="padding: 16px; color: #718096; font-size: 0.9rem;">
                                {{ $member->email }}
                            </td>

                            <td style="padding: 16px;">
                                <!-- Badge de Rol -->
                                <span style="background-color: rgba(88, 148, 69, 0.1); color: var(--color-primary); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                {{ $role }}
                            </span>
                            </td>

                            <td style="padding: 16px; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    @if(Auth::user()->hasCompanyRole(['PROPIETARIO', 'ADMINISTRADOR']))
                                        <button type="button" onclick="openEditMemberModal('{{ $member->id }}', '{{ $member->name }}', '{{ $role }}')" style="background: none; border: none; color: var(--color-primary); cursor: pointer; font-size: 0.875rem; font-weight: 600; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="icon-edit"></i> Editar
                                        </button>

                                        @if($member->id !== Auth::id())
                                            <button type="button" onclick="openDeleteMemberModal('{{ $member->id }}', '{{ $member->name }}')" style="background: none; border: none; color: #e53e3e; cursor: pointer; font-size: 0.875rem; font-weight: 600; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="icon-trash"></i> Eliminar
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- MODAL 1: Invitar Miembro -->
    <div id="invite-member-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass-surface" style="width: 100%; max-width: 500px; padding: 32px; border-radius: 12px; background: #ffffff;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 12px;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Invitar Nuevo Miembro</h3>
                <button type="button" onclick="closeInviteModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #718096;">&times;</button>
            </div>

            <form method="POST" action="{{ route('company.team.store') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf

                <!-- Correo Electrónico -->
                <div>
                    <label for="invite_email" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Correo Electrónico</label>
                    <input type="email" id="invite_email" name="email" value="{{ old('email') }}" required placeholder="usuario@empresa.com" style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; box-sizing: border-box;">
                    @error('email')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Asignación de Rol -->
                <div>
                    <label for="invite_role" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Rol en el Espacio</label>
                    <select id="invite_role" name="role" required style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; background: #ffffff; box-sizing: border-box;">
                        <option value="MIEMBRO">Miembro</option>
                        <option value="TECNICO">Técnico</option>
                        <option value="ADMINISTRADOR">Administrador</option>
                    </select>
                    @error('role')
                    <span style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botones -->
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                    <button type="button" onclick="closeInviteModal()" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 24px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Enviar Invitación
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- MODAL 2: Editar Rol de Miembro -->
    <div id="edit-member-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass-surface" style="width: 100%; max-width: 500px; padding: 32px; border-radius: 12px; background: #ffffff;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 12px;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">Gestionar Miembro</h3>
                <button type="button" onclick="closeEditMemberModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #718096;">&times;</button>
            </div>

            <form id="edit-member-form" method="POST" action="" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 4px;">Nombre del Integrante</label>
                    <span id="edit-member-name" style="font-size: 1.05rem; font-weight: 700; color: var(--color-tertiary);"></span>
                </div>

                <!-- Modificar Rol -->
                <div>
                    <label for="edit_role" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--color-tertiary); margin-bottom: 6px;">Rol Asignado</label>
                    <select id="edit_role" name="role" required style="width: 100%; padding: 10px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); outline: none; background: #ffffff; box-sizing: border-box;">
                        <option value="PROPIETARIO">Propietario</option>
                        <option value="ADMINISTRADOR">Administrador</option>
                        <option value="TECNICO">Técnico</option>
                        <option value="MIEMBRO">Miembro</option>
                    </select>
                </div>

                <!-- Botones -->
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                    <button type="button" onclick="closeEditMemberModal()" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit" style="background-color: var(--color-primary); color: white; border: none; padding: 10px 24px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Actualizar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- MODAL 3: Confirmar Eliminación de Miembro -->
    <div id="delete-member-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass-surface" style="width: 100%; max-width: 450px; padding: 32px; border-radius: 12px; background: #ffffff;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 12px;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #e53e3e; margin: 0;">Remover Integrante</h3>
                <button type="button" onclick="closeDeleteMemberModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #718096;">&times;</button>
            </div>

            <p style="color: #4a5568; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
                ¿Estás seguro de que deseas remover a <strong id="delete-member-name" style="color: var(--color-tertiary);"></strong> de esta empresa? Perderá el acceso inmediatamente a este espacio.
            </p>

            <form id="delete-member-form" method="POST" action="">
                @csrf
                @method('DELETE')

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="closeDeleteMemberModal()" style="background: transparent; border: 1px solid rgba(27, 61, 26, 0.2); padding: 10px 20px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit" style="background-color: #e53e3e; color: white; border: none; padding: 10px 24px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                        Sí, Remover
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Modal Invitar
        function openInviteModal() {
            document.getElementById('invite-member-modal').style.display = 'flex';
        }

        function closeInviteModal() {
            document.getElementById('invite-member-modal').style.display = 'none';
        }

        // Modal Editar
        function openEditMemberModal(memberId, name, role) {
            document.getElementById('edit-member-name').innerText = name;
            document.getElementById('edit_role').value = role;

            const form = document.getElementById('edit-member-form');
            form.action = `/company/team/${memberId}`;

            document.getElementById('edit-member-modal').style.display = 'flex';
        }

        function closeEditMemberModal() {
            document.getElementById('edit-member-modal').style.display = 'none';
        }

        // Modal Eliminar
        function openDeleteMemberModal(memberId, name) {
            document.getElementById('delete-member-name').innerText = name;

            const form = document.getElementById('delete-member-form');
            form.action = `/company/team/${memberId}`;

            document.getElementById('delete-member-modal').style.display = 'flex';
        }

        function closeDeleteMemberModal() {
            document.getElementById('delete-member-modal').style.display = 'none';
        }

        // Backdrop click
        window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('invite-member-modal')) closeInviteModal();
            if (e.target === document.getElementById('edit-member-modal')) closeEditMemberModal();
            if (e.target === document.getElementById('delete-member-modal')) closeDeleteMemberModal();
        });

        @if ($errors->has('email') || $errors->has('role'))
        openInviteModal();
        @endif
    </script>
@endpush
