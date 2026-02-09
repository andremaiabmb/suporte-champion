@extends('layouts.app')
@section('title', "Chamado {$ticket->code}")

@section('content')
<div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
  <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div>
      <h1 class="text-xl font-semibold">{{ $ticket->subject }}</h1>
      <p class="text-sm text-slate-500">
        Código: <span class="font-mono">{{ $ticket->code }}</span> ·
        Setor: {{ $ticket->sector->name ?? '—' }} ·
        Autor: {{ $ticket->author->name ?? '—' }} ·
        Responsável: {{ $ticket->assignee->name ?? '—' }}
      </p>
    </div>
    <form method="POST" action="{{ route('admin.supports.status', $ticket) }}" class="flex items-center gap-2">
      @csrf
      <select name="status" class="rounded-xl border-slate-200 dark:border-white/10">
        @foreach (['open','in_progress','waiting','resolved','closed','reopened'] as $st)
          <option value="{{ $st }}" @selected($ticket->status===$st)>{{ strtoupper($st) }}</option>
        @endforeach
      </select>
      <button class="rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10">Atualizar</button>
    </form>
  </div>

  <div class="mt-6 grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
      <h2 class="font-medium mb-3">Mensagens</h2>
      <div class="space-y-3">
        @forelse ($ticket->messages as $m)
          <div class="rounded-xl border border-slate-200 dark:border-white/10 p-3 @if($m->is_internal) bg-amber-50/50 dark:bg-amber-900/10 @endif">
            <div class="text-xs text-slate-500 mb-1">
              {{ $m->author->name ?? '—' }} • {{ $m->created_at?->format('d/m/Y H:i') }}
              @if($m->is_internal) <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] bg-amber-200/80 text-amber-900">INTERNAL</span> @endif
            </div>
            <div class="text-sm whitespace-pre-line">{{ $m->message }}</div>
          </div>
        @empty
          <div class="text-sm text-slate-500">Sem mensagens ainda.</div>
        @endforelse
      </div>

      <form method="POST" action="{{ route('admin.supports.note', $ticket) }}" class="mt-4 space-y-2">
        @csrf
        <textarea name="message" rows="3" class="w-full rounded-xl border-slate-200 dark:border-white/10" placeholder="Adicionar mensagem..."></textarea>
        <label class="text-sm inline-flex items-center gap-2">
          <input type="checkbox" name="is_internal" value="1"> Nota interna (oculta do aluno)
        </label>
        <div><button class="rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10">Enviar</button></div>
      </form>
    </div>

    <aside>
      <h2 class="font-medium mb-3">Ações</h2>
      <form method="POST" action="{{ route('admin.supports.assign', $ticket) }}" class="mb-2">
        @csrf
        <button class="w-full rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10">Assumir</button>
      </form>

      <form method="POST" action="{{ route('admin.supports.reassign', $ticket) }}" class="space-y-2">
        @csrf
        <input name="assignee_id" type="number" class="w-full rounded-xl border-slate-200 dark:border-white/10" placeholder="ID do responsável">
        <button class="w-full rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10">Reatribuir</button>
      </form>

      <h2 class="font-medium mt-6 mb-3">Histórico</h2>
      <div class="space-y-2 text-sm">
        @forelse($ticket->transitions as $t)
          <div class="rounded-xl border border-slate-200 dark:border-white/10 p-2">
            {{ $t->created_at?->format('d/m/Y H:i') }} —
            <strong>{{ $t->field }}</strong>:
            "{{ $t->old_value ?? '—' }}" → "{{ $t->new_value ?? '—' }}"
            @if($t->user) • por {{ $t->user->name }} @endif
          </div>
        @empty
          <div class="text-slate-500">Sem histórico.</div>
        @endforelse
      </div>
    </aside>
  </div>
</div>
@endsection
