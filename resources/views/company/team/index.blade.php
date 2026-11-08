@extends('layouts.app')

@section('title', 'Mi Equipo | Sensoría IoT')
@section('header', 'Gestión de Equipo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/team.css') }}">
@endpush

@section('content')
    <div class="team-container">

        <!-- Contenedor principal con Glassmorphism -->
        <div class="glass-surface team-card">

            <!-- Cabecera de la sección -->
            <div class="team-card-header">
                <div>
                    <h3 class="team-title">
                        Miembros de {{ $company->trade_name }}
                    </h3>
                    <p class="team-subtitle">
                        Administra los accesos y roles de las personas en este espacio de trabajo.
                    </p>
                </div>

                <!-- Botón Invitar Miembro (Abre Modal) -->
                @if(Auth::user()->hasCompanyRole(['PROPIETARIO', 'ADMINISTRADOR']))
                    <button type="button" onclick="openInviteModal()" class="btn-primary">
                        <i class="fa-solid fa-user-plus"></i> Invitar Miembro
                    </button>
                @endif
            </div>

            <!-- Tabla de Usuarios -->
            <div class="team-table-wrapper">
                <table class="team-table">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                        <th>Rol</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($teamMembers as $member)
                        @php
                            $role = strtoupper($member->pivot->company_role ?? 'MIEMBRO');
                        @endphp
                        <tr>
                            <td>
                                <div class="member-info">
                                    <!-- Avatar circular con la inicial -->
                                    <div class="member-avatar">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="member-name">{{ $member->name }}</span>
                                        @if($member->id === Auth::id())
                                            <span class="member-you-badge">(Tú)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="member-email">
                                {{ $member->email }}
                            </td>

                            <td>
                                <!-- Badge de Rol -->
                                <span class="role-badge">
                                    {{ $role }}
                                </span>
                            </td>

                            <td style="text-align: right;">
                                <div class="actions-container">
                                    @if(Auth::user()->hasCompanyRole(['PROPIETARIO', 'ADMINISTRADOR']))
                                        <button type="button" onclick="openEditMemberModal('{{ $member->id }}', '{{ $member->name }}', '{{ $role }}')" class="btn-icon-edit">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </button>

                                        @if($member->id !== Auth::id())
                                            <button type="button" onclick="openDeleteMemberModal('{{ $member->id }}', '{{ $member->name }}')" class="btn-icon-delete">
                                                <i class="fa-solid fa-trash-can"></i> Eliminar
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
    <div id="invite-member-modal" class="modal-overlay">
        <div class="glass-surface modal-content">

            <div class="modal-header">
                <h3 class="modal-title">Invitar Nuevo Miembro</h3>
                <button type="button" onclick="closeInviteModal()" class="btn-close-modal">&times;</button>
            </div>

            <form method="POST" action="{{ route('company.team.store') }}" class="modal-form">
                @csrf

                <!-- Correo Electrónico -->
                <div class="form-group">
                    <label for="invite_email">Correo Electrónico</label>
                    <input type="email" id="invite_email" name="email" value="{{ old('email') }}" required placeholder="usuario@empresa.com" class="form-control">
                    @error('email')
                    <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Asignación de Rol -->
                <div class="form-group">
                    <label for="invite_role">Rol en el Espacio</label>
                    <select id="invite_role" name="role" required class="form-control">
                        <option value="MIEMBRO">Miembro</option>
                        <option value="TECNICO">Técnico</option>
                        <option value="ADMINISTRADOR">Administrador</option>
                    </select>
                    @error('role')
                    <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="modal-actions">
                    <button type="button" onclick="closeInviteModal()" class="btn-cancel">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        Enviar Invitación
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- MODAL 2: Editar Rol de Miembro -->
    <div id="edit-member-modal" class="modal-overlay">
        <div class="glass-surface modal-content">

            <div class="modal-header">
                <h3 class="modal-title">Gestionar Miembro</h3>
                <button type="button" onclick="closeEditMemberModal()" class="btn-close-modal">&times;</button>
            </div>

            <form id="edit-member-form" method="POST" action="" class="modal-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nombre del Integrante</label>
                    <span id="edit-member-name" class="highlight-text" style="font-size: 1.05rem;"></span>
                </div>

                <!-- Modificar Rol -->
                <div class="form-group">
                    <label for="edit_role">Rol Asignado</label>
                    <select id="edit_role" name="role" required class="form-control">
                        <option value="PROPIETARIO">Propietario</option>
                        <option value="ADMINISTRADOR">Administrador</option>
                        <option value="TECNICO">Técnico</option>
                        <option value="MIEMBRO">Miembro</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="modal-actions">
                    <button type="button" onclick="closeEditMemberModal()" class="btn-cancel">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        Actualizar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- MODAL 3: Confirmar Eliminación de Miembro -->
    <div id="delete-member-modal" class="modal-overlay">
        <div class="glass-surface modal-content sm">

            <div class="modal-header">
                <h3 class="modal-title danger">Remover Integrante</h3>
                <button type="button" onclick="closeDeleteMemberModal()" class="btn-close-modal">&times;</button>
            </div>

            <p class="modal-text">
                ¿Estás seguro de que deseas remover a <strong id="delete-member-name" class="highlight-text"></strong> de esta empresa? Perderá el acceso inmediatamente a este espacio.
            </p>

            <form id="delete-member-form" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="modal-actions">
                    <button type="button" onclick="closeDeleteMemberModal()" class="btn-cancel">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-danger">
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
