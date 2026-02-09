<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Opcional: se existir o modelo de setores
use App\Models\Sector;

class MyTicketsController extends Controller
{
    /**
     * GET /suporte  -> support.my.index
     * Lista os chamados do usuário autenticado (com filtros simples).
     */
    public function index(Request $request)
    {
        $tickets = Ticket::where('user_id', $request->user()->id)
            ->when($request->filled('status'), fn($q) =>
                $q->where('status', $request->string('status'))
            )
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q');
                $q->where(function ($sub) use ($term) {
                    $sub->where('code', 'like', "%{$term}%")
                        ->orWhere('subject', 'like', "%{$term}%");
                });
            })
            ->with(['sector'])
            ->latest()
            ->paginate(10);

        return view('support.my.index', compact('tickets'));
    }

    /**
     * GET /suporte/novo -> support.my.create
     * Exibe o formulário para abrir um chamado.
     */
    public function create()
    {
        // Carrega setores se o modelo existir; senão, envia coleção vazia
        $sectors = class_exists(Sector::class)
            ? Sector::query()->orderBy('name')->get(['id','name'])
            : collect();

        return view('support.my.create', compact('sectors'));
    }

    /**
     * POST /suporte/novo -> support.my.store
     * Cria o chamado e (opcionalmente) registra a 1ª mensagem pública com a descrição.
     */
public function store(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        'subject'       => ['required', 'string', 'max:180'],
        'description'   => ['required', 'string'],
        'priority'      => ['nullable', \Illuminate\Validation\Rule::in(['low','normal','high','urgent'])],
        'sector_id'     => ['nullable', 'integer', 'exists:sectors,id'],
        'attachments.*' => ['file', 'max:10240'], // 10MB por arquivo
    ], [
        'attachments.*.max' => 'Cada arquivo deve ter no máximo 10MB.',
    ]);

    // Código único (ex.: SUP-XXXXXX)
    do {
        $code = 'SUP-'.strtoupper(\Illuminate\Support\Str::random(6));
    } while (Ticket::where('code', $code)->exists());

    $ticket = new Ticket();
    $ticket->code        = $code;
    $ticket->user_id     = $user->id;
    $ticket->subject     = $data['subject'];
    $ticket->description = $data['description'];   // <-- ESSA LINHA RESOLVE O NOT NULL
    $ticket->priority    = $data['priority'] ?? 'normal';
    $ticket->status      = 'open';
    $ticket->sector_id   = $data['sector_id'] ?? null;

    // Upload opcional
    $savedFiles = [];
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            if (!$file) continue;
            $path = $file->store("tickets/{$code}", 'public');
            if ($path) $savedFiles[] = $path;
        }
    }
    if (method_exists($ticket, 'getFillable') && in_array('attachments', $ticket->getFillable(), true)) {
        $ticket->attachments = $savedFiles;
    }

    $ticket->save();

    // Primeira mensagem pública com a descrição
    TicketMessage::create([
        'ticket_id'   => $ticket->id,
        'user_id'     => $user->id,
        'message'     => $data['description'],
        'is_internal' => false,
    ]);

    return redirect()
        ->route('support.show', $ticket->code)
        ->with('status', 'Chamado criado com sucesso!');
}


    /**
     * GET /suporte/{ticket:code} -> support.show
     * Detalhes do chamado (somente do dono).
     */
    public function show(Request $request, Ticket $ticket)
    {
        // Garante que o aluno só vê o que é dele
        abort_if($ticket->user_id !== $request->user()->id, 403);

        // Carrega dados necessários + mensagens públicas
        $ticket->load([
            'user:id,name',
            'assignee:id,name',
            'sector:id,name',
            'publicMessages.author:id,name',
        ]);

        $messages = $ticket->publicMessages;

        return view('support.my.show', compact('ticket', 'messages'));
    }

    /**
     * POST /suporte/{ticket:code}/reply -> support.my.reply
     * Responde ao chamado (mensagem pública).
     */
    public function reply(Request $request, Ticket $ticket)
    {
        abort_if($ticket->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:2'],
            'attachments.*' => ['file', 'max:10240'], // opcional
        ]);

        // Cria mensagem pública
        TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $request->user()->id,
            'message'     => $data['body'],
            'is_internal' => false,
        ]);

        // (Opcional) tratar anexos da resposta aqui caso tenha colunas/relacionamentos para isso.

        // Ao responder, pode mudar status se estava aguardando/fechado
        if (in_array($ticket->status, ['waiting','resolved','closed'])) {
            $ticket->update(['status' => 'reopened']);
        }

        return back()->with('status', 'Mensagem enviada.');
    }

    /**
     * POST /suporte/{ticket:code}/close -> support.my.close
     * Fecha o próprio chamado.
     */
    public function close(Request $request, Ticket $ticket)
    {
        abort_if($ticket->user_id !== $request->user()->id, 403);

        $ticket->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()
            ->route('support.my.index')
            ->with('status', 'Chamado encerrado.');
    }

    // ================= Helpers =================

    private function generateUniqueCode(): string
    {
        do {
            $code = 'SUP-'.strtoupper(Str::random(6));
        } while (Ticket::where('code', $code)->exists());

        return $code;
    }

    private function modelHasFillable($model, string $key): bool
    {
        return method_exists($model, 'getFillable')
            && in_array($key, $model->getFillable(), true);
    }
}
