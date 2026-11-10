@extends('layouts.backoffice')

@section('title', 'Tickets de Soporte | Sensoría')
@section('header', 'Tickets Globales')

@section('content')
    <div class="admin-card animate-fade-in-up">
        <div class="card-body">
            <table id="ticketsTable" class="display responsive nowrap" style="width:100%">
                <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Empresa</th>
                    <th>Asunto</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tickets as $ticket)
                    <tr>
                        <td><strong>{{ $ticket->ticket_number }}</strong></td>
                        <td>{{ $ticket->company->legal_name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($ticket->subject, 40) }}</td>
                        <td>
                        <span class="badge priority-{{ strtolower($ticket->priority) }}">
                            {{ $ticket->priority }}
                        </span>
                        </td>
                        <td>
                        <span class="badge status-{{ strtolower($ticket->status) }}">
                            {{ $ticket->status }}
                        </span>
                        </td>
                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn-action">
                                <i class="fa-solid fa-eye"></i> Atender
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#ticketsTable').DataTable({
                order: [[5, 'desc']] // Ordenar por fecha más reciente
            });
        });
    </script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/tickets/tickets-messages.css') }}"
@endpush
