@extends('layouts.app')

@section('title', 'Suporte · Meus Chamados')

@php
  // URLs conforme suas rotas atuais
  $createUrl = route('support.my.create');
  $indexUrl  = route('support.my.index');

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

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Suporte</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">Acompanhe seus chamados e o andamento.</p>
    </div>

    <a href="{{ $createUrl }}"
       class="inline-flex items-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 5v14M5 12h14"/>
      </svg>
      Abrir chamado
    </a>
  </div>
</div>
@endsection

@section('content')
<div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">

  {{-- Barra de ferramentas superior --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
    <div class="text-sm text-slate-600 dark:text-slate-300">
      @php
        $total = method_exists($tickets, 'total') ? $tickets->total() : (is_countable($tickets) ? count($tickets) : 0);
      @endphp
      <span class="font-medium">{{ $total }}</span> chamados encontrados
      @if(request('status'))
        <span class="mx-1">•</span>
        <span>Status: <span class="font-medium">{{ $statusLabel[request('status')] ?? request('status') }}</span></span>
      @endif
      @if(request('q'))
        <span class="mx-1">•</span>
        <span>Busca: <span class="font-medium">“{{ request('q') }}”</span></span>
      @endif
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ $createUrl }}"
         class="inline-flex items-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-3.5 py-2 text-sm transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14"/>
        </svg>
        Abrir chamado
      </a>

      @if(request()->hasAny(['q','status']))
        <a href="{{ $indexUrl }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition">
          Limpar filtros
        </a>
      @endif
    </div>
  </div>

  {{-- Filtros --}}
  <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
    <input name="q" value="{{ request('q') }}" placeholder="Buscar por código ou assunto…"
           class="md:col-span-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm"/>
    <select name="status" class="rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2.5 text-sm bg-white dark:bg-slate-900">
      <option value="">Todos status</option>
      @foreach($statusLabel as $k=>$v)
        <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
      @endforeach
    </select>
    <button class="rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
      Aplicar
    </button>
  </form>

  {{-- Lista --}}
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2 pr-3">Código</th>
          <th class="py-2 pr-3">Assunto</th>
          <th class="py-2 pr-3">Setor</th>
          <th class="py-2 pr-3">Prioridade</th>
          <th class="py-2 pr-3">Status</th>
          <th class="py-2 pr-3">Aberto em</th>
          <th class="py-2">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tickets as $t)
          @php
            $st     = $t->status ?? 'open';
            $prio   = $t->priority ?? 'normal';
            $sector = $t->sector->name ?? ($t->sector_name ?? '—');
          @endphp
          <tr class="border-t border-slate-100 dark:border-white/10 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
            <td class="py-2 pr-3 font-mono text-xs">{{ $t->code }}</td>
            <td class="py-2 pr-3">
              <div class="font-medium">{{ $t->subject }}</div>
              <div class="text-xs text-slate-500">
                {{ \Illuminate\Support\Str::limit($t->description ?? '', 90) }}
              </div>
            </td>
            <td class="py-2 pr-3">{{ $sector }}</td>
            <td class="py-2 pr-3">
              <span class="px-2 py-0.5 text-xs rounded-full {{ $prioStyle[$prio] ?? 'bg-slate-100' }}">
                {{ ucfirst($prio) }}
              </span>
            </td>
            <td class="py-2 pr-3">
              <span class="px-2 py-0.5 text-xs rounded-full {{ $statusStyle[$st] ?? 'bg-slate-100' }}">
                {{ $statusLabel[$st] ?? strtoupper($st) }}
              </span>
            </td>
            <td class="py-2 pr-3">
              {{ optional($t->created_at)->format('d/m/Y H:i') ?? '—' }}
            </td>
            <td class="py-2">
              {{-- Detalhe do ticket usa binding {ticket:code} pela rota support.show --}}
              <a href="{{ route('support.show', $t->code) }}"
                 class="text-brand-700 hover:underline">Ver</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="py-8 text-center">
              <div class="mx-auto w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-slate-500 dark:text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
              </div>
              <div class="font-medium">Você ainda não abriu chamados</div>
              <div class="text-sm text-slate-500 mt-1">Clique em “Abrir chamado” para registrar uma solicitação.</div>
              <a href="{{ $createUrl }}"
                 class="mt-3 inline-flex items-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-3.5 py-2 text-sm transition">
                Abrir chamado
              </a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $tickets->withQueryString()->links() }}</div>
</div>

{{-- Botão flutuante (FAB) para novo chamado --}}
<a href="{{ $createUrl }}"
   class="fixed bottom-6 right-6 md:bottom-8 md:right-8 inline-flex items-center justify-center rounded-full shadow-lg bg-brand-600 hover:bg-brand-700 text-white w-14 h-14 transition"
   aria-label="Abrir chamado (N)">
  <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M12 5v14M5 12h14"/>
  </svg>
</a>

{{-- Atalho de teclado: N para novo chamado --}}
<script>
  document.addEventListener('keydown', (e) => {
    const tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
    if (['input','textarea','select'].includes(tag) || e.ctrlKey || e.metaKey || e.altKey) return;
    if (e.key === 'n' || e.key === 'N') window.location.href = @json($createUrl);
  });
</script>
@endsection
