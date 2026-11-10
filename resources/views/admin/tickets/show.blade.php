@extends('layouts.backoffice')

@section('title', 'Ticket #' . $ticket->ticket_number)
@section('header', 'Atención de Ticket')

@section('header-actions')
    <a href="{{ route('admin.tickets.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Volver a Lista
    </a>
@endsection

@section('content')
    <div class="ticket-layout">
        <!-- Panel Izquierdo: Información del Ticket -->
        <div class="ticket-info-panel admin-card">
            <div class="card-body">
                <h3 class="info-title">Detalles de Solicitud</h3>

                <div class="info-group">
                    <label>Número de Ticket</label>
                    <p><strong>{{ $ticket->ticket_number }}</strong></p>
                </div>

                <div class="info-group">
                    <label>Empresa Solicitante</label>
                    <p>{{ $ticket->company->legal_name ?? 'Desconocida' }}</p>
                    <small>Creado por: {{ $ticket->user->name ?? 'Usuario borrado' }}</small>
                </div>

                <div class="info-group">
                    <label>Asunto</label>
                    <p>{{ $ticket->subject }}</p>
                </div>

                <div class="info-row">
                    <div class="info-group">
                        <label>Categoría</label>
                        <p>{{ $ticket->category }}</p>
                    </div>
                    <div class="info-group">
                        <label>Prioridad</label>
                        <p>{{ $ticket->priority }}</p>
                    </div>
                </div>

                <div class="info-group">
                    <label>Agente Asignado</label>
                    <p>{{ $ticket->assignedAgent->name ?? 'Sin asignar' }}</p>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Hilo de Chat -->
        <div class="ticket-chat-panel admin-card">
            <div class="chat-header">
                <h4>Historial de Conversación</h4>
                <span class="badge status-{{ strtolower($ticket->status) }}">{{ $ticket->status }}</span>
            </div>

            <div class="chat-history" id="chatHistory">
                @foreach($ticket->messages as $msg)
                    @php
                        // Validamos si el autor del mensaje es el usuario actual en sesión
                        $isMine = $msg->user_id === Auth::id();
                    @endphp
                    <div class="chat-bubble-wrapper {{ $isMine ? 'staff' : 'client' }} {{ $msg->is_internal_note ? 'internal-note' : '' }}">
                        <div class="chat-bubble">
                            <div class="bubble-meta">
                                <strong>{{ $isMine ? 'Tú' : $msg->user->name }}</strong>
                                <span>{{ $msg->created_at->timezone('America/El_Salvador')->format('d/m H:i') }}</span>
                            </div>
                            <div class="bubble-text">
                                {!! nl2br(e($msg->message)) !!}
                            </div>
                            @if($msg->is_internal_note)
                                <small class="note-badge"><i class="fa-solid fa-lock"></i> Nota Interna</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Área de Respuesta -->
            <div class="chat-reply-area">
                <!-- Agregamos un ID al form para controlarlo con jQuery -->
                <form id="ticketReplyForm" action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <textarea name="message" rows="3" placeholder="Escribe tu respuesta aquí..." required></textarea>

                    <div class="reply-actions">
                        <div class="options">
                            <select name="status" class="form-select">
                                <option value="EN_PROCESO" {{ $ticket->status == 'EN_PROCESO' ? 'selected' : '' }}>Marcar como En Proceso</option>
                                <option value="RESUELTO" {{ $ticket->status == 'RESUELTO' ? 'selected' : '' }}>Marcar como Resuelto</option>
                                <option value="CERRADO" {{ $ticket->status == 'CERRADO' ? 'selected' : '' }}>Cerrar Ticket</option>
                            </select>
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_internal_note" value="1"> Nota privada (Solo Staff)
                            </label>
                        </div>
                        <!-- Agregamos un ID al botón -->
                        <button type="submit" id="btnSubmitReply" class="btn-primary">
                            <i class="fa-solid fa-paper-plane"></i> Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const chat = document.getElementById('chatHistory');
            if(chat) {
                chat.scrollTop = chat.scrollHeight;
            }

            $('#ticketReplyForm').on('submit', function(e) {
                let $btn = $('#btnSubmitReply');

                if (this.checkValidity()) {
                    $btn.prop('disabled', true);

                    $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Enviando...');
                    $btn.css({'opacity': '0.7', 'cursor': 'not-allowed'});

                }
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/tickets/tickets-messages.css') }}">
@endpush
