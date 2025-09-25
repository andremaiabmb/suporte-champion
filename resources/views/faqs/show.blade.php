@extends('layouts.app')

@section('title', ($faq->question ?? 'FAQ') . ' · Suporte ao Aluno')

@section('content')
<a href="{{ route('faqs.index') }}" class="inline-flex items-center text-sm text-brand-600 hover:text-brand-700">
  <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
  Voltar às dúvidas
</a>

<article class="mt-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm p-6">
  <h1 class="text-2xl font-bold">
    {{ $faq->question ?? 'Pergunta' }}
  </h1>

  <div class="mt-4 prose max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">
    {!! nl2br(e($faq->answer ?? '')) !!}
  </div>

  @if(!empty($faq->published_at))
    <div class="mt-6 text-xs text-slate-500 dark:text-slate-400">
      Publicado em {{ \Illuminate\Support\Carbon::parse($faq->published_at)->format('d/m/Y H:i') }}
    </div>
  @endif
</article>
@endsection
