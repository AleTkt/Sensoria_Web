<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    // Obtener tickets de la empresa
    public function index(Request $request)
    {
        $companyId = $request->header('X-Company-Id');

        $tickets = Ticket::where('company_id', $companyId)
            ->latest()
            ->get(['id', 'ticket_number', 'subject', 'category', 'priority', 'status', 'created_at']);

        return response()->json(['data' => $tickets]);
    }

    // Crear un ticket
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'category' => 'required|string',
            'priority' => 'required|string',
            'description' => 'required|string',
        ]);

        $companyId = $request->header('X-Company-Id');

        $ticket = Ticket::create([
            'ticket_number' => 'TKT-' . strtoupper(Str::random(6)),
            'company_id' => $companyId,
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'ABIERTO',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->description,
            'is_internal_note' => false,
        ]);

        return response()->json(['message' => 'Ticket creado con éxito', 'ticket_id' => $ticket->id], 201);
    }

    // Ver mensajes de un ticket específico
    public function show(Request $request, $id)
    {
        $companyId = $request->header('X-Company-Id');

        $ticket = Ticket::with(['messages' => function($query) {
            $query->where('is_internal_note', false)->with('user:id,name,global_role');
        }])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return response()->json(['data' => $ticket]);
    }

    // Responder desde Android
    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        $companyId = $request->header('X-Company-Id');
        $ticket = Ticket::where('company_id', $companyId)->findOrFail($id);

        if (in_array($ticket->status, ['RESUELTO', 'CERRADO'])) {
            $ticket->update(['status' => 'ABIERTO']);
        }

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_internal_note' => false,
        ]);

        // Retornamos el mensaje recién creado con su usuario
        return response()->json(['data' => $message->load('user:id,name,global_role')], 201);
    }
}
