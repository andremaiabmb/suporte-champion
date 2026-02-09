<?php

namespace App\Http\Controllers\Dashboard\Support;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);

        $status  = $request->string('status')->value();
        $sector  = $request->integer('sector');

        $sectors = Sector::query()->orderBy('name')->get(['id','name']);

        $tickets = Ticket::query()
            ->with([
                'author:id,name',
                'assignee:id,name',
                'sector:id,name',
            ])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($sector, fn($q) => $q->where('sector_id', $sector))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.support.index', compact('tickets','sectors'));
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'author:id,name',
            'assignee:id,name',
            'sector:id,name',
            // relações opcionais; só serão carregadas se existirem
            'messages.author:id,name',
            'messages.attachments',
            'transitions',
        ]);

        $sectors = Sector::query()->orderBy('name')->get(['id','name']);

        return view('dashboard.support.show', compact('ticket','sectors'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        // Validação mínima (ajuste conforme seu FormRequest se usar um)
        $data = $request->validate([
            'sector_id'   => ['nullable','integer','exists:sectors,id'],
            'priority'    => ['required','in:low,normal,high,urgent'],
            'status'      => ['required','in:open,in_progress,waiting,resolved,closed,reopened'],
            'assignee_id' => ['nullable','integer','exists:users,id'],
            'message'     => ['nullable','string','max:10000'],
            'is_internal' => ['nullable','boolean'],
            'attachments.*' => ['nullable','file','max:10240'], // 10MB
        ]);

        DB::transaction(function () use ($ticket, $data, $request) {
            $actor = $request->user();

            // Log de transições (se a tabela/model existir)
            $logChange = function (string $field, $old, $new) use ($ticket, $actor) {
                if (class_exists(\App\Models\TicketTransition::class) && $old !== $new) {
                    \App\Models\TicketTransition::create([
                        'ticket_id' => $ticket->id,
                        'user_id'   => $actor->id,
                        'field'     => $field,
                        'old_value' => is_scalar($old) ? $old : json_encode($old),
                        'new_value' => is_scalar($new) ? $new : json_encode($new),
                    ]);
                }
            };

            // Aplicar mudanças com log
            $original = $ticket->getOriginal();

            foreach (['sector_id','priority','status','assignee_id'] as $f) {
                if (array_key_exists($f, $data)) {
                    $old = $ticket->{$f};
                    $ticket->{$f} = $data[$f];
                    $logChange($f, $old, $ticket->{$f});
                }
            }

            $ticket->save();

            // Mensagem opcional
            if (!empty($data['message'])) {
                if (class_exists(\App\Models\TicketMessage::class)) {
                    $msg = new \App\Models\TicketMessage();
                    $msg->ticket_id  = $ticket->id;
                    $msg->user_id    = $actor->id;
                    $msg->message    = $data['message'];
                    $msg->is_internal= (bool)($data['is_internal'] ?? false);
                    $msg->save();

                    // Anexos
                    if ($request->hasFile('attachments')) {
                        foreach ($request->file('attachments') as $file) {
                            if (!$file) continue;
                            $path = $file->store('tickets/'.date('Y/m'), 'public');

                            if (class_exists(\App\Models\TicketAttachment::class)) {
                                \App\Models\TicketAttachment::create([
                                    'ticket_message_id' => $msg->id,
                                    'path'              => $path,
                                    'original_name'     => $file->getClientOriginalName(),
                                    'size'              => $file->getSize(),
                                    'mime'              => $file->getMimeType(),
                                ]);
                            }
                        }
                    }
                }
            }
        });

        return back()->with('status', 'Chamado atualizado com sucesso.');
    }

    public function claim(Request $request, Ticket $ticket)
    {
        $this->authorize('claim', $ticket);

        $actor = $request->user();
        $old   = $ticket->assignee_id;

        $ticket->assignee_id = $actor->id;
        $ticket->save();

        if (class_exists(\App\Models\TicketTransition::class)) {
            \App\Models\TicketTransition::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $actor->id,
                'field'     => 'assignee_id',
                'old_value' => $old,
                'new_value' => $ticket->assignee_id,
            ]);
        }

        return back()->with('status', 'Chamado assumido.');
    }

    public function release(Request $request, Ticket $ticket)
    {
        // **corrigido**: ability certa é 'release' (antes costumam usar 'update' por engano)
        $this->authorize('release', $ticket);

        $actor = $request->user();
        $old   = $ticket->assignee_id;

        $ticket->assignee_id = null;
        $ticket->save();

        if (class_exists(\App\Models\TicketTransition::class)) {
            \App\Models\TicketTransition::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $actor->id,
                'field'     => 'assignee_id',
                'old_value' => $old,
                'new_value' => null,
            ]);
        }

        return back()->with('status', 'Chamado liberado.');
    }
}
