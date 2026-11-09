@extends('layouts.app')

@section('title', 'Soporte Técnico | Sensoría')
@section('header', 'Gestión de Tickets B2B')

@section('content')
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px;">
            <h2 style="margin: 0; font-size: 1.1rem; color: #0f172a;">Historial de Solicitudes y Soporte</h2>
        </div>

        <!-- Formulario para crear ticket acorde a crm.tickets -->
        <form action="{{ route('workspace.tickets.index') }}" method="POST" style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #e2e8f0;">
            @csrf
            <h3 style="margin-top: 0; font-size: 0.95rem; color: #0f172a;">Abrir Nuevo Ticket</h3>

            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <input type="text" name="subject" placeholder="Asunto del problema" required style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">

                <select name="category" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    <option value="TECNICO">Técnico</option>
                    <option value="FACTURACION">Facturación</option>
                    <option value="GENERAL">General</option>
                    <option value="DISPOSITIVO">Dispositivo</option>
                </select>

                <select name="priority" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    <option value="BAJA">Baja</option>
                    <option value="MEDIA" selected>Media</option>
                    <option value="ALTA">Alta</option>
                    <option value="CRITICA">Crítica</option>
                </select>
            </div>

            <textarea name="description" placeholder="Describe detalladamente el inconveniente para el equipo de soporte..." rows="3" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; margin-bottom: 12px; box-sizing: border-box;"></textarea>

            <button type="submit" style="background: var(--color-primary); color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer;">Registrar Ticket</button>
        </form>

        <!-- Tabla de Tickets con el esquema real -->
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #64748b; font-size: 0.85rem;">
                <th style="padding: 12px;">Código</th>
                <th style="padding: 12px;">Asunto</th>
                <th style="padding: 12px;">Categoría</th>
                <th style="padding: 12px;">Prioridad</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px;">Fecha</th>
            </tr>
            </thead>
            <tbody>
            @forelse($tickets as $ticket)
                <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                    <td style="padding: 12px; font-weight: bold; color: #475569;">{{ $ticket->ticket_number }}</td>
                    <td style="padding: 12px; font-weight: 600; color: #0f172a;">{{ $ticket->subject }}</td>
                    <td style="padding: 12px; color: #64748b;">{{ $ticket->category }}</td>
                    <td style="padding: 12px; color: #64748b;">{{ $ticket->priority }}</td>
                    <td style="padding: 12px;"><span style="font-weight: bold; font-size: 0.75rem; color: #2563eb;">{{ $ticket->status }}</span></td>
                    <td style="padding: 12px; color: #64748b;">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 32px; color: #94a3b8;">No hay tickets de soporte registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
