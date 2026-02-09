@extends('layouts.app')

@section('title', 'Inscrição no Evento')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <h1 class="text-2xl md:text-3xl font-semibold">Inscrição no Evento</h1>
  <p class="mt-2 text-slate-600 dark:text-slate-300">
    Confira os detalhes do evento antes de confirmar sua participação.
  </p>
</div>
@endsection

@section('content')
<div class="space-y-6">

  @if (session('status'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 p-4 dark:border-emerald-900/30 dark:bg-emerald-900/10 dark:text-emerald-300">
      {{ session('status') }}
    </div>
  @endif

  <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <h2 class="text-xl font-semibold">{{ $event->title }}</h2>
    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
      @php
        // Normaliza possíveis nomes de colunas
        $start = $event->starts_at ?? $event->start_at ?? $event->start_date ?? $event->begin_at ?? $event->date ?? null;
        $end   = $event->ends_at   ?? $event->end_at   ?? $event->end_date   ?? $event->finish_at ?? null;
      @endphp

      @if (!empty($start))
        <span>Início: {{ \Illuminate\Support\Carbon::parse($start)->format('d/m/Y H:i') }}</span><br>
      @endif
      @if (!empty($end))
        <span>Fim: {{ \Illuminate\Support\Carbon::parse($end)->format('d/m/Y H:i') }}</span><br>
      @endif
      @if (!empty($event->location))
        <span>Local: {{ $event->location }}</span>
      @endif
    </p>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    @if (!empty($already_registered) && $already_registered)
      <div class="text-sm text-emerald-700 dark:text-emerald-300">
        Você já está inscrito neste evento.
      </div>
      <div class="mt-4">
        <a href="{{ route('home') }}"
           class="rounded-xl bg-slate-100 hover:bg-slate-200 px-5 py-2 font-medium dark:bg-slate-800 dark:hover:bg-slate-700">
          Voltar para a página inicial
        </a>
      </div>
    @else
      <p class="text-sm text-slate-600 dark:text-slate-300">
        Prezado(a) {{ auth()->user()->name ?? 'Aluno(a)' }}, confirme abaixo a sua inscrição para este evento.
      </p>

      <form method="POST" action="{{ route('events.register', $event) }}" class="mt-4">
        @csrf
        <div class="flex items-center gap-3">
          <button type="submit"
                  class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-5 py-2 font-medium">
            Confirmar Inscrição
          </button>
          <a href="{{ url()->previous() }}"
             class="rounded-xl bg-slate-100 hover:bg-slate-200 px-5 py-2 font-medium dark:bg-slate-800 dark:hover:bg-slate-700">
            Cancelar
          </a>
        </div>
      </form>
    @endif
  </div>
</div>
@endsection
