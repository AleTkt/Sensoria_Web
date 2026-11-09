@extends('layouts.app')

@section('title', 'Ticket ' . $ticket->ticket_number . ' | Sensoría')
@section('header', 'Detalle de Solicitud: ' . $ticket->ticket_number)

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">

        <!-- Hilo de Conversación -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px;">
                <h2 style="margin: 0; font-size: 1.2rem; color: #0f172a;">{{ $ticket->subject }}</h2>
                <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #64748b;">Abierto por {{ $ticket->user->name }} el {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Lista de Mensajes -->
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px; max-height: 500px; overflow-y: auto; padding-right: 8px;">
                @foreach($ticket->messages as $msg)
                    @if(!$msg->is_internal_note)
                        <div style="background: {{ $msg->user_id === Auth::id() ? '#f8fafc' : 'rgba(88, 148, 69, 0.05)' }}; border: 1px solid {{ $msg->user_id === Auth::id() ? '#e2e8f0' : 'rgba(88, 148, 69, 0.2)' }}; border-radius: 8px; padding: 16px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-weight: 600; font-size: 0.85rem; color: {{ $msg->user_id === Auth::id() ? '#334155' : 'var(--color-primary)' }};">
                                {{ $msg->user->name }} {{ $msg->user->global_role === 'SOPORTE' ? '(Soporte Técnico)' : '' }}
                            </span>
                                <span style="font-size: 0.75rem; color: #94a3b8;">{{ $msg->created_at->format('d/m/y H:i') }}</span>
                            </div>
                            <p style="margin: 0; font-size: 0.95rem; color: #1e293b; white-space: pre-wrap;">{{ $msg->message }}</p>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Formulario de Respuesta -->
            @if(!in_array($ticket->status, ['CERRADO']))
                <form action="{{ route('workspace.tickets.reply', $ticket->id) }}" method="POST" style="border-top: 1px solid #e2e8f0; padding-top: 16px;">
                    @csrf
                    <textarea name="message" rows="3" placeholder="Escribe tu respuesta aquí..." required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; margin-bottom: 12px; box-sizing: border-box; resize: vertical;"></textarea>
                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" style="background: var(--color-primary); color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                            <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Enviar Respuesta
                        </button>
                    </div>
                </form>
            @else
                <div style="background: #f1f5f9; padding: 16px; border-radius: 8px; text-align: center; color: #64748b; font-size: 0.9rem;">
                    Este ticket ha sido cerrado. Si necesitas más ayuda, por favor abre un nuevo ticket.
                </div>
            @endif
        </div>

        <!-- Panel Lateral de Información -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; font-size: 1rem; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">Información del Ticket</h3>

            <div style="margin-top: 16px;">
                <p style="margin: 0 0 4px 0; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Estado</p>
                <span style="font-weight: bold; font-size: 0.85rem; color: #2563eb; background: rgba(37, 99, 235, 0.1); padding: 4px 8px; border-radius: 4px;">{{ $ticket->status }}</span>
            </div>

            <div style="margin-top: 16px;">
                <p style="margin: 0 0 4px 0; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Categoría</p>
                <p style="margin: 0; font-weight: 600; font-size: 0.9rem; color: #334155;">{{ $ticket->category }}</p>
            </div>

            <div style="margin-top: 16px;">
                <p style="margin: 0 0 4px 0; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Prioridad</p>
                <p style="margin: 0; font-weight: 600; font-size: 0.9rem; color: #334155;">{{ $ticket->priority }}</p>
            </div>

            <div style="margin-top: 24px;">
                <a href="{{ route('workspace.tickets.index') }}" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 10px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
@endsection
