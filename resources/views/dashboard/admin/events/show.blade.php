@extends('layouts.app')

@section('title', 'Admin • Evento #'.$event->id)

@section('hero')
@php
  $starts = optional($event->starts_at)->format('d/m/Y H:i');
  $ends   = optional($event->ends_at)->format('d/m/Y H:i');
@endphp
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-6 md:p-8">
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">{{ $event->title }}</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">
        {{ $starts }} — {{ $ends }}
        @if($event->location) • {{ $event->location }} @endif
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.events.attendance', [$event->id, 'date'=>now()->toDateString()]) }}"
         class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 text-sm font-semibold">
        <i data-lucide="scan-line" class="w-4 h-4"></i>
        <span>Credenciamento</span>
      </a>
      <a href="{{ route('admin.events.edit', $event->id) }}"
         class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="settings-2" class="w-4 h-4"></i>
        <span>Editar</span>
      </a>
      <a href="{{ route('admin.events.index') }}"
         class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Voltar</span>
      </a>
    </div>
  </div>
</div>
@endsection

@section('content')
@php
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Facades\Schema;

  $cover = $event->cover_path;
  $coverUrl = $cover ? (preg_match('#^https?://|^//#', $cover) ? $cover : asset('storage/'.$cover)) : null;

  // ---- RESUMO DE INSCRIÇÕES ----
  $totalRegs = 0;
  if (Schema::hasTable('event_registrations')) {
      $totalRegs = (int) DB::table('event_registrations')->where('event_id', $event->id)->count();
  } elseif (Schema::hasTable('registrations')) {
      $totalRegs = (int) DB::table('registrations')->where('event_id', $event->id)->count();
  } elseif (Schema::hasTable('event_users')) {
      $totalRegs = (int) DB::table('event_users')->where('event_id', $event->id)->count();
  }

  // ---- RESUMO DE PRESENÇAS (POR DIA) ----
  $today = now()->toDateString();
  $hasAtt = Schema::hasTable('event_attendances');

  $presentToday = $hasAtt
      ? (int) DB::table('event_attendances')
          ->where('event_id', $event->id)
          ->where('date', $today)
          ->whereNotNull('checkin_at')
          ->count()
      : 0;

  $leftToday = $hasAtt
      ? (int) DB::table('event_attendances')
          ->where('event_id', $event->id)
          ->where('date', $today)
          ->whereNotNull('checkout_at')
          ->count()
      : 0;

  $presentNow = $hasAtt
      ? (int) DB::table('event_attendances')
          ->where('event_id', $event->id)
          ->where('date', $today)
          ->whereNotNull('checkin_at')
          ->whereNull('checkout_at')
          ->count()
      : 0;

  // HISTÓRICO DOS ÚLTIMOS 7 DIAS
  $history = [];
  if ($hasAtt) {
      $history = DB::table('event_attendances')
        ->select('date',
                 DB::raw('count(case when checkin_at is not null then 1 end) as checkins'),
                 DB::raw('count(case when checkout_at is not null then 1 end) as checkouts'))
        ->where('event_id', $event->id)
        ->groupBy('date')
        ->orderBy('date','desc')
        ->limit(7)->get();
  }

  // ---- LISTA DE INSCRIÇÕES + STATUS HOJE ----
  // detecta tabela de inscrições
  $regTable = null;
  foreach (['event_registrations','registrations','event_users'] as $t) {
      if (Schema::hasTable($t)) { $regTable = $t; break; }
  }

  $registrations = collect();
  if ($regTable) {
      $registrations = DB::table($regTable)
        ->where('event_id', $event->id)
        ->orderByDesc('id')
        ->paginate(10);
  }

  // helper p/ status de uma inscrição hoje
  $statusToday = function($userId, $email) use ($event, $today, $hasAtt) {
      if (!$hasAtt || !$userId) return null;
      $row = DB::table('event_attendances')
          ->where('event_id', $event->id)
          ->where('user_id', $userId)
          ->where('date', $today)
          ->first();
      if (!$row) return 'Sem registro';
      if ($row->checkin_at && !$row->checkout_at) return 'Presente (sem checkout)';
      if ($row->checkin_at && $row->checkout_at)  return 'Saiu (com checkout)';
      return 'Sem registro';
  };
@endphp

<div class="grid gap-6 lg:grid-cols-3">
  {{-- COLUNA ESQUERDA --}}
  <div class="lg:col-span-2 space-y-6">

    {{-- Capa --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:bg-slate-900 dark:border-white/10">
      <h3 class="text-sm font-semibold mb-3">Capa</h3>
      <div class="aspect-[16/9] rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800">
        @if ($coverUrl)
          <img src="{{ $coverUrl }}" alt="Capa do evento" class="w-full h-full object-cover">
        @else
          <div class="w-full h-full grid place-items-center text-slate-400 text-sm">Sem imagem</div>
        @endif
      </div>
    </div>

    {{-- Resumo rápido (inscrições + presenças de hoje) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-white/10 p-4">
        <div class="text-sm text-slate-600 dark:text-slate-300">Inscritos</div>
        <div class="mt-1 text-2xl font-semibold">{{ $totalRegs }}</div>
        <div class="mt-2 text-xs text-slate-500">Total de inscrições</div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-white/10 p-4">
        <div class="text-sm text-slate-600 dark:text-slate-300">Presentes hoje</div>
        <div class="mt-1 text-2xl font-semibold">{{ $presentToday }}</div>
        <div class="mt-2 text-xs text-slate-500">Com check-in em {{ $today }}</div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-white/10 p-4">
        <div class="text-sm text-slate-600 dark:text-slate-300">No local agora</div>
        <div class="mt-1 text-2xl font-semibold">{{ $presentNow }}</div>
        <div class="mt-2 text-xs text-slate-500">Check-in feito, sem checkout</div>
      </div>
    </div>

    {{-- Inscrições (com status de hoje) --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:bg-slate-900 dark:border-white/10">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold">Inscrições</h3>
        <span class="text-xs text-slate-500">Paginado</span>
      </div>

      <div class="mt-3 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-slate-500 dark:text-slate-400">
              <th class="py-2 pr-3">#</th>
              <th class="py-2 pr-3">Aluno</th>
              <th class="py-2 pr-3">E-mail</th>
              <th class="py-2 pr-3">Status (hoje)</th>
              <th class="py-2">Criado</th>
            </tr>
          </thead>
          <tbody class="align-top">
          @forelse($registrations as $r)
            @php
              // tenta inferir user_id e email conforme cada tabela
              $uid = $r->user_id ?? null;
              $mail = $r->email ?? null;
              $st = $statusToday($uid, $mail);
              $badgeClass = match ($st) {
                'Presente (sem checkout)' => 'text-emerald-700 bg-emerald-50 border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/40',
                'Saiu (com checkout)'     => 'text-amber-700  bg-amber-50  border-amber-200  dark:text-amber-200  dark:bg-amber-900/20  dark:border-amber-800/40',
                default                   => 'text-slate-700  bg-slate-50  border-slate-200  dark:text-slate-200  dark:bg-white/5      dark:border-white/10'
              };
            @endphp
            <tr class="border-t border-slate-100 dark:border-white/10">
              <td class="py-2 pr-3">#{{ $r->id }}</td>
              <td class="py-2 pr-3">{{ $r->name ?? $r->user_name ?? '—' }}</td>
              <td class="py-2 pr-3">{{ $mail ?? '—' }}</td>
              <td class="py-2 pr-3">
                <span class="inline-flex items-center px-2 py-1 rounded-lg border text-xs {{ $badgeClass }}">
                  {{ $st ?? 'Sem registro' }}
                </span>
              </td>
              <td class="py-2">{{ optional($r->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-8 text-center text-slate-500">Nenhuma inscrição.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        {{ $registrations->links() }}
      </div>
    </div>
  </div>

  {{-- COLUNA DIREITA --}}
  <div class="space-y-6">
    {{-- Ações rápidas --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:bg-slate-900 dark:border-white/10">
      <h3 class="text-sm font-semibold mb-3">Ações</h3>
      <a href="{{ route('admin.events.attendance', [$event->id, 'date'=>now()->toDateString()]) }}"
         class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-semibold">
        <i data-lucide="scan-line" class="w-4 h-4"></i>
        Abrir credenciamento
      </a>

      <form class="mt-2" method="POST" action="{{ route('admin.events.destroy', $event->id) }}"
            onsubmit="return confirm('Remover este evento?');">
        @csrf @method('DELETE')
        <button class="w-full rounded-xl px-4 py-2 text-sm font-semibold bg-rose-600 text-white hover:bg-rose-700">
          Remover evento
        </button>
      </form>
    </div>

    {{-- Histórico (últimos 7 dias) --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:bg-slate-900 dark:border-white/10">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold">Presenças (últimos dias)</h3>
        <span class="text-xs text-slate-500">Check-in / Check-out</span>
      </div>
      <div class="mt-3 space-y-2">
        @forelse($history as $h)
          <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
            <div class="font-medium">{{ \Carbon\Carbon::parse($h->date)->format('d/m/Y') }}</div>
            <div class="flex items-center gap-3">
              <span class="inline-flex items-center gap-1 text-emerald-700 dark:text-emerald-300">
                <i data-lucide="log-in" class="w-4 h-4"></i>{{ $h->checkins }}
              </span>
              <span class="inline-flex items-center gap-1 text-amber-700 dark:text-amber-300">
                <i data-lucide="log-out" class="w-4 h-4"></i>{{ $h->checkouts }}
              </span>
            </div>
          </div>
        @empty
          <div class="text-slate-500 text-sm">Sem presenças registradas.</div>
        @endforelse
      </div>
      <a href="{{ route('admin.events.attendance', [$event->id, 'date'=>now()->toDateString()]) }}"
         class="mt-3 inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">
        <i data-lucide="calendar" class="w-4 h-4"></i> Gerir presenças por dia
      </a>
    </div>
  </div>
</div>
@endsection
