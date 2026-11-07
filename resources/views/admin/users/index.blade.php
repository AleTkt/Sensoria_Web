@extends('layouts.backoffice')

@section('title', 'Usuarios Globales | Staff Sensoría')
@section('header', 'Directorio Global de Usuarios')

@section('content')
    <div style="background: white; padding: 24px; border: 1px solid var(--admin-border); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">

        <table id="users-table" class="display" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol Global</th>
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
                        <button type="button" title="Gestionar Usuario" style="background: none; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">
                            Gestionar ⚙️
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                order: [[0, 'desc']], // Ordena por los más recientes primero
                columnDefs: [
                    { orderable: false, targets: 5 } // Desactiva el ordenamiento en la columna de botones
                ]
            });
        });
    </script>
@endpush
