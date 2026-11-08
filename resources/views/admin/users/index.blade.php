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
                        <div style="display: flex; justify-content: flex-end; gap: 6px;">
                            {{-- Botón Editar --}}
                            <button onclick="document.getElementById('modal-edit-{{ $user->id }}').style.display='flex'" title="Editar Usuario" style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; cursor: pointer; color: #475569;">
                                ✏️
                            </button>

                            {{-- Botón Modal Restablecer Contraseña --}}
                            <button onclick="document.getElementById('modal-reset-{{ $user->id }}').style.display='flex'" title="Restablecer Contraseña" style="background: #e0f2fe; border: 1px solid #bae6fd; padding: 6px 10px; border-radius: 4px; cursor: pointer; color: #0369a1;">
                                🔑
                            </button>

                            {{-- Botón Modal Suspender / Reactivar --}}
                            @if($user->id !== auth()->id())
                                <button onclick="document.getElementById('modal-status-{{ $user->id }}').style.display='flex'" title="{{ $user->is_active ? 'Suspender Usuario' : 'Reactivar Usuario' }}" style="background: {{ $user->is_active ? '#fee2e2' : '#dcfce3' }}; border: 1px solid {{ $user->is_active ? '#fecaca' : '#bbf7d0' }}; padding: 6px 10px; border-radius: 4px; cursor: pointer;">
                                    {{ $user->is_active ? '🚫' : '✅' }}
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- Modal de Edición --}}
                <div id="modal-edit-{{ $user->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; width: 100%; max-width: 450px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative; text-align: left;">

                        <button onclick="document.getElementById('modal-edit-{{ $user->id }}').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>

                        <h3 style="margin-top: 0; margin-bottom: 20px; color: #0f172a;">Editar Usuario: {{ $user->name }}</h3>

                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
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

                            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                                <button type="button" onclick="document.getElementById('modal-edit-{{ $user->id }}').style.display='none'" style="background: white; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">Cancelar</button>
                                <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 600; cursor: pointer;">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Modal Confirmación Restablecer Contraseña --}}
                <div id="modal-reset-{{ $user->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; width: 100%; max-width: 400px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative; text-align: left;">
                        <button onclick="document.getElementById('modal-reset-{{ $user->id }}').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>

                        <h3 style="margin-top: 0; margin-bottom: 16px; color: #0f172a;">Restablecer Contraseña</h3>
                        <p style="color: #475569; margin-bottom: 24px;">¿Estás seguro que deseas enviar un correo de restablecimiento de contraseña al usuario <strong>{{ $user->name }}</strong>?</p>

                        <form action="{{ route('admin.users.password-reset', $user->id) }}" method="POST">
                            @csrf
                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" onclick="document.getElementById('modal-reset-{{ $user->id }}').style.display='none'" style="background: white; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">Cancelar</button>
                                <button type="submit" style="background: #0369a1; color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 600; cursor: pointer;">Sí, Enviar Correo</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Modal Confirmación Suspender/Reactivar --}}
                @if($user->id !== auth()->id())
                    <div id="modal-status-{{ $user->id }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                        <div style="background: white; width: 100%; max-width: 400px; border-radius: 8px; padding: 24px; box-sizing: border-box; position: relative; text-align: left;">
                            <button onclick="document.getElementById('modal-status-{{ $user->id }}').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>

                            <h3 style="margin-top: 0; margin-bottom: 16px; color: #0f172a;">
                                {{ $user->is_active ? 'Suspender Usuario' : 'Reactivar Usuario' }}
                            </h3>
                            <p style="color: #475569; margin-bottom: 24px;">
                                ¿Estás seguro que deseas {{ $user->is_active ? 'suspender' : 'reactivar' }} al usuario <strong>{{ $user->name }}</strong>?
                                @if($user->is_active)
                                    <br><br><span style="color: #dc2626; font-size: 0.85rem;">Al suspenderlo, no podrá acceder a la plataforma. Sus datos e historial se mantendrán intactos.</span>
                                @endif
                            </p>

                            <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <button type="button" onclick="document.getElementById('modal-status-{{ $user->id }}').style.display='none'" style="background: white; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">Cancelar</button>
                                    <button type="submit" style="background: {{ $user->is_active ? '#dc2626' : '#16a34a' }}; color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 600; cursor: pointer;">
                                        Sí, {{ $user->is_active ? 'Suspender' : 'Reactivar' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

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
                    { orderable: false, targets: 6 }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });
        });
    </script>
@endpush
