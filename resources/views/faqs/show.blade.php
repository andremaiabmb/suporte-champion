{{-- resources/views/faqs/show.blade.php --}}
@extends('layouts.app')

@section('title', ($faq->question ?? 'FAQ').' · Suporte ao Aluno')

@section('hero')
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl mt-6 shadow-soft border border-slate-200/70 dark:border-white/10">
  <div class="px-6 py-12 sm:px-10">
    <div class="max-w-3xl">
      <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
        {{ $faq->question }}
      </h1>
      @if($faq->published_at)
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
          Publicado em {{ $faq->published_at->format('d/m/Y') }}
        </p>
      @endif
    </div>
  </div>
</section>
@endsection

@section('content')
@php use Illuminate\Support\Str; @endphp

<section class="mt-8">
  <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 shadow-sm p-6 md:p-10">

    {{-- Resposta principal em Markdown (opcional) --}}
    @if(filled($faq->answer))
      <div class="prose prose-slate max-w-none dark:prose-invert">
        {!! Str::markdown($faq->answer) !!}
      </div>
    @endif

    {{-- Passo a passo --}}
    @if($faq->steps->count())
      <hr class="my-8 border-slate-200 dark:border-white/10">
      <h2 class="text-xl font-semibold mb-4">Passo a passo</h2>

      <ol class="space-y-6">
        @foreach($faq->steps as $step)
          <li class="border-l-4 border-brand-600 pl-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
              <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-600 text-white text-sm font-bold">
                {{ $step->step_number }}
              </span>
              {{ $step->title }}
            </h3>

            @if(filled($step->body))
              <div class="mt-2 prose prose-slate max-w-none dark:prose-invert text-slate-700 dark:text-slate-200">
                {!! Str::markdown($step->body) !!}
              </div>
            @endif

            @if($step->image_path)
              <figure class="mt-4">
                <img
                  src="{{ asset('storage/'.$step->image_path) }}"
                  alt="{{ $step->image_caption }}"
                  class="rounded-xl border border-slate-200 dark:border-white/10 shadow-sm max-h-96 object-contain w-full">
                @if($step->image_caption)
                  <figcaption class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ $step->image_caption }}
                  </figcaption>
                @endif
              </figure>
            @endif
          </li>
        @endforeach
      </ol>
    @endif

    <div class="mt-10">
      <a href="{{ route('faqs.index') }}"
         class="inline-flex items-center text-sm font-medium text-brand-600 hover:text-brand-700">
        ← Voltar à lista de dúvidas
      </a>
    </div>
  </div>
</section>
@endsection
