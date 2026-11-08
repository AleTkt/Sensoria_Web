@extends('layouts.backoffice')

@section('title', 'Usuarios Globales | Staff Sensoría')
@section('header', 'Directorio Global de Usuarios')

@section('content')

    {{-- Alertas de Éxito o Error --}}
    @if(session('success'))
        <div style="background: #dcfce3; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;">
            {{ session('error') }}
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

        {{-- Botón para Crear Usuario --}}
        <div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
            <button onclick="document.getElementById('modal-create').style.display='flex'" style="background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                + Nuevo Usuario
            </button>
        </div>

        <table id="users-table" class="display" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol Global</th>
                <th>Estado</th>
                <th>Espacio de Trabajo (Tenant)</th>
                <th>Fecha de Registro</th>
                <th style="text-align: right;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        <strong style="color: #0f172a;">{{ $user->name }}</strong><br>
                        <small style="color: #64748b;">{{ $user->email }}</small>
                    </td>
                    <td>
                        @if($user->global_role === 'ADMIN')
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">ADMIN</span>
                        @elseif($user->global_role === 'SOPORTE')
                            <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">SOPORTE</span>
                        @else
                            <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">CLIENTE B2B</span>
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                            <span style="color: #16a34a; font-weight: 600; font-size: 0.85rem;">🟢 Activo</span>
                        @else
                            <span style="color: #dc2626; font-weight: 600; font-size: 0.85rem;">🔴 Suspendido</span>
                        @endif
                    </td>
                    <td>
                        @if($user->companies->count() > 0)
                            {{ $user->companies->first()->trade_name }}
                            @if($user->companies->count() > 1)
                                <span style="font-size: 0.75rem; color: #64748b;">(+{{ $user->companies->count() - 1 }})</span>
                            @endif
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Sin asignar</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td style="text-align: right;">
                        <button onclick="document.getElementById('modal-manage-{{ $user->id }}').style.display='flex'" title="Gestionar Usuario" style="background: none; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">
                            Gestionar ⚙️
                        </button>
                    </td>
                </tr>

                {{-- Modal de Gestión por Usuario --}}
                <div id="modal-manage-{{ $user->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; width: 100%; max-width: 500px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative;">

                        <button onclick="document.getElementById('modal-manage-{{ $user->id }}').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>

                        <h3 style="margin-top: 0; margin-bottom: 20px; color: #0f172a;">Gestionar Usuario: {{ $user->name }}</h3>

                        {{-- Formulario de Edición --}}
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e2e8f0;">
                            @csrf
                            @method('PUT')

                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Nombre</label>
                                <input type="text" name="name" value="{{ $user->name }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                            </div>

                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Correo Electrónico</label>
                                <input type="email" name="email" value="{{ $user->email }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Rol Global</label>
                                <select name="global_role" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                                    <option value="CLIENTE" {{ $user->global_role == 'CLIENTE' ? 'selected' : '' }}>CLIENTE B2B</option>
                                    <option value="SOPORTE" {{ $user->global_role == 'SOPORTE' ? 'selected' : '' }}>SOPORTE</option>
                                    <option value="ADMIN" {{ $user->global_role == 'ADMIN' ? 'selected' : '' }}>ADMINISTRADOR</option>
                                </select>
                            </div>

                            <button type="submit" style="width: 100%; background: #2563eb; color: white; border: none; padding: 10px; border-radius: 4px; font-weight: 600; cursor: pointer;">
                                Guardar Cambios
                            </button>
                        </form>

                        {{-- Acciones Peligrosas / Extras --}}
                        <div style="display: flex; gap: 10px;">
                            {{-- Restablecer Contraseña --}}
                            <form action="{{ route('admin.users.password-reset', $user->id) }}" method="POST" style="flex: 1;">
                                @csrf
                                <button type="submit" onclick="return confirm('¿Enviar correo de restablecimiento de contraseña a este usuario?')" style="width: 100%; background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; padding: 10px; border-radius: 4px; font-weight: 600; cursor: pointer;">
                                    🔑 Enviar Password Reset
                                </button>
                            </form>

                            {{-- Suspender / Reactivar (Bloqueamos si es el mismo usuario logueado) --}}
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('¿Estás seguro de cambiar el estado de acceso de este usuario?')" style="width: 100%; background: {{ $user->is_active ? '#fee2e2' : '#dcfce3' }}; color: {{ $user->is_active ? '#991b1b' : '#166534' }}; border: 1px solid {{ $user->is_active ? '#fecaca' : '#bbf7d0' }}; padding: 10px; border-radius: 4px; font-weight: 600; cursor: pointer;">
                                        {{ $user->is_active ? '🚫 Suspender Acceso' : '✅ Reactivar Acceso' }}
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal para Crear Usuario --}}
    <div id="modal-create" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; width: 100%; max-width: 450px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative;">

            <button onclick="document.getElementById('modal-create').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>

            <h3 style="margin-top: 0; margin-bottom: 20px; color: #0f172a;">Crear Nuevo Usuario</h3>

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Nombre Completo</label>
                    <input type="text" name="name" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Correo Electrónico</label>
                    <input type="email" name="email" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Rol Global</label>
                    <select name="global_role" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                        <option value="CLIENTE">CLIENTE B2B</option>
                        <option value="SOPORTE">SOPORTE</option>
                        <option value="ADMIN">ADMINISTRADOR</option>
                    </select>
                    <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">Nota: La contraseña se generará automáticamente y se enviará por correo.</small>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                    <button type="button" onclick="document.getElementById('modal-create').style.display='none'" style="background: white; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">Cancelar</button>
                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 600; cursor: pointer;">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 6 } // La columna de acciones (ahora es la índice 6 porque agregamos Estado)
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });
        });
    </script>
@endpush
