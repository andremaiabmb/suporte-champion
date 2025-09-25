@extends('layouts.app')

@section('title', 'Início · Suporte ao Aluno')

@section('hero')

<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl mt-6 shadow-soft">
  <div class="px-6 py-12 sm:px-10">
    <div class="max-w-3xl">
      <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
        Suporte ao Aluno
      </h1>
      <p class="mt-3 text-slate-600 dark:text-slate-300">
        Avisos, tutoriais e dúvidas frequentes — tudo em um só lugar. Colabore, aprenda e fique por dentro dos prazos.
      </p>
      <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('faqs.index') }}" class="inline-flex items-center rounded-xl bg-brand-600 px-4 py-2 text-white font-medium shadow hover:bg-brand-700 transition">
          Dúvidas Frequentes
        </a>
        <a href="#eventos" class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-slate-700 font-medium shadow ring-1 ring-slate-900/10 hover:shadow-md transition dark:bg-slate-900 dark:text-slate-200">
          Eventos Disponíveis
        </a>
      </div>
    </div>
  </div>
</section>
@endsection

@section('content')
{{-- Últimas notícias (placeholder inicialmente) --}}
<section class="mt-8">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-semibold">Últimas notícias</h2>
    <a href="#" class="text-sm text-brand-600 hover:text-brand-700">Ver todas</a>
  </div>

  <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @for($i=0; $i<3; $i++)
      <article class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
        <div class="aspect-[16/9] bg-slate-100 dark:bg-slate-800"></div>
        <div class="p-5">
          <h3 class="text-base font-semibold line-clamp-2">Título da notícia</h3>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 line-clamp-3">
            Resumo curto da notícia… (substituir futuramente por dados reais)
          </p>
        </div>
      </article>
    @endfor
  </div>
</section>

{{-- Eventos --}}
<section id="eventos" class="mt-12">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-semibold">Eventos disponíveis</h2>
    <a href="#" class="text-sm text-brand-600 hover:text-brand-700">Ver agenda</a>
  </div>

  <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @for($i=0; $i<3; $i++)
      <article class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
        <div class="aspect-[16/9] bg-slate-100 dark:bg-slate-800"></div>
        <div class="p-5">
          <h3 class="text-base font-semibold line-clamp-2">Título do evento</h3>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Data e horário</p>
        </div>
      </article>
    @endfor
  </div>
</section>
@endsection
