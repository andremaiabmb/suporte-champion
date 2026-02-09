{{-- resources/views/dashboard/faqs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin • Ver FAQ')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <div class="flex items-center justify-between">
    <div>
      <nav class="text-xs text-slate-500 dark:text-slate-400">
        <a href="{{ route('dashboard.admin.index') }}" class="hover:underline">Dashboard</a>
        <span class="px-1">/</span>
        <a href="{{ route('admin.faqs.index') }}" class="hover:underline">FAQs</a>
        <span class="px-1">/</span>
        <span>Ver</span>
      </nav>
      <h1 class="mt-2 text-2xl md:text-3xl font-semibold">{{ $faq->question }}</h1>
      <p class="mt-1 text-slate-600 dark:text-slate-300">
        Slug: <code>{{ $faq->slug }}</code>
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      {{-- Publicar/Despublicar --}}
      <form method="POST" action="{{ route('admin.faqs.update', $faq->id) }}">
        @csrf @method('PUT')
        <input type="hidden" name="question" value="{{ $faq->question }}">
        <input type="hidden" name="slug" value="{{ $faq->slug }}">
        <input type="hidden" name="answer" value="{{ $faq->answer }}">
        {{-- toggle publish --}}
        <input type="hidden" name="publish" value="{{ $faq->isPublished() ? 0 : 1 }}">
        <button
          class="rounded-xl {{ $faq->isPublished() ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white px-4 py-2 text-sm font-semibold">
          {{ $faq->isPublished() ? 'Despublicar' : 'Publicar' }}
        </button>
      </form>

      <a href="{{ route('admin.faqs.edit', $faq->id) }}"
         class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50 dark:bg-slate-800 dark:border-white/10 dark:hover:bg-slate-700">
        Editar
      </a>

      <a href="{{ route('admin.faqs.index') }}"
         class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold dark:bg-slate-200 dark:text-slate-900">
        Listagem
      </a>

      {{-- Remover --}}
      <form method="POST" action="{{ route('admin.faqs.destroy', $faq->id) }}"
            onsubmit="return confirm('Remover este FAQ? Esta ação é irreversível.');">
        @csrf @method('DELETE')
        <button
          class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100 dark:border-red-400/30 dark:bg-red-900/20 dark:text-red-300">
          Remover
        </button>
      </form>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

  {{-- Resumo --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <div class="flex flex-wrap items-center justify-between gap-2">
      <h2 class="text-lg font-semibold">Resumo</h2>
      <div class="text-xs text-slate-500 dark:text-slate-400">
        @if(method_exists($faq, 'user') && $faq->relationLoaded('user') && $faq->user)
          Autor: <span class="font-medium">{{ $faq->user->name }}</span>
          <span class="px-1">•</span>
        @endif
        Criado: {{ optional($faq->created_at)->format('d/m/Y H:i') ?? '—' }}
        <span class="px-1">•</span>
        Atualizado: {{ optional($faq->updated_at)->format('d/m/Y H:i') ?? '—' }}
      </div>
    </div>

    <div class="mt-3 whitespace-pre-line">{{ $faq->answer }}</div>

    <div class="mt-3 text-sm">
      Status:
      <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $faq->isPublished() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' }}">
        {{ $faq->isPublished() ? 'Publicado' : 'Rascunho' }}
      </span>
    </div>
  </div>

  {{-- Passo a passo --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <h2 class="text-lg font-semibold">Passo a passo</h2>
    <div class="mt-4 space-y-6">
      @forelse ($faq->steps as $s)
        <div class="rounded-xl border border-slate-200 p-4 dark:border-white/10">
          <div class="flex flex-col md:flex-row gap-4">
            @if ($s->image_path)
              <div class="md:w-56 shrink-0">
                @php
                  $isAbsolute = Str::startsWith($s->image_path, ['http://', 'https://', '//']);
                  $imgSrc = $isAbsolute ? $s->image_path : asset('storage/'.$s->image_path);
                @endphp
                <img src="{{ $imgSrc }}" alt="{{ $s->image_caption }}" class="w-full rounded-lg border border-slate-200 dark:border-white/10">
                @if ($s->image_caption)
                  <div class="text-xs text-slate-500 mt-1">{{ $s->image_caption }}</div>
                @endif
              </div>
            @endif
            <div class="grow">
              @if ($s->title)
                <h3 class="text-base font-semibold">{{ $s->step_number }}. {{ $s->title }}</h3>
              @else
                <h3 class="text-base font-semibold">Passo {{ $s->step_number }}</h3>
              @endif
              <div class="prose prose-slate dark:prose-invert max-w-none mt-2 whitespace-pre-line text-sm">
                {{ $s->body }}
              </div>
            </div>
          </div>
        </div>
      @empty
        <p class="text-slate-500">Nenhum passo cadastrado.</p>
      @endforelse
    </div>
  </div>

</div>
@endsection
