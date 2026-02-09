{{-- resources/views/faqs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dúvidas Frequentes · Suporte ao Aluno')

@section('hero')
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl mt-6 shadow-soft border border-slate-200/70 dark:border-white/10">
  <div class="px-6 py-12 sm:px-10">
    <div class="max-w-3xl">
      <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
        Dúvidas Frequentes
      </h1>
      <p class="mt-3 text-slate-600 dark:text-slate-300">
        Encontre respostas rápidas — pesquise por palavra-chave ou navegue pela lista de perguntas publicadas.
      </p>

      {{-- Busca inline no hero --}}
      <form method="GET" action="{{ route('faqs.index') }}" class="mt-6 max-w-xl">
        <div class="flex gap-3">
          <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            class="flex-1 rounded-xl bg-white px-3 py-2 text-slate-700 ring-1 ring-slate-900/10 shadow placeholder-slate-400
                   focus:outline-none focus:ring-2 focus:ring-brand-600 dark:bg-slate-900 dark:text-slate-200 dark:ring-white/10"
            placeholder="Ex.: matrícula, pagamento, visto…"
            autocomplete="off"
          />
          <button
            class="inline-flex items-center rounded-xl bg-brand-600 px-4 py-2 text-white font-medium shadow hover:bg-brand-700 transition">
            Buscar
          </button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection

@section('content')
@php use Illuminate\Support\Str; @endphp

<section class="mt-8">
  @php
    $total = method_exists($faqs, 'total') ? $faqs->total() : (is_countable($faqs) ? count($faqs) : 0);
  @endphp
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-semibold">Resultados</h2>

    <div class="text-sm text-slate-600 dark:text-slate-300">
      @if (request('q'))
        <span>“<span class="font-medium">{{ e(request('q')) }}</span>” —</span>
      @endif
      <span class="font-medium">{{ $total }}</span> registro(s)
      @if (request('q'))
        <a href="{{ route('faqs.index') }}" class="ml-2 text-brand-600 hover:text-brand-700">Limpar</a>
      @endif
    </div>
  </div>

  <div class="mt-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 shadow-sm divide-y divide-slate-200/70 dark:divide-white/5">
    @forelse($faqs as $faq)
      <details class="group p-5">
        <summary class="flex cursor-pointer list-none items-start justify-between gap-4">
          <div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
              {{ $faq->question }}
            </h3>
            @if($faq->steps_count > 0)
              <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                {{ $faq->steps_count }} passo(s)
              </p>
            @endif
          </div>
          <span class="mt-0.5 text-slate-400 group-open:rotate-180 transition" aria-hidden="true">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
            </svg>
          </span>
        </summary>

        {{-- Resumo/answer em Markdown (opcional) --}}
        @if(filled($faq->answer))
          <div class="mt-3 prose prose-slate max-w-none dark:prose-invert">
            {!! Str::markdown(Str::limit($faq->answer, 600)) !!}
          </div>
        @endif

        {{-- LISTA DE PASSOS (títulos) --}}
        @if($faq->steps->count())
          <div class="mt-4">
            <ol class="space-y-3">
              @foreach($faq->steps as $step)
                <li class="flex items-start gap-3">
                  <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white text-xs font-bold">
                    {{ $step->step_number }}
                  </span>
                  <a href="{{ route('faqs.show', $faq) }}"
                     class="text-slate-700 dark:text-slate-200 hover:text-brand-700 dark:hover:text-brand-400 font-medium">
                    {{ $step->title }}
                  </a>
                </li>
              @endforeach
            </ol>
          </div>
        @endif

        <div class="mt-5">
          <a href="{{ route('faqs.show', $faq) }}"
             class="inline-flex items-center text-sm font-medium text-brand-600 hover:text-brand-700">
            Abrir página da FAQ
            <svg class="ml-1 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
              <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 5h10M19 5v10M19 5l-7 7"/>
            </svg>
          </a>
        </div>
      </details>
    @empty
      <div class="p-6 text-slate-500 dark:text-slate-400">
        Nenhuma FAQ encontrada.
      </div>
    @endforelse
  </div>

  @if (method_exists($faqs, 'hasPages') && $faqs->hasPages())
    <div class="mt-6">
      {{ $faqs->withQueryString()->links() }}
    </div>
  @endif
</section>
@endsection
