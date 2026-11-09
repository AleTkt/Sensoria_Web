<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id');
        $tickets = Ticket::where('company_id', $companyId)
            ->with(['user', 'assignedAgent'])
            ->latest()
            ->get();

        return view('workspace.tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:TECNICO,FACTURACION,GENERAL,DISPOSITIVO'],
            'priority' => ['required', 'in:BAJA,MEDIA,ALTA,CRITICA'],
            'description' => ['required', 'string'],
        ]);

        $companyId = session('current_company_id');

        // Creamos el ticket con número único
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-' . strtoupper(Str::random(6)),
            'company_id' => $companyId,
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'ABIERTO',
        ]);

        // Insertamos la descripción inicial en la tabla de mensajes del ticket
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->description,
            'is_internal_note' => false,
        ]);

        return redirect()->route('workspace.tickets.index')->with('success', 'Ticket de soporte creado exitosamente.');
    }

    public function show($id)
    {
        $companyId = session('current_company_id');

        // Cargamos el ticket asegurando que pertenezca a la empresa en sesión
        // y traemos la relación de mensajes ordenados cronológicamente
        $ticket = Ticket::with(['user', 'assignedAgent', 'messages.user'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return view('workspace.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => ['required', 'string'],
        ]);

        $companyId = session('current_company_id');
        $ticket = Ticket::where('company_id', $companyId)->findOrFail($id);

        // Si el ticket estaba cerrado y el cliente responde, lo reabrimos
        if (in_array($ticket->status, ['RESUELTO', 'CERRADO'])) {
            $ticket->update(['status' => 'ABIERTO']);
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'message' => $request->message,
            'is_internal_note' => false,
        ]);

        return redirect()->route('workspace.tickets.show', $ticket->id)
            ->with('success', 'Mensaje enviado correctamente.');
    }
}
