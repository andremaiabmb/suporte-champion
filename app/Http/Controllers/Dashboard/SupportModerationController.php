<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketTransition;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupportModerationController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::with(['sector', 'author'])
            ->filterStatus($request->status)
            ->filterSector($request->sector)
            ->latest('created_at')
            ->paginate(12);

        $sectors = Sector::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('dashboard.support.moderation', compact('tickets', 'sectors'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load([
            'sector', 'author', 'assignee',
            'messages.author',
            'transitions.user',
        ]);

        return view('dashboard.support.show', compact('ticket'));
    }

    public function assign(Ticket $ticket, Request $request)
    {
        $ticket->update([
            'assignee_id' => $request->user()->id,
            'status'      => $ticket->status === 'open' ? 'in_progress' : $ticket->status,
        ]);

        TicketTransition::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $request->user()->id,
            'field'     => 'assignee_id',
            'old_value' => null,
            'new_value' => (string) $request->user()->id,
        ]);

        return back()->with('ok', 'Chamado assumido.');
    }

    public function reassign(Ticket $ticket, Request $request)
    {
        $request->validate([
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        $old = $ticket->assignee_id;
        $ticket->update(['assignee_id' => $request->assignee_id]);

        TicketTransition::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $request->user()->id,
            'field'     => 'assignee_id',
            'old_value' => (string) $old,
            'new_value' => (string) $request->assignee_id,
        ]);

        return back()->with('ok', 'Responsável atualizado.');
    }

    public function status(Ticket $ticket, Request $request)
    {
        $request->validate([
            'status' => [
                'required',
                Rule::in(['open','in_progress','waiting','resolved','closed','reopened']),
            ],
        ]);

        $old = $ticket->status;
        $ticket->update(['status' => $request->status]);

        TicketTransition::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $request->user()->id,
            'field'     => 'status',
            'old_value' => $old,
            'new_value' => $request->status,
        ]);

        return back()->with('ok', 'Status alterado.');
    }

    public function note(Ticket $ticket, Request $request)
    {
        $request->validate([
            'message'     => ['required','string'],
            'is_internal' => ['nullable','boolean'],
        ]);

        TicketMessage::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => $request->user()->id,
            'message'    => $request->message,
            'is_internal'=> (bool)$request->boolean('is_internal'),
        ]);

        return back()->with('ok', 'Mensagem registrada.');
    }
}
