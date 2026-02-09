@extends('layouts.app')

@section('title','Suporte • Moderação')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Moderação de Chamados</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">Filtre, assuma, reatribua e mude status.</p>
    </div>
  </div>
</div>
@endsection

@section('content')
@php
  // Mapas para rótulos/cores de status e prioridade
  $statusLabel = [
    'open'        => 'Aberto',
    'in_progress' => 'Em andamento',
    'waiting'     => 'Aguardando',
    'resolved'    => 'Resolvido',
    'closed'      => 'Fechado',
    'reopened'    => 'Reaberto',
  ];
  $statusStyle = [
    'open'        => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
    'in_progress' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-200',
    'waiting'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
    'resolved'    => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-200',
    'closed'      => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
    'reopened'    => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
  ];
  $prioStyle = [
    'low'    => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
    'normal' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-200',
    'high'   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
    'urgent' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
  ];
@endphp

<div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
  {{-- Filtros --}}
  <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
    <input
      name="q"
      value="{{ request('q') }}"
      placeholder="Buscar por código/assunto/autor…"
      class="md:col-span-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm"
    />

    <select name="status" class="rounded-xl border-slate-200 dark:border-white/10" onchange="this.form.submit()">
      <option value="">Todos status</option>
      @foreach($statusLabel as $k=>$v)
        <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
      @endforeach
    </select>

    <select name="sector" class="rounded-xl border-slate-200 dark:border-white/10" onchange="this.form.submit()">
      <option value="">Todos setores</option>
      @foreach(($sectors ?? []) as $s)
        <option value="{{ $s->id }}" @selected(request('sector')==$s->id)>{{ $s->name }}</option>
      @endforeach
    </select>

    <select name="priority" class="rounded-xl border-slate-200 dark:border-white/10" onchange="this.form.submit()">
      <option value="">Todas prioridades</option>
      @foreach(['low'=>'Baixa','normal'=>'Normal','high'=>'Alta','urgent'=>'Urgente'] as $k=>$v)
        <option value="{{ $k }}" @selected(request('priority')===$k)>{{ $v }}</option>
      @endforeach
    </select>
  </form>

  {{-- Lista --}}
  <div class="mt-5 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2 pr-3">Código</th>
          <th class="py-2 pr-3">Assunto</th>
          <th class="py-2 pr-3">Setor</th>
          <th class="py-2 pr-3">Autor</th>
          <th class="py-2 pr-3">Responsável</th>
          <th class="py-2 pr-3">Prioridade</th>
          <th class="py-2 pr-3">Status</th>
          <th class="py-2">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tickets as $t)
          @php
            // Nomes com fallback seguro (controller pode usar relations ou joins)
            $reporter = $t->author->name ?? $t->user->name ?? ($t->reporter_name ?? '—');
            $assignee = $t->assignee->name ?? ($t->agent_name ?? '—');
            $sector   = $t->sector->name ?? ($t->sector_name ?? '—');

            $st   = $t->status ?? 'open';
            $prio = $t->priority ?? 'normal';
          @endphp
          <tr class="border-t border-slate-100 dark:border-white/10 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
            <td class="py-2 pr-3 font-mono text-xs">{{ $t->code }}</td>
            <td class="py-2 pr-3">
              <div class="font-medium">{{ $t->subject }}</div>
              <div class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($t->description ?? '', 80) }}</div>
            </td>
            <td class="py-2 pr-3">{{ $sector }}</td>
            <td class="py-2 pr-3">{{ $reporter }}</td>
            <td class="py-2 pr-3">{{ $assignee }}</td>
            <td class="py-2 pr-3">
              <span class="px-2 py-0.5 text-xs rounded-full {{ $prioStyle[$prio] ?? 'bg-slate-100' }}">
                {{ ucfirst(__($prio)) }}
              </span>
            </td>
            <td class="py-2 pr-3">
              <span class="px-2 py-0.5 text-xs rounded-full {{ $statusStyle[$st] ?? 'bg-slate-100' }}">
                {{ $statusLabel[$st] ?? strtoupper($st) }}
              </span>
            </td>
            <td class="py-2">
              <a
                href="{{ route('admin.supports.show', $t) }}@if(request()->getQueryString())?{{ request()->getQueryString() }}@endif"
                class="text-brand-700 hover:underline"
                title="Abrir chamado {{ $t->code }}"
              >Abrir</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="py-6 text-center text-slate-500">
              Nenhum chamado encontrado com os filtros atuais.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $tickets->withQueryString()->links() }}</div>
</div>
@endsection
