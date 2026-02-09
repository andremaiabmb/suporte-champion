<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStoreRequest;
use App\Models\Sector;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Notifications\TicketCreated;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tickets = Ticket::with('sector')
            ->mine($user)
            ->latest()
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        $sectors = Sector::where('is_active', true)->orderBy('name')->get();
        return view('support.create', compact('sectors'));
    }

    public function store(TicketStoreRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $ticket = Ticket::create([
            'user_id'     => $user->id,
            'sector_id'   => $data['sector_id'] ?? null,
            'subject'     => $data['subject'],
            'description' => $data['description'],
            'priority'    => $data['priority'] ?? 'normal',
        ]);

        $message = TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'message'     => $ticket->description,
            'is_internal' => false,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/'.$ticket->id, 'public');
                TicketAttachment::create([
                    'ticket_id'     => $ticket->id,
                    'message_id'    => $message->id,
                    'original_name' => $file->getClientOriginalName(),
                    'disk'          => 'public',
                    'path'          => $path,
                    'size'          => $file->getSize(),
                    'mime'          => $file->getClientMimeType(),
                ]);
            }
        }

        $user->notify(new TicketCreated($ticket));

        return redirect()->route('support.show', $ticket->code)
            ->with('status', 'Chamado aberto com sucesso.');
    }

    public function show(Request $request, string $code)
    {
        $ticket = Ticket::with([
                'sector','assignee',
                'messages.author','messages.attachments',
                'attachments','transitions.actor',
            ])
            ->where('code', $code)
            ->firstOrFail();

        $this->authorize('view', $ticket);

        return view('support.show', compact('ticket'));
    }
}
