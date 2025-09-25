@extends('layouts.app')

@section('title', 'Dúvidas Frequentes · Suporte ao Aluno')

@section('content')
<div class="mt-2">
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold">Dúvidas Frequentes</h1>
      <p class="mt-1 text-slate-600 dark:text-slate-300 text-sm">Encontre respostas rápidas.</p>
    </div>
    <form method="GET" action="{{ route('faqs.index') }}" class="w-full max-w-sm">
      <div class="relative">
        <input type="search" name="q" value="{{ $search ?? request('q') }}"
               class="w-full rounded-xl border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-900 px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
               placeholder="Pesquisar..." />
        <button class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-300" aria-label="Pesquisar">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/>
          </svg>
        </button>
      </div>
    </form>
  </div>

  <div class="mt-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm divide-y divide-slate-200/70 dark:divide-white/5">
    @forelse($faqs as $faq)
      <details class="group p-5">
        <summary class="flex cursor-pointer list-none items-start justify-between gap-4">
          <h3 class="text-base font-semibold">
            {{ $faq->question ?? 'Pergunta' }}
          </h3>
          <span class="mt-0.5 text-slate-400 group-open:rotate-180 transition">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
            </svg>
          </span>
        </summary>
        <div class="mt-3 prose prose-sm max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">
          {!! nl2br(e($faq->answer ?? '')) !!}
        </div>
        <div class="mt-4 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-3">
          <a href="{{ route('faqs.show', $faq) }}" class="inline-flex items-center gap-1 text-brand-600 hover:text-brand-700">
            Ver página
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </a>
          @if(!empty($faq->updated_at))
            <span>Atualizado em {{ \Illuminate\Support\Carbon::parse($faq->updated_at)->format('d/m/Y') }}</span>
          @endif
        </div>
      </details>
    @empty
      <div class="p-6 text-slate-600 dark:text-slate-300">Nenhuma dúvida encontrada.</div>
    @endforelse
  </div>

  @if(method_exists($faqs, 'hasPages') && $faqs->hasPages())
    <div class="mt-6">{{ $faqs->onEachSide(1)->links() }}</div>
  @endif
</div>
@endsection
