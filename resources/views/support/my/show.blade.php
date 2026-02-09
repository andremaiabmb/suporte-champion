{{-- resources/views/support/my/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Suporte • ' . ($ticket->code ?? 'Chamado'))

@section('hero')
  <div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
    <div class="flex items-center justify-between gap-4">
      <div>
        <div class="text-xs text-slate-500">Chamado</div>
        <h1 class="text-2xl font-semibold tracking-tight">
          {{ $ticket->code ?? '—' }} · {{ $ticket->subject ?? 'Sem assunto' }}
        </h1>
        <p class="mt-1 text-slate-600 dark:text-slate-300">
          Aberto por <span class="font-medium">{{ optional($ticket->author)->name ?? 'Você' }}</span>
          em {{ optional($ticket->created_at)->format('d/m/Y H:i') ?? '—' }}
        </p>
      </div>

      @php
        $status = $ticket->status ?? 'open';
        $priority = $ticket->priority ?? 'normal';
        $statusMap = [
          'open' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-200', 'label' => 'Aberto'],
          'in_progress' => ['bg' => 'bg-sky-100 dark:bg-sky-900/30', 'text' => 'text-sky-700 dark:text-sky-200', 'label' => 'Em andamento'],
          'waiting' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-200', 'label' => 'Aguardando'],
          'resolved' => ['bg' => 'bg-teal-100 dark:bg-teal-900/30', 'text' => 'text-teal-700 dark:text-teal-200', 'label' => 'Resolvido'],
          'closed' => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-200', 'label' => 'Fechado'],
          'reopened' => ['bg' => 'bg-violet-100 dark:bg-violet-900/30', 'text' => 'text-violet-700 dark:text-violet-200', 'label' => 'Reaberto'],
        ];
        $priorityMap = [
          'low' => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-200', 'label' => 'Baixa'],
          'normal' => ['bg' => 'bg-sky-100 dark:bg-sky-900/30', 'text' => 'text-sky-700 dark:text-sky-200', 'label' => 'Normal'],
          'high' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-700 dark:text-orange-200', 'label' => 'Alta'],
          'urgent' => ['bg' => 'bg-rose-100 dark:bg-rose-900/30', 'text' => 'text-rose-700 dark:text-rose-200', 'label' => 'Urgente'],
        ];
        $st = $statusMap[$status] ?? $statusMap['open'];
        $pr = $priorityMap[$priority] ?? $priorityMap['normal'];
      @endphp

      <div class="flex items-center gap-2 shrink-0">
        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $st['bg'] }} {{ $st['text'] }}">
          {{ $st['label'] }}
        </span>
        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $pr['bg'] }} {{ $pr['text'] }}">
          Prioridade: {{ $pr['label'] }}
        </span>
      </div>
    </div>
  </div>
@endsection

@section('content')
@php
  /** @var \Illuminate\Support\Collection $messages */
  $messages = $messages ?? collect();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  {{-- Lateral: detalhes do ticket --}}
  <aside class="lg:col-span-4">
    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-5">
      <h3 class="font-semibold">Detalhes</h3>
      <dl class="mt-4 space-y-3 text-sm">
        <div class="flex items-start justify-between gap-3">
          <dt class="text-slate-500">Código</dt>
          <dd class="font-mono">{{ $ticket->code ?? '—' }}</dd>
        </div>
        <div class="flex items-start justify-between gap-3">
          <dt class="text-slate-500">Assunto</dt>
          <dd class="text-right">{{ $ticket->subject ?? '—' }}</dd>
        </div>
        <div class="flex items-start justify-between gap-3">
          <dt class="text-slate-500">Setor</dt>
          <dd class="text-right">{{ optional($ticket->sector)->name ?? '—' }}</dd>
        </div>
        <div class="flex items-start justify-between gap-3">
          <dt class="text-slate-500">Responsável</dt>
          <dd class="text-right">{{ optional($ticket->assignee)->name ?? 'Não atribuído' }}</dd>
        </div>
        <div class="flex items-start justify-between gap-3">
          <dt class="text-slate-500">Aberto em</dt>
          <dd class="text-right">{{ optional($ticket->created_at)->format('d/m/Y H:i') ?? '—' }}</dd>
        </div>
        @if($ticket->resolved_at)
          <div class="flex items-start justify-between gap-3">
            <dt class="text-slate-500">Resolvido em</dt>
            <dd class="text-right">{{ optional($ticket->resolved_at)->format('d/m/Y H:i') }}</dd>
          </div>
        @endif
        @if($ticket->closed_at)
          <div class="flex items-start justify-between gap-3">
            <dt class="text-slate-500">Fechado em</dt>
            <dd class="text-right">{{ optional($ticket->closed_at)->format('d/m/Y H:i') }}</dd>
          </div>
        @endif
      </dl>

      <div class="mt-5">
        <a href="{{ route('support.my.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
          Voltar à lista
        </a>

        @if(($ticket->status ?? '') !== 'closed')
          <form action="{{ route('support.my.close', $ticket) }}" method="POST" class="mt-3"
                onsubmit="return confirm('Deseja encerrar este chamado?');">
            @csrf
            <button type="submit"
              class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-3 py-2 text-sm hover:opacity-90">
              Encerrar chamado
            </button>
          </form>
        @endif
      </div>
    </div>
  </aside>

  {{-- Corpo: descrição + mensagens + resposta --}}
  <section class="lg:col-span-8 space-y-6">

    {{-- Descrição inicial --}}
    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
      <div class="flex items-start gap-3">
        <div class="shrink-0 w-9 h-9 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center">
          <svg class="w-4.5 h-4.5 text-slate-600 dark:text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 12c2.8 0 5-2.2 5-5s-2.2-5-5-5-5 2.2-5 5 2.2 5 5 5z"/><path d="M21 22c0-4.4-4-8-9-8s-9 3.6-9 8"/>
          </svg>
        </div>
        <div class="flex-1">
          <div class="flex items-center justify-between">
            <div class="font-medium">{{ optional($ticket->author)->name ?? 'Você' }}</div>
            <div class="text-xs text-slate-500">{{ optional($ticket->created_at)->format('d/m/Y H:i') ?? '—' }}</div>
          </div>
          <div class="prose prose-slate dark:prose-invert max-w-none mt-2">
            {!! nl2br(e($ticket->description ?? '—')) !!}
          </div>
        </div>
      </div>
    </div>

    {{-- Mensagens (timeline) --}}
    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900">
      <div class="px-6 py-4 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
        <h3 class="font-semibold">Mensagens</h3>
        <span class="text-xs text-slate-500">
          {{ $messages->count() }}
          {{ $messages->count() === 1 ? 'mensagem' : 'mensagens' }}
        </span>
      </div>

      <div class="divide-y divide-slate-100 dark:divide-white/10">
        @forelse ($messages as $msg)
          <div class="p-6">
            <div class="flex items-start gap-3">
              <div class="shrink-0 w-8 h-8 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-600 dark:text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 12c2.8 0 5-2.2 5-5s-2.2-5-5-5-5 2.2-5 5 2.2 5 5 5z"/><path d="M21 22c0-4.4-4-8-9-8s-9 3.6-9 8"/>
                </svg>
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between">
                  <div class="font-medium">
                    {{ optional($msg->user)->name ?? 'Equipe de Suporte' }}
                    @if($msg->user_id === optional($ticket->author)->id) <span class="ml-1 text-xs text-slate-500">(autor)</span>@endif
                  </div>
                  <div class="text-xs text-slate-500">{{ optional($msg->created_at)->format('d/m/Y H:i') ?? '—' }}</div>
                </div>
                <div class="prose prose-slate dark:prose-invert max-w-none mt-2">
                  {!! nl2br(e($msg->message ?? $msg->body ?? '')) !!}
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="p-6 text-sm text-slate-500">Ainda não há mensagens neste chamado.</div>
        @endforelse
      </div>
    </div>

    {{-- Responder (se não fechado) --}}
    @if(($ticket->status ?? '') !== 'closed')
      <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
        <h3 class="font-semibold">Responder</h3>
        <form action="{{ route('support.my.reply', $ticket) }}" method="POST" class="mt-3 space-y-3">
          @csrf
          <div>
            <textarea name="message" rows="5" required
              class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950/50 px-3 py-2 outline-none focus:ring-2 focus:ring-brand-500"
              placeholder="Descreva sua dúvida, avanço ou anexe informações adicionais (copie/cole links e detalhes)."></textarea>
            @error('message')
              <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
            @enderror
          </div>
          <div class="flex items-center gap-2">
            <button type="submit"
              class="inline-flex items-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 transition">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
              Enviar resposta
            </button>

            <a href="{{ route('support.my.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800">
              Cancelar
            </a>
          </div>
        </form>
      </div>
    @else
      <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
        <div class="text-sm text-slate-600 dark:text-slate-300">
          Este chamado está <strong>fechado</strong>. Se precisar reabrir, crie um novo chamado referenciando o código {{ $ticket->code ?? '' }}.
        </div>
      </div>
    @endif

  </section>
</div>
@endsection
