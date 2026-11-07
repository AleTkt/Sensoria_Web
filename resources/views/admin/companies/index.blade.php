@extends('layouts.backoffice')

@section('title', 'Empresas | Staff Sensoría')
@section('header', 'Directorio de Empresas (Tenants)')

@section('content')
    <div style="background: white; padding: 24px; border: 1px solid var(--admin-border); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">

        <!-- Inicialización de DataTables en HTML -->
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
                    <td>{{ $company->sector }}</td>
                    <td>
                        @php
                            // Extraemos el primer usuario con rol PROPIETARIO
                            $owner = $company->users->first();
                        @endphp
                        {{ $owner ? $owner->name : 'Sin Propietario Asignado' }}
                    </td>
                    <td>
                        @if($company->is_active)
                            <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">ACTIVA</span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">SUSPENDIDA</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <button type="button" title="Ver Detalles" style="background: none; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">
                            Opciones ⚙️
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection

@push('scripts')
    <!-- Activación de DataTables con jQuery -->
    <script>
        $(document).ready(function() {
            $('#companies-table').DataTable({
                // Toma la configuración en español automáticamente del layout backoffice
                order: [[0, 'desc']], // Ordenar por ID descendente por defecto
                columnDefs: [
                    { orderable: false, targets: 5 } // Deshabilita ordenar por la columna "Acciones"
                ]
            });
        });
    </script>
@endpush
