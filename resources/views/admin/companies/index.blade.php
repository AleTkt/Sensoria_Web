@extends('layouts.backoffice')

@section('title', 'Empresas | Staff Sensoría')
@section('header', 'Directorio de Empresas (Tenants)')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/companies/companies.css') }}">
@endpush

@section('content')

    <!-- Alertas de Éxito y Errores -->
    @if(session('status'))
        <div style="background: rgba(88, 148, 69, 0.12); color: #166534; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(88, 148, 69, 0.3); font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-circle-check" style="color: var(--color-primary, #589445); font-size: 1.2rem;"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #fca5a5; font-weight: 500;">
            <div style="font-weight: 700; margin-bottom: 0.3rem;">Por favor corrija los siguientes errores:</div>
            <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sensoria-card">

        {{-- Botón para Crear Empresa --}}
        <div style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end;">
            <button onclick="document.getElementById('modal-create').style.display='flex'" class="btn-sensoria-primary">
                <i class="fa-solid fa-plus"></i> Nueva Empresa
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table id="companies-table" class="display" style="width:100%">
                <thead>
                <tr>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">ID</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Empresa / Negocio</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Sector</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Propietario Principal</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Estado</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A); text-align: right;">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($companies as $company)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 1rem; font-weight: 700; color: #4b5563;">{{ $company->id }}</td>
                        <td style="padding: 1rem;">
                            <strong style="color: var(--color-tertiary, #1B3D1A); font-size: 1rem;">{{ $company->trade_name }}</strong><br>
                            <span style="color: #6b7280; font-size: 0.82rem;"><i class="fa-regular fa-envelope"></i> {{ $company->email }}</span>
                        </td>
                        <td style="padding: 1rem;">
                            <span style="background: #f3f4f6; color: #374151; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                {{ $company->sector ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="padding: 1rem;">
                            @php
                                $owner = $company->users->where('pivot.company_role', 'PROPIETARIO')->first();
                            @endphp
                            <div style="display: flex; align-items: center; gap: 0.4rem; color: #374151;">
                                <i class="fa-regular fa-user" style="color: var(--color-primary, #589445);"></i>
                                {{ $owner ? $owner->name : 'Sin Propietario' }}
                            </div>
                        </td>
                        <td style="padding: 1rem;">
                            @if($company->is_active)
                                <span style="background: #d1fae5; color: #065f46; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;">ACTIVA</span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;">SUSPENDIDA</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 6px;">
                                {{-- Botón Modal Editar Empresa --}}
                                <button onclick="document.getElementById('modal-edit-{{ $company->id }}').style.display='flex'" class="btn-action-icon" title="Editar Empresa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                {{-- Botón Modal Suspender / Reactivar --}}
                                <button onclick="document.getElementById('modal-status-{{ $company->id }}').style.display='flex'" class="btn-action-icon" title="{{ $company->is_active ? 'Suspender Empresa' : 'Reactivar Empresa' }}" style="color: {{ $company->is_active ? '#dc2626' : '#16a34a' }};">
                                    <i class="fa-solid {{ $company->is_active ? 'fa-ban' : 'fa-circle-check' }}"></i>
                                </button>

                                {{-- Botón Modal Gestionar Equipo --}}
                                <button onclick="document.getElementById('modal-team-{{ $company->id }}').style.display='flex'" class="btn-action-icon" title="Gestionar Equipo" style="color: #0284c7;">
                                    <i class="fa-solid fa-users-gear"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- ITERAMOS DE NUEVO LAS EMPRESAS PARA GENERAR LOS MODALES ESTANDARIZADOS FUERA DE LA TABLA --}}
        @foreach($companies as $company)
            {{-- Modal de Edición --}}
            <div id="modal-edit-{{ $company->id }}" class="sensoria-modal-overlay">
                <div class="sensoria-modal-box">
                    <button onclick="document.getElementById('modal-edit-{{ $company->id }}').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>

                    <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--color-tertiary, #1B3D1A); font-weight: 800; font-size: 1.25rem;">
                        <i class="fa-solid fa-building-pen" style="color: var(--color-primary, #589445);"></i> Editar Empresa: {{ $company->trade_name }}
                    </h3>

                    <form action="{{ route('admin.companies.update', $company->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label class="sensoria-label">Nombre Comercial *</label>
                                <input type="text" name="trade_name" value="{{ $company->trade_name }}" required class="sensoria-input">
                            </div>
                            <div>
                                <label class="sensoria-label">Razón Social</label>
                                <input type="text" name="business_name" value="{{ $company->business_name }}" class="sensoria-input">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label class="sensoria-label">NIT</label>
                                <input type="text" name="nit" value="{{ $company->nit }}" class="sensoria-input">
                            </div>
                            <div>
                                <label class="sensoria-label">NRC</label>
                                <input type="text" name="nrc" value="{{ $company->nrc }}" class="sensoria-input">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label class="sensoria-label">Sector</label>
                                <input type="text" name="sector" value="{{ $company->sector }}" class="sensoria-input" placeholder="Ej. Retail, Alimentos...">
                            </div>
                            <div>
                                <label class="sensoria-label">Teléfono</label>
                                <input type="text" name="phone" value="{{ $company->phone }}" class="sensoria-input">
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label class="sensoria-label">Correo Corporativo *</label>
                            <input type="email" name="email" value="{{ $company->email }}" required class="sensoria-input">
                        </div>

                        <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 2rem;">
                            <button type="button" onclick="document.getElementById('modal-edit-{{ $company->id }}').style.display='none'" class="btn-sensoria-secondary">Cancelar</button>
                            <button type="submit" class="btn-sensoria-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Confirmación Suspender/Reactivar --}}
            <div id="modal-status-{{ $company->id }}" class="sensoria-modal-overlay">
                <div class="sensoria-modal-box" style="max-width: 450px;">
                    <button onclick="document.getElementById('modal-status-{{ $company->id }}').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>

                    <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--color-tertiary, #1B3D1A); font-weight: 800; font-size: 1.2rem;">
                        {{ $company->is_active ? 'Suspender Empresa' : 'Reactivar Empresa' }}
                    </h3>
                    <p style="color: #4b5563; margin-bottom: 1.5rem; font-size: 0.92rem; line-height: 1.5;">
                        ¿Estás seguro que deseas {{ $company->is_active ? 'suspender' : 'reactivar' }} la empresa <strong style="color: #111827;">{{ $company->trade_name }}</strong>?
                    </p>

                    <form action="{{ route('admin.companies.toggle-status', $company->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                            <button type="button" onclick="document.getElementById('modal-status-{{ $company->id }}').style.display='none'" class="btn-sensoria-secondary">Cancelar</button>
                            <button type="submit" class="btn-sensoria-primary" style="background-color: {{ $company->is_active ? '#dc2626' : '#16a34a' }};">
                                Sí, {{ $company->is_active ? 'Suspender' : 'Reactivar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Gestionar Equipo --}}
            <div id="modal-team-{{ $company->id }}" class="sensoria-modal-overlay">
                <div class="sensoria-modal-box" style="max-width: 700px;">
                    <button onclick="document.getElementById('modal-team-{{ $company->id }}').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>

                    <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--color-tertiary, #1B3D1A); font-weight: 800; font-size: 1.25rem;">
                        <i class="fa-solid fa-users" style="color: var(--color-primary, #589445);"></i> Equipo de: {{ $company->trade_name }}
                    </h3>

                    <!-- Formulario para Agregar Nuevo Miembro -->
                    <div style="background: #f9faf7; border: 1px solid rgba(88, 148, 69, 0.2); padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        <h4 style="margin-top: 0; margin-bottom: 0.75rem; font-size: 0.88rem; color: var(--color-tertiary, #1B3D1A); font-weight: 700; text-transform: uppercase;">
                            <i class="fa-solid fa-user-plus"></i> Añadir Miembro Existente
                        </h4>
                        <form action="{{ route('admin.companies.team.store', $company->id) }}" method="POST" style="display: grid; grid-template-columns: 2fr 1.2fr auto; gap: 0.75rem; align-items: end;">
                            @csrf
                            <div>
                                <label class="sensoria-label">Usuario (Cliente Global)</label>
                                <select name="user_id" required class="sensoria-input">
                                    <option value="">Seleccione un usuario...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->name }} ({{ $cliente->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="sensoria-label">Rol en la Empresa</label>
                                <select name="role" required class="sensoria-input">
                                    <option value="MIEMBRO">Miembro</option>
                                    <option value="TECNICO">Técnico</option>
                                    <option value="ADMINISTRADOR">Administrador</option>
                                    <option value="PROPIETARIO">Propietario</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-sensoria-primary" style="height: 41px; padding: 0 1rem; justify-content: center;">
                                <i class="fa-solid fa-plus"></i> Añadir
                            </button>
                        </form>
                    </div>

                    <!-- Lista de Miembros Actuales -->
                    <h4 style="margin-top: 0; margin-bottom: 0.75rem; font-size: 0.88rem; color: var(--color-tertiary, #1B3D1A); font-weight: 700; text-transform: uppercase;">Miembros Actuales</h4>
                    <div style="border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; background: white;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead style="background: #f9faf7; border-bottom: 2px solid rgba(88, 148, 69, 0.15);">
                            <tr>
                                <th style="padding: 0.75rem 1rem; font-size: 0.78rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A); text-transform: uppercase;">Usuario</th>
                                <th style="padding: 0.75rem 1rem; font-size: 0.78rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A); text-transform: uppercase;">Rol Local</th>
                                <th style="padding: 0.75rem 1rem; font-size: 0.78rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A); text-transform: uppercase; text-align: right;">Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($company->users as $user)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 0.85rem 1rem; font-size: 0.88rem;">
                                        <strong style="color: #111827;">{{ $user->name }}</strong><br>
                                        <span style="color: #6b7280; font-size: 0.78rem;">{{ $user->email }}</span>
                                    </td>
                                    <td style="padding: 0.85rem 1rem;">
                                        <form action="{{ route('admin.companies.team.update', [$company->id, $user->id]) }}" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" class="sensoria-input" style="padding: 0.35rem 0.5rem; font-size: 0.82rem; width: auto;">
                                                <option value="PROPIETARIO" {{ $user->pivot->company_role == 'PROPIETARIO' ? 'selected' : '' }}>Propietario</option>
                                                <option value="ADMINISTRADOR" {{ $user->pivot->company_role == 'ADMINISTRADOR' ? 'selected' : '' }}>Administrador</option>
                                                <option value="TECNICO" {{ $user->pivot->company_role == 'TECNICO' ? 'selected' : '' }}>Técnico</option>
                                                <option value="MIEMBRO" {{ $user->pivot->company_role == 'MIEMBRO' ? 'selected' : '' }}>Miembro</option>
                                            </select>
                                            <button type="submit" class="btn-action-icon" title="Actualizar Rol" style="padding: 0.35rem 0.6rem;">
                                                <i class="fa-solid fa-floppy-disk" style="color: var(--color-primary, #589445);"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td style="padding: 0.85rem 1rem; text-align: right;">
                                        <form action="{{ route('admin.companies.team.destroy', [$company->id, $user->id]) }}" method="POST" onsubmit="return confirm('¿Quitar a este usuario de la empresa?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon" title="Remover" style="background: #fee2e2; border-color: #fca5a5; color: #dc2626;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 2rem; text-align: center; color: #6b7280; font-size: 0.88rem; font-style: italic;">No hay miembros registrados en esta empresa.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    {{-- Modal para Crear Empresa y Asignar Propietario --}}
    <div id="modal-create" class="sensoria-modal-overlay">
        <div class="sensoria-modal-box">

            <button onclick="document.getElementById('modal-create').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>

            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--color-tertiary, #1B3D1A); font-weight: 800; font-size: 1.25rem;">
                <i class="fa-solid fa-building-user" style="color: var(--color-primary, #589445);"></i> Crear Nueva Empresa
            </h3>

            <form action="{{ route('admin.companies.store') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="sensoria-label">Nombre Comercial *</label>
                        <input type="text" name="trade_name" required class="sensoria-input" placeholder="Nombre público">
                    </div>
                    <div>
                        <label class="sensoria-label">Razón Social</label>
                        <input type="text" name="business_name" class="sensoria-input" placeholder="Nombre legal">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="sensoria-label">Sector</label>
                        <input type="text" name="sector" class="sensoria-input" placeholder="Ej. Retail, Restaurantes...">
                    </div>
                    <div>
                        <label class="sensoria-label">Correo Corporativo *</label>
                        <input type="email" name="email" required class="sensoria-input" placeholder="empresa@correo.com">
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 1.5rem 0;">

                <div style="margin-bottom: 1rem;">
                    <label class="sensoria-label" style="color: var(--color-tertiary, #1B3D1A);">Asignar Propietario Inicial *</label>
                    <select name="owner_id" required class="sensoria-input">
                        <option value="">-- Selecciona un Usuario Cliente --</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->name }} ({{ $cliente->email }})</option>
                        @endforeach
                    </select>
                    <small style="color: #6b7280; font-size: 0.78rem; display: block; margin-top: 0.3rem;">El usuario debe existir previamente en el Directorio de Usuarios Globales.</small>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" onclick="document.getElementById('modal-create').style.display='none'" class="btn-sensoria-secondary">Cancelar</button>
                    <button type="submit" class="btn-sensoria-primary">Crear Empresa</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#companies-table').DataTable({
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 5 }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });
        });
    </script>
@endpush
