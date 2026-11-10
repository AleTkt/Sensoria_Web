<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Listado general de tickets para el Staff.
     */
    public function index()
    {
        $tickets = Ticket::with(['company', 'user'])
            ->orderByRaw("
                CASE status
                    WHEN 'ABIERTO' THEN 1
                    WHEN 'EN_PROCESO' THEN 2
                    WHEN 'RESUELTO' THEN 3
                    WHEN 'CERRADO' THEN 4
                    ELSE 5
                END
            ")
            ->latest()
            ->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Detalle del ticket y chat.
     */
    public function show($id)
    {
        $ticket = Ticket::with(['company', 'user', 'messages.user', 'assignedAgent'])
            ->findOrFail($id);

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Enviar una respuesta al ticket.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'status' => 'nullable|string|in:ABIERTO,EN_PROCESO,RESUELTO,CERRADO',
            'is_internal_note' => 'nullable|boolean'
        ]);

        $ticket = Ticket::findOrFail($id);

        // Crear el mensaje
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(), // El Admin que responde
            'message' => $request->message,
            'is_internal_note' => $request->has('is_internal_note'),
        ]);

        // Actualizar el estado del ticket y asignar automáticamente si no tiene agente
        $ticket->status = $request->status ?? 'EN_PROCESO';

        if (!$ticket->assigned_to_user_id) {
            $ticket->assigned_to_user_id = Auth::id();
        }

        $ticket->save();

        return back()->with('success', 'Respuesta enviada exitosamente.');
    }
}
