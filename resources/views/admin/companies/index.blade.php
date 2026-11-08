@extends('layouts.backoffice')

@section('title', 'Empresas | Staff Sensoría')
@section('header', 'Directorio de Empresas (Tenants)')

@section('content')

    <!-- Alertas de Éxito -->
    @if(session('status'))
        <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #bbf7d0;">
            ✅ {{ session('status') }}
        </div>
    @endif

    <div style="background: white; padding: 24px; border: 1px solid var(--admin-border); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">

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
                        <!-- Botón que activa el JS enviando los datos de la empresa -->
                        <button type="button"
                                onclick="openCompanyModal({{ $company->id }}, '{{ $company->trade_name }}', {{ $company->is_active ? 'true' : 'false' }})"
                                style="background: none; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: #475569; font-weight: 600;">
                            Opciones ⚙️
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- EL MODAL (Oculto por defecto) -->
    <div id="companyModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 32px; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h3 style="margin: 0; color: #0f172a; font-size: 1.25rem;">Gestión de Empresa</h3>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8;">&times;</button>
            </div>

            <p style="color: #475569; margin-bottom: 8px;">Empresa seleccionada:</p>
            <h4 id="modalCompanyName" style="margin: 0 0 24px 0; color: #0f172a; font-size: 1.5rem;">Nombre Empresa</h4>

            <!-- Formulario para Cambiar Estado -->
            <form id="toggleStatusForm" method="POST" action="">
                @csrf
                @method('PATCH')

                <div style="padding: 16px; border-radius: 8px; margin-bottom: 24px;" id="modalWarningBox">
                    <p style="margin: 0; font-size: 0.9rem; font-weight: 600;" id="modalWarningText">Texto de advertencia aquí</p>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="closeModal()" style="padding: 10px 16px; background: white; border: 1px solid #cbd5e1; color: #475569; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        Cancelar
                    </button>
                    <button type="submit" id="modalSubmitBtn" style="padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: white;">
                        Confirmar Acción
                    </button>
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
                ]
            });
        });

        // Lógica del Modal en Vanilla JS
        const modal = document.getElementById('companyModal');
        const form = document.getElementById('toggleStatusForm');
        const companyNameEl = document.getElementById('modalCompanyName');
        const warningBox = document.getElementById('modalWarningBox');
        const warningText = document.getElementById('modalWarningText');
        const submitBtn = document.getElementById('modalSubmitBtn');

        function openCompanyModal(id, name, isActive) {
            // Asignar datos
            companyNameEl.textContent = name;

            // Construir URL dinámica para el formulario
            form.action = `/admin/empresas/${id}/toggle-status`;

            // Configurar diseño según el estado actual
            if(isActive) {
                warningBox.style.background = '#fee2e2'; // Fondo rojo claro
                warningBox.style.color = '#991b1b';
                warningText.innerHTML = "Al suspender esta empresa, <strong>ninguno de sus usuarios</strong> podrá acceder al panel de la plataforma.";
                submitBtn.style.background = '#dc2626'; // Botón rojo
                submitBtn.textContent = 'Suspender Empresa';
            } else {
                warningBox.style.background = '#dcfce7'; // Fondo verde claro
                warningBox.style.color = '#166534';
                warningText.innerHTML = " Al reactivar esta empresa, se restaurará el acceso inmediato a todo su personal.";
                submitBtn.style.background = '#16a34a'; // Botón verde
                submitBtn.textContent = 'Reactivar Empresa';
            }

            // Mostrar modal
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera de la caja blanca
        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
@endpush
