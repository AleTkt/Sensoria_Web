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
}
