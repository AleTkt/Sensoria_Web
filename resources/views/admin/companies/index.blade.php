@extends('layouts.backoffice')

@section('title', 'Empresas | Staff Sensoría')
@section('header', 'Directorio de Empresas (Tenants)')

@section('content')

    <!-- Alertas de Éxito y Errores -->
    @if(session('status'))
        <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #bbf7d0;">
            ✅ {{ session('status') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; padding: 24px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">

        {{-- Botón para Crear Empresa --}}
        <div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
            <button onclick="document.getElementById('modal-create').style.display='flex'" style="background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                + Nueva Empresa
            </button>
        </div>

        <table id="companies-table" class="display" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Empresa / Negocio</th>
                <th>Sector</th>
                <th>Propietario Principal</th>
                <th>Estado</th>
                <th style="text-align: right;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{ $company->id }}</td>
                    <td>
                        <strong style="color: #0f172a;">{{ $company->trade_name }}</strong><br>
                        <small style="color: #64748b;">{{ $company->email }}</small>
                    </td>
                    <td>{{ $company->sector ?? 'N/A' }}</td>
                    <td>
                        @php
                            $owner = $company->users->where('pivot.company_role', 'PROPIETARIO')->first();
                        @endphp
                        {{ $owner ? $owner->name : 'Sin Propietario' }}
                    </td>
                    <td>
                        @if($company->is_active)
                            <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">ACTIVA</span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">SUSPENDIDA</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 6px;">
                            {{-- Botón Modal Editar Empresa --}}
                            <button onclick="document.getElementById('modal-edit-{{ $company->id }}').style.display='flex'" title="Editar Empresa" style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; cursor: pointer; color: #475569;">
                                ✏️
                            </button>

                            {{-- Botón Modal Suspender / Reactivar --}}
                            <button onclick="document.getElementById('modal-status-{{ $company->id }}').style.display='flex'" title="{{ $company->is_active ? 'Suspender Empresa' : 'Reactivar Empresa' }}" style="background: {{ $company->is_active ? '#fee2e2' : '#dcfce3' }}; border: 1px solid {{ $company->is_active ? '#fecaca' : '#bbf7d0' }}; padding: 6px 10px; border-radius: 4px; cursor: pointer;">
                                {{ $company->is_active ? '🚫' : '✅' }}
                            </button>

                            {{-- Botón Modal Gestionar Equipo --}}
                            <button onclick="document.getElementById('modal-team-{{ $company->id }}').style.display='flex'" title="Gestionar Equipo" style="background: #e0f2fe; border: 1px solid #bae6fd; padding: 6px 10px; border-radius: 4px; cursor: pointer; color: #0284c7;">
                                👥
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{-- ITERAMOS DE NUEVO LAS EMPRESAS PARA GENERAR LOS MODALES FUERA DE LA TABLA --}}
        @foreach($companies as $company)
            {{-- Modal de Edición --}}
            <div id="modal-edit-{{ $company->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                <div style="background: white; width: 100%; max-width: 600px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative; text-align: left; max-height: 90vh; overflow-y: auto;">
                    <button onclick="document.getElementById('modal-edit-{{ $company->id }}').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #94a3b8;">&times;</button>

                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #0f172a;">Editar Empresa: {{ $company->trade_name }}</h3>

                    <form action="{{ route('admin.companies.update', $company->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Nombre Comercial *</label>
                                <input type="text" name="trade_name" value="{{ $company->trade_name }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Razón Social</label>
                                <input type="text" name="business_name" value="{{ $company->business_name }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">NIT</label>
                                <input type="text" name="nit" value="{{ $company->nit }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">NRC</label>
                                <input type="text" name="nrc" value="{{ $company->nrc }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Sector</label>
                                <input type="text" name="sector" value="{{ $company->sector }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;" placeholder="Ej. Retail, Alimentos...">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Teléfono</label>
                                <input type="text" name="phone" value="{{ $company->phone }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                            </div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Correo Corporativo *</label>
                            <input type="email" name="email" value="{{ $company->email }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                        </div>

                        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                            <button type="button" onclick="document.getElementById('modal-edit-{{ $company->id }}').style.display='none'" style="background: white; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">Cancelar</button>
                            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 600; cursor: pointer;">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Confirmación Suspender/Reactivar --}}
            <div id="modal-status-{{ $company->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                <div style="background: white; width: 100%; max-width: 450px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative; text-align: left;">
                    <button onclick="document.getElementById('modal-status-{{ $company->id }}').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #94a3b8;">&times;</button>

                    <h3 style="margin-top: 0; margin-bottom: 16px; color: #0f172a;">
                        {{ $company->is_active ? 'Suspender Empresa' : 'Reactivar Empresa' }}
                    </h3>
                    <p style="color: #475569; margin-bottom: 24px;">
                        ¿Estás seguro que deseas {{ $company->is_active ? 'suspender' : 'reactivar' }} la empresa <strong>{{ $company->trade_name }}</strong>?
                    </p>

                    <form action="{{ route('admin.companies.toggle-status', $company->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button type="button" onclick="document.getElementById('modal-status-{{ $company->id }}').style.display='none'" style="background: white; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">Cancelar</button>
                            <button type="submit" style="background: {{ $company->is_active ? '#dc2626' : '#16a34a' }}; color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 600; cursor: pointer;">
                                Sí, {{ $company->is_active ? 'Suspender' : 'Reactivar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Gestionar Equipo --}}
            <div id="modal-team-{{ $company->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                <div style="background: white; width: 100%; max-width: 650px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative; max-height: 90vh; overflow-y: auto;">
                    <button onclick="document.getElementById('modal-team-{{ $company->id }}').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #94a3b8;">&times;</button>

                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #0f172a;">Equipo de: {{ $company->trade_name }}</h3>

                    <!-- Formulario para Agregar Nuevo Miembro -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 6px; margin-bottom: 24px;">
                        <h4 style="margin-top: 0; margin-bottom: 12px; font-size: 0.9rem; color: #334155;">+ Añadir Miembro Existente</h4>
                        <form action="{{ route('admin.companies.team.store', $company->id) }}" method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
                            @csrf
                            <div style="flex: 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; margin-bottom: 4px;">Usuario (Cliente Global)</label>
                                <select name="user_id" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                                    <option value="">Seleccione un usuario...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->name }} ({{ $cliente->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; margin-bottom: 4px;">Rol en la Empresa</label>
                                <select name="role" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                                    <option value="MIEMBRO">Miembro</option>
                                    <option value="TECNICO">Técnico</option>
                                    <option value="ADMINISTRADOR">Administrador</option>
                                    <option value="PROPIETARIO">Propietario</option>
                                </select>
                            </div>
                            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 9px 16px; border-radius: 4px; font-weight: 600; cursor: pointer; height: 35px;">Añadir</button>
                        </form>
                    </div>

                    <!-- Lista de Miembros Actuales -->
                    <h4 style="margin-top: 0; margin-bottom: 12px; font-size: 0.9rem; color: #334155;">Miembros Actuales</h4>
                    <div style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead style="background: #f1f5f9;">
                            <tr>
                                <th style="padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 0.8rem; color: #475569;">Usuario</th>
                                <th style="padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 0.8rem; color: #475569;">Rol Local</th>
                                <th style="padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 0.8rem; color: #475569; text-align: right;">Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($company->users as $user)
                                <tr>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">
                                        <strong>{{ $user->name }}</strong><br>
                                        <span style="color: #64748b; font-size: 0.75rem;">{{ $user->email }}</span>
                                    </td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                        <form action="{{ route('admin.companies.team.update', [$company->id, $user->id]) }}" method="POST" style="display: flex; gap: 6px;">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" style="padding: 4px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.8rem;">
                                                <option value="PROPIETARIO" {{ $user->pivot->company_role == 'PROPIETARIO' ? 'selected' : '' }}>Propietario</option>
                                                <option value="ADMINISTRADOR" {{ $user->pivot->company_role == 'ADMINISTRADOR' ? 'selected' : '' }}>Administrador</option>
                                                <option value="TECNICO" {{ $user->pivot->company_role == 'TECNICO' ? 'selected' : '' }}>Técnico</option>
                                                <option value="MIEMBRO" {{ $user->pivot->company_role == 'MIEMBRO' ? 'selected' : '' }}>Miembro</option>
                                            </select>
                                            <button type="submit" title="Actualizar Rol" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; cursor: pointer; color: #475569;">💾</button>
                                        </form>
                                    </td>
                                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                        <form action="{{ route('admin.companies.team.destroy', [$company->id, $user->id]) }}" method="POST" onsubmit="return confirm('¿Quitar a este usuario de la empresa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Remover" style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 4px; cursor: pointer; color: #991b1b; padding: 4px 8px;">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 16px; text-align: center; color: #64748b; font-size: 0.85rem;">No hay miembros registrados.</td>
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
    <div id="modal-create" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; width: 100%; max-width: 600px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative; max-height: 90vh; overflow-y: auto;">

            <button onclick="document.getElementById('modal-create').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #94a3b8;">&times;</button>

            <h3 style="margin-top: 0; margin-bottom: 20px; color: #0f172a;">Crear Nueva Empresa</h3>

            <form action="{{ route('admin.companies.store') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Nombre Comercial *</label>
                        <input type="text" name="trade_name" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;" placeholder="Nombre público">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Razón Social</label>
                        <input type="text" name="business_name" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;" placeholder="Nombre legal">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Sector</label>
                        <input type="text" name="sector" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;" placeholder="Ej. Retail, Restaurantes...">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Correo Corporativo *</label>
                        <input type="email" name="email" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; color: #0369a1;">Asignar Propietario Inicial *</label>
                    <select name="owner_id" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                        <option value="">-- Selecciona un Usuario Cliente --</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->name }} ({{ $cliente->email }})</option>
                        @endforeach
                    </select>
                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">El usuario debe existir previamente en el Directorio de Usuarios Globales.</small>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="document.getElementById('modal-create').style.display='none'" style="background: white; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">Cancelar</button>
                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 600; cursor: pointer;">Crear Empresa</button>
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
