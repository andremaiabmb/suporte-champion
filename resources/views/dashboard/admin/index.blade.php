@extends('layouts.app')

@section('title', 'Admin • Dashboard')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8 md:p-10">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Painel Administrativo</h1>
      <p class="mt-3 text-slate-600 dark:text-slate-300 leading-relaxed">
        Visão geral do sistema, métricas e atalhos de gestão.
      </p>
    </div>

    {{-- AÇÕES (atalhos principais) --}}
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.supports.index') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        {{-- lifebuoy --}}
        <svg class="w-4 h-4 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="9"></circle>
          <circle cx="12" cy="12" r="4"></circle>
          <path d="M15.5 8.5l3-3M8.5 8.5l-3-3M8.5 15.5l-3 3M15.5 15.5l3 3"></path>
        </svg>
        <span>Support</span>
      </a>
      <a href="{{ route('admin.sectors.index') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        {{-- grid --}}
        <svg class="w-4 h-4 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7" rx="1"></rect>
          <rect x="14" y="3" width="7" height="7" rx="1"></rect>
          <rect x="3" y="14" width="7" height="7" rx="1"></rect>
          <rect x="14" y="14" width="7" height="7" rx="1"></rect>
        </svg>
        <span>Setores</span>
      </a>
      <a href="{{ route('admin.sectors.create') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white transition">
        {{-- plus --}}
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6z"/></svg>
        <span>Novo Setor</span>
      </a>
    </div>
  </div>
</div>
@endsection

@section('content')
@php
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Arr;

  $safeCount = function (string $table): int {
    try { return Schema::hasTable($table) ? (int) DB::table($table)->count() : 0; }
    catch (\Throwable $e) { return 0; }
  };

  $metrics = [
    'users_count'   => $safeCount('users'),
    'posts_count'   => $safeCount('posts'),
    'faqs_count'    => $safeCount('faqs'),
    'tickets_count' => $safeCount('tickets') ?: $safeCount('support_tickets') ?: $safeCount('chamados'),
  ];

  // Série do gráfico (estável por dia)
  mt_srand(crc32('admin'.date('Y-m-d')));
  $values = array_map(fn() => max(0, 12 + mt_rand(-9, 9)), range(1,7));
@endphp

{{-- AÇÕES RÁPIDAS --}}
<div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
  <a href="{{ route('admin.posts.create') }}" class="group rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5 hover:shadow transition" aria-label="Criar novo Post">
    <div class="flex items-center gap-3">
      <span class="inline-flex p-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/40">
        {{-- square-pen --}}
        <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="14" height="14" rx="2"></rect>
          <path d="M13 7l4 4M7 17l3-.5 6.5-6.5-2.5-2.5L7.5 14z"></path>
        </svg>
      </span>
      <div>
        <div class="font-medium text-slate-700 dark:text-slate-100">Novo Post</div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Publicar comunicado</div>
      </div>
    </div>
  </a>

  <a href="{{ route('admin.faqs.create') }}" class="group rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5 hover:shadow transition" aria-label="Criar nova FAQ">
    <div class="flex items-center gap-3">
      <span class="inline-flex p-2.5 rounded-xl bg-violet-100 dark:bg-violet-900/40">
        {{-- circle-help --}}
        <svg class="w-5 h-5 text-violet-700 dark:text-violet-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="9"></circle>
          <path d="M9.5 9a3 3 0 115 2.2c-.7.5-1 1-1 1.8v.5"></path>
          <circle cx="12" cy="17" r="1"></circle>
        </svg>
      </span>
      <div>
        <div class="font-medium text-slate-700 dark:text-slate-100">Nova FAQ</div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Adicionar resposta</div>
      </div>
    </div>
  </a>

  <a href="{{ route('admin.events.create') }}" class="group rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5 hover:shadow transition" aria-label="Criar novo Evento">
    <div class="flex items-center gap-3">
      <span class="inline-flex p-2.5 rounded-xl bg-sky-100 dark:bg-sky-900/40">
        {{-- calendar-plus --}}
        <svg class="w-5 h-5 text-sky-700 dark:text-sky-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="5" width="18" height="16" rx="2"></rect>
          <path d="M16 3v4M8 3v4M3 9h18M12 12v6M9 15h6"></path>
        </svg>
      </span>
      <div>
        <div class="font-medium text-slate-700 dark:text-slate-100">Novo Evento</div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Incluir no calendário</div>
      </div>
    </div>
  </a>

  <a href="{{ route('admin.supports.index') }}" class="group rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5 hover:shadow transition" aria-label="Ir para Chamados">
    <div class="flex items-center gap-3">
      <span class="inline-flex p-2.5 rounded-xl bg-amber-100 dark:bg-amber-900/40">
        {{-- lifebuoy --}}
        <svg class="w-5 h-5 text-amber-700 dark:text-amber-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="9"></circle>
          <circle cx="12" cy="12" r="4"></circle>
          <path d="M15.5 8.5l3-3M8.5 8.5l-3-3M8.5 15.5l-3 3M15.5 15.5l3 3"></path>
        </svg>
      </span>
      <div>
        <div class="font-medium text-slate-700 dark:text-slate-100">Chamados</div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Atender solicitações</div>
      </div>
    </div>
  </a>
</div>

{{-- CARDS (botão "Gerir" temático e evidente, ícones inline) --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">
  {{-- Usuários --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="flex items-start justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="inline-flex p-2.5 rounded-xl bg-sky-100 dark:bg-sky-900/40">
          {{-- users --}}
          <svg class="w-5 h-5 text-sky-700 dark:text-sky-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="8" r="3"></circle>
            <path d="M4 20a5 5 0 0110 0"></path>
            <circle cx="17" cy="10" r="2"></circle>
            <path d="M14 20a5 5 0 017 0"></path>
          </svg>
        </span>
        <h3 class="font-medium text-slate-700 dark:text-slate-100">Usuários</h3>
      </div>
      <a href="{{ route('dashboard.admin.index') }}"
         class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1.5 rounded-full bg-sky-600/10 text-sky-700 dark:text-sky-200 border border-sky-200/60 dark:border-sky-500/30 hover:bg-sky-600/15 transition focus:outline-none focus:ring-2 focus:ring-sky-500"
         aria-label="Gerir usuários">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06A1.65 1.65 0 0015 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 008.6 15a1.65 1.65 0 00-1.82-.33l-.06.06A2 2 0 013.9 11.9l.06-.06A1.65 1.65 0 004.4 10a1.65 1.65 0 00-.33-1.82l-.06-.06A2 2 0 016.84 5.3l.06.06A1.65 1.65 0 008.6 5a1.65 1.65 0 001.82-.33l.06-.06A2 2 0 0113.3 7.84l-.06.06A1.65 1.65 0 0015 8.6a1.65 1.65 0 001.82-.33l.06-.06A2 2 0 0119.7 11.3l-.06.06A1.65 1.65 0 0019.4 15z"/></svg>
        <span>Gerir</span>
      </a>
    </div>
    <div class="mt-4">
      <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $metrics['users_count'] }}</div>
      <div class="text-sm text-slate-500 dark:text-slate-400">Usuários ativos</div>
    </div>
  </div>

  {{-- Posts --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="flex items-start justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="inline-flex p-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/40">
          {{-- newspaper --}}
          <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="16" rx="2"></rect>
            <path d="M7 8h6M7 12h10M7 16h10"></path>
          </svg>
        </span>
        <h3 class="font-medium text-slate-700 dark:text-slate-100">Posts</h3>
      </div>
      <a href="{{ route('admin.posts.index') }}"
         class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1.5 rounded-full bg-emerald-600/10 text-emerald-700 dark:text-emerald-200 border border-emerald-200/60 dark:border-emerald-500/30 hover:bg-emerald-600/15 transition focus:outline-none focus:ring-2 focus:ring-emerald-500"
         aria-label="Gerir posts">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"></path></svg>
        <span>Gerir</span>
      </a>
    </div>
    <div class="mt-4">
      <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $metrics['posts_count'] }}</div>
      <div class="text-sm text-slate-500 dark:text-slate-400">Publicações totais</div>
    </div>
  </div>

  {{-- FAQs --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="flex items-start justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="inline-flex p-2.5 rounded-xl bg-violet-100 dark:bg-violet-900/40">
          {{-- help-circle --}}
          <svg class="w-5 h-5 text-violet-700 dark:text-violet-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M9.5 9a3 3 0 115 2.2c-.7.5-1 1-1 1.8v.5"></path>
            <circle cx="12" cy="17" r="1"></circle>
          </svg>
        </span>
        <h3 class="font-medium text-slate-700 dark:text-slate-100">FAQs</h3>
      </div>
      <a href="{{ route('admin.faqs.index') }}"
         class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1.5 rounded-full bg-violet-600/10 text-violet-700 dark:text-violet-200 border border-violet-200/60 dark:border-violet-500/30 hover:bg-violet-600/15 transition focus:outline-none focus:ring-2 focus:ring-violet-500"
         aria-label="Gerir FAQs">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19h.01M8 9a4 4 0 118 0c0 2-2 2-2 4"></path></svg>
        <span>Gerir</span>
      </a>
    </div>
    <div class="mt-4">
      <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $metrics['faqs_count'] }}</div>
      <div class="text-sm text-slate-500 dark:text-slate-400">Perguntas publicadas</div>
    </div>
  </div>

  {{-- Chamados --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="flex items-start justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="inline-flex p-2.5 rounded-xl bg-amber-100 dark:bg-amber-900/40">
          {{-- lifebuoy --}}
          <svg class="w-5 h-5 text-amber-700 dark:text-amber-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="9"></circle>
            <circle cx="12" cy="12" r="4"></circle>
            <path d="M15.5 8.5l3-3M8.5 8.5l-3-3M8.5 15.5l-3 3M15.5 15.5l3 3"></path>
          </svg>
        </span>
        <h3 class="font-medium text-slate-700 dark:text-slate-100">Chamados</h3>
      </div>
      <a href="{{ route('admin.supports.index') }}"
         class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-200 border border-amber-300/60 dark:border-amber-500/30 hover:bg-amber-500/15 transition focus:outline-none focus:ring-2 focus:ring-amber-500"
         aria-label="Gerir chamados">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M4 7l8 7 8-7"/></svg>
        <span>Gerir</span>
      </a>
    </div>
    <div class="mt-4">
      <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $metrics['tickets_count'] }}</div>
      <div class="text-sm text-slate-500 dark:text-slate-400">Abertos/Total</div>
    </div>
  </div>
</div>

{{-- GRÁFICO (vanilla) --}}
<div class="mt-6 rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-6">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2">
      <span class="inline-flex p-2 rounded-xl bg-brand-100 dark:bg-brand-900/40">
        {{-- chart-line --}}
        <svg class="w-5 h-5 text-brand-700 dark:text-brand-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 3v18h18"></path><path d="M19 9l-5 5-3-3-4 4"></path>
        </svg>
      </span>
      <h3 class="font-medium text-slate-700 dark:text-slate-100">Atividade semanal</h3>
    </div>
    <a href="{{ route('dashboard.admin.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 inline-flex items-center gap-1">
      {{-- refresh-ccw --}}
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M23 20v-6h-6"/><path d="M3.51 15A9 9 0 1021 8.5L23 10"/></svg>
      <span>Atualizar</span>
    </a>
  </div>

  <div class="relative h-48">
    <canvas id="miniChartAdmin" class="w-full h-full"></canvas>
  </div>

  @push('scripts')
  <script>
    (function () {
      const el = document.getElementById('miniChartAdmin'); if (!el) return;
      const values = @json($values);
      const dpr = window.devicePixelRatio || 1, w = el.clientWidth, h = el.clientHeight;
      el.width = w * dpr; el.height = h * dpr;
      const ctx = el.getContext('2d'); ctx.scale(dpr, dpr);

      const axis = getComputedStyle(document.body).color;

      // grid
      ctx.strokeStyle = axis + '33'; ctx.lineWidth = 1; ctx.beginPath();
      [0.25, 0.5, 0.75].forEach(r => { const y = h * r; ctx.moveTo(0, y); ctx.lineTo(w, y); });
      ctx.stroke();

      // linha
      const pad = 24, cw = w - pad*2, ch = h - pad*2;
      const max = Math.max(...values, 1), min = Math.min(...values, 0), span = Math.max(max - min, 1);
      const pts = values.map((v, i) => ({ x: pad + (i/(values.length-1))*cw, y: pad + (1 - (v - min)/span)*ch }));

      ctx.strokeStyle = axis; ctx.lineWidth = 2; ctx.beginPath();
      pts.forEach((p, i) => i ? ctx.lineTo(p.x, p.y) : ctx.moveTo(p.x, p.y));
      ctx.stroke();

      // pontos
      ctx.fillStyle = axis;
      pts.forEach(p => { ctx.beginPath(); ctx.arc(p.x, p.y, 3, 0, Math.PI*2); ctx.fill(); });
    })();
  </script>
  @endpush
</div>

@php
  // Atividade recente (segura) — busca últimos itens se as tabelas existirem
  $fetchLatest = function (string $table, string $type, string $titleCol = 'title') {
    try {
      if (!Schema::hasTable($table)) return [];
      return DB::table($table)
        ->select('id', DB::raw("'".$type."' as type"), $titleCol.' as title', 'created_at')
        ->orderByDesc('created_at')
        ->limit(6)
        ->get()
        ->map(function ($r) use ($type) {
          $route = match ($type) {
            'post'  => route('admin.posts.show', $r->id),
            'faq'   => route('admin.faqs.show', $r->id),
            'event' => route('admin.events.show', $r->id),
            default => '#',
          };
          return (object) [
            'type'  => $type,
            'id'    => $r->id,
            'title' => $r->title ?? ('#'.$r->id),
            'at'    => $r->created_at,
            'url'   => $route,
          ];
        })->all();
    } catch (\Throwable $e) {
      return [];
    }
  };

  $recent = array_slice(
    Arr::sortDesc(
      array_merge(
        $fetchLatest('posts', 'post', 'title'),
        $fetchLatest('faqs', 'faq', 'question'),
        $fetchLatest('events', 'event', 'title')
      ),
      fn($x) => strtotime($x->at ?? '1970-01-01')
    ),
    0, 8
  );
@endphp

{{-- ATIVIDADE RECENTE --}}
<div class="mt-6 rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10">
  <div class="px-6 py-4 border-b border-slate-100 dark:border-white/10 flex items-center gap-2">
    <span class="inline-flex p-2.5 rounded-xl bg-sky-100 dark:bg-sky-900/40">
      {{-- activity --}}
      <svg class="w-5 h-5 text-sky-700 dark:text-sky-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 12h4l3 8 4-16 3 8h4"></path>
      </svg>
    </span>
    <h3 class="font-medium text-slate-700 dark:text-slate-100">Atividade recente</h3>
  </div>

  <div class="divide-y divide-slate-100 dark:divide-white/10">
    @forelse ($recent as $row)
      <a href="{{ $row->url }}" class="flex items-center justify-between px-6 py-3 hover:bg-slate-50 dark:hover:bg-white/5 transition">
        <div class="flex items-center gap-3">
          @switch($row->type)
            @case('post')
              <span class="inline-flex p-2 rounded-xl bg-emerald-100 dark:bg-emerald-900/40">
                <svg class="w-4 h-4 text-emerald-700 dark:text-emerald-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M7 8h6M7 12h10M7 16h10"></path>
                </svg>
              </span>
            @break
            @case('faq')
              <span class="inline-flex p-2 rounded-xl bg-violet-100 dark:bg-violet-900/40">
                <svg class="w-4 h-4 text-violet-700 dark:text-violet-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="9"></circle><path d="M9.5 9a3 3 0 115 2.2c-.7.5-1 1-1 1.8v.5"></path><circle cx="12" cy="17" r="1"></circle>
                </svg>
              </span>
            @break
            @case('event')
              <span class="inline-flex p-2 rounded-xl bg-sky-100 dark:bg-sky-900/40">
                <svg class="w-4 h-4 text-sky-700 dark:text-sky-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 9h18"></path>
                </svg>
              </span>
            @break
            @default
              <span class="inline-flex p-2 rounded-xl bg-slate-100 dark:bg-slate-800">
                <svg class="w-4 h-4 text-slate-600 dark:text-slate-300" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"/></svg>
              </span>
          @endswitch
          <div>
            <div class="text-sm font-medium text-slate-800 dark:text-slate-100 line-clamp-1">{{ $row->title }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($row->at)->diffForHumans() }}</div>
          </div>
        </div>
        {{-- arrow-up-right --}}
        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M7 7h10v10"/></svg>
      </a>
    @empty
      <div class="px-6 py-6 text-sm text-slate-500 dark:text-slate-400">Sem registros por enquanto.</div>
    @endforelse
  </div>
</div>
@endsection
