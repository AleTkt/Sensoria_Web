@extends('layouts.backoffice')

@section('title', 'Usuarios Globales | Staff Sensoría')
@section('header', 'Directorio Global de Usuarios')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/users/users-index.css') }}">
@endpush

@section('content')

    {{-- Alertas de Éxito o Error --}}
    @if(session('success'))
        <div style="background: rgba(88, 148, 69, 0.12); color: #166534; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(88, 148, 69, 0.3); font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-circle-check" style="color: var(--color-primary, #589445); font-size: 1.2rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #fca5a5; font-weight: 500; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem;"></i>
            <span>{{ session('error') }}</span>
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

        {{-- Botón para Crear Usuario --}}
        <div style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end;">
            <button onclick="document.getElementById('modal-create').style.display='flex'" class="btn-sensoria-primary">
                <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table id="users-table" class="display" style="width:100%">
                <thead>
                <tr>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">ID</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Usuario</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Rol Global</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Estado</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Espacio de Trabajo (Tenant)</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Fecha de Registro</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A); text-align: right;">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 1rem; font-weight: 700; color: #4b5563;">{{ $user->id }}</td>
                        <td style="padding: 1rem;">
                            <strong style="color: var(--color-tertiary, #1B3D1A); font-size: 1rem;">{{ $user->name }}</strong><br>
                            <span style="color: #6b7280; font-size: 0.82rem;"><i class="fa-regular fa-envelope"></i> {{ $user->email }}</span>
                        </td>
                        <td style="padding: 1rem;">
                            @if($user->global_role === 'ADMIN')
                                <span style="background: #fee2e2; color: #991b1b; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; border: 1px solid #fca5a5;">ADMIN</span>
                            @elseif($user->global_role === 'SOPORTE')
                                <span style="background: #e0f2fe; color: #0369a1; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; border: 1px solid #bae6fd;">SOPORTE</span>
                            @else
                                <span style="background: #f3f4f6; color: #4b5563; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; border: 1px solid #e5e7eb;">CLIENTE B2B</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            @if($user->is_active)
                                <span style="background: #d1fae5; color: #065f46; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;">Activo</span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;">Suspendido</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            @if($user->companies->count() > 0)
                                <span style="font-weight: 600; color: #374151;">{{ $user->companies->first()->trade_name }}</span>
                                @if($user->companies->count() > 1)
                                    <span style="font-size: 0.75rem; color: #6b7280; background: #f3f4f6; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600; margin-left: 0.25rem;">(+{{ $user->companies->count() - 1 }})</span>
                                @endif
                            @else
                                <span style="color: #9ca3af; font-style: italic; font-size: 0.85rem;">Sin asignar</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; color: #6b7280; font-size: 0.875rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 6px;">
                                {{-- Botón Editar --}}
                                <button onclick="document.getElementById('modal-edit-{{ $user->id }}').style.display='flex'" class="btn-action-icon" title="Editar Usuario">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                {{-- Botón Modal Restablecer Contraseña --}}
                                <button onclick="document.getElementById('modal-reset-{{ $user->id }}').style.display='flex'" class="btn-action-icon" title="Restablecer Contraseña" style="color: #0369a1;">
                                    <i class="fa-solid fa-key"></i>
                                </button>

                                {{-- Botón Modal Suspender / Reactivar --}}
                                @if($user->id !== auth()->id())
                                    <button onclick="document.getElementById('modal-status-{{ $user->id }}').style.display='flex'" class="btn-action-icon" title="{{ $user->is_active ? 'Suspender Usuario' : 'Reactivar Usuario' }}" style="color: {{ $user->is_active ? '#dc2626' : '#16a34a' }};">
                                        <i class="fa-solid {{ $user->is_active ? 'fa-ban' : 'fa-circle-check' }}"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Modal de Edición --}}
                    <div id="modal-edit-{{ $user->id }}" class="sensoria-modal-overlay">
                        <div class="sensoria-modal-box">
                            <button onclick="document.getElementById('modal-edit-{{ $user->id }}').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>

                            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--color-tertiary, #1B3D1A); font-weight: 800; font-size: 1.2rem;">
                                <i class="fa-solid fa-user-pen" style="color: var(--color-primary, #589445);"></i> Editar Usuario: {{ $user->name }}
                            </h3>

                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div style="margin-bottom: 1rem;">
                                    <label class="sensoria-label">Nombre *</label>
                                    <input type="text" name="name" value="{{ $user->name }}" required class="sensoria-input">
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <label class="sensoria-label">Correo Electrónico *</label>
                                    <input type="email" name="email" value="{{ $user->email }}" required class="sensoria-input">
                                </div>

                                <div style="margin-bottom: 1.5rem;">
                                    <label class="sensoria-label">Rol Global *</label>
                                    <select name="global_role" required class="sensoria-input">
                                        <option value="CLIENTE" {{ $user->global_role == 'CLIENTE' ? 'selected' : '' }}>CLIENTE B2B</option>
                                        <option value="SOPORTE" {{ $user->global_role == 'SOPORTE' ? 'selected' : '' }}>SOPORTE</option>
                                        <option value="ADMIN" {{ $user->global_role == 'ADMIN' ? 'selected' : '' }}>ADMINISTRADOR</option>
                                    </select>
                                </div>

                                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 2rem;">
                                    <button type="button" onclick="document.getElementById('modal-edit-{{ $user->id }}').style.display='none'" class="btn-sensoria-secondary">Cancelar</button>
                                    <button type="submit" class="btn-sensoria-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Modal Confirmación Restablecer Contraseña --}}
                    <div id="modal-reset-{{ $user->id }}" class="sensoria-modal-overlay">
                        <div class="sensoria-modal-box" style="max-width: 420px;">
                            <button onclick="document.getElementById('modal-reset-{{ $user->id }}').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>

                            <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--color-tertiary, #1B3D1A); font-weight: 800; font-size: 1.2rem;">
                                <i class="fa-solid fa-key" style="color: #0369a1;"></i> Restablecer Contraseña
                            </h3>
                            <p style="color: #4b5563; margin-bottom: 1.5rem; font-size: 0.92rem; line-height: 1.5;">
                                ¿Estás seguro que deseas enviar un correo de restablecimiento de contraseña al usuario <strong style="color: #111827;">{{ $user->name }}</strong>?
                            </p>

                            <form action="{{ route('admin.users.password-reset', $user->id) }}" method="POST">
                                @csrf
                                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                    <button type="button" onclick="document.getElementById('modal-reset-{{ $user->id }}').style.display='none'" class="btn-sensoria-secondary">Cancelar</button>
                                    <button type="submit" class="btn-sensoria-primary" style="background-color: #0369a1;">Sí, Enviar Correo</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Modal Confirmación Suspender/Reactivar --}}
                    @if($user->id !== auth()->id())
                        <div id="modal-status-{{ $user->id }}" class="sensoria-modal-overlay">
                            <div class="sensoria-modal-box" style="max-width: 420px;">
                                <button onclick="document.getElementById('modal-status-{{ $user->id }}').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>

                                <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--color-tertiary, #1B3D1A); font-weight: 800; font-size: 1.2rem;">
                                    {{ $user->is_active ? 'Suspender Usuario' : 'Reactivar Usuario' }}
                                </h3>
                                <p style="color: #4b5563; margin-bottom: 1.5rem; font-size: 0.92rem; line-height: 1.5;">
                                    ¿Estás seguro que deseas {{ $user->is_active ? 'suspender' : 'reactivar' }} al usuario <strong style="color: #111827;">{{ $user->name }}</strong>?
                                    @if($user->is_active)
                                        <br><br><span style="color: #dc2626; font-size: 0.82rem; font-weight: 600;">Al suspenderlo, no podrá acceder a la plataforma. Sus datos e historial se mantendrán intactos.</span>
                                    @endif
                                </p>

                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                        <button type="button" onclick="document.getElementById('modal-status-{{ $user->id }}').style.display='none'" class="btn-sensoria-secondary">Cancelar</button>
                                        <button type="submit" class="btn-sensoria-primary" style="background-color: {{ $user->is_active ? '#dc2626' : '#16a34a' }};">
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
    </div>

    {{-- Modal para Crear Usuario --}}
    <div id="modal-create" class="sensoria-modal-overlay">
        <div class="sensoria-modal-box">
            <button onclick="document.getElementById('modal-create').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>

            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--color-tertiary, #1B3D1A); font-weight: 800; font-size: 1.2rem;">
                <i class="fa-solid fa-user-plus" style="color: var(--color-primary, #589445);"></i> Crear Nuevo Usuario
            </h3>

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div style="margin-bottom: 1rem;">
                    <label class="sensoria-label">Nombre Completo *</label>
                    <input type="text" name="name" required class="sensoria-input" placeholder="Nombre del usuario">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="sensoria-label">Correo Electrónico *</label>
                    <input type="email" name="email" required class="sensoria-input" placeholder="usuario@correo.com">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="sensoria-label">Rol Global *</label>
                    <select name="global_role" required class="sensoria-input">
                        <option value="CLIENTE">CLIENTE B2B</option>
                        <option value="SOPORTE">SOPORTE</option>
                        <option value="ADMIN">ADMINISTRADOR</option>
                    </select>
                    <small style="color: #6b7280; font-size: 0.78rem; display: block; margin-top: 0.4rem;">Nota: La contraseña se generará automáticamente y se enviará por correo.</small>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" onclick="document.getElementById('modal-create').style.display='none'" class="btn-sensoria-secondary">Cancelar</button>
                    <button type="submit" class="btn-sensoria-primary">Crear Usuario</button>
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
