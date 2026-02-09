@php
  $isEdit = isset($faq) && $faq->exists;
@endphp

@if (session('status'))
  <div class="mb-4 rounded-xl border border-slate-200 bg-white p-3 text-sm dark:bg-slate-900 dark:border-white/10">
    {{ session('status') }}
  </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Pergunta</label>
        <input type="text" name="question" value="{{ old('question', $faq->question) }}" required
               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 dark:bg-slate-900 dark:border-white/10">
        @error('question') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $faq->slug) }}" placeholder="(opcional – deixei em branco para gerar automaticamente)"
               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 dark:bg-slate-900 dark:border-white/10">
        @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Resposta (resumo/introdução)</label>
        <textarea name="answer" rows="5" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 dark:bg-slate-900 dark:border-white/10">{{ old('answer', $faq->answer) }}</textarea>
        @error('answer') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <div class="space-y-3">
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="publish" value="1" {{ old('publish', $faq->published_at ? 1 : 0) ? 'checked' : '' }}>
        Publicar FAQ
      </label>
      <p class="text-xs text-slate-500 dark:text-slate-400">
        Se marcado, o FAQ será publicado (campo <code>published_at</code>).
      </p>
      <p class="text-xs text-slate-500 dark:text-slate-400">
        Para exibir imagens, garanta o symlink: <code>php artisan storage:link</code>.
      </p>
    </div>
  </div>

  {{-- Passos --}}
  <div class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Passo a passo com imagens</h2>
      <button type="button" id="addStepBtn"
              class="rounded-xl bg-slate-900 text-white px-3 py-2 text-sm font-medium dark:bg-slate-200 dark:text-slate-900">
        Adicionar passo
      </button>
    </div>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Você pode incluir título, texto, imagem e legenda para cada passo. A ordem é a do formulário.</p>

    <div id="stepsWrapper" class="mt-4 space-y-4">
      @php
        $oldSteps = old('steps', $isEdit ? $faq->steps->map(function($s){
          return [
            'title' => $s->title,
            'body'  => $s->body,
            'image_caption' => $s->image_caption,
            'existing_image' => $s->image_path,
          ];
        })->toArray() : []);
      @endphp

      @forelse ($oldSteps as $i => $s)
        @include('dashboard.faqs.partials._step', ['index' => $i, 'step' => $s])
      @empty
        {{-- inicia sem passos --}}
      @endforelse
    </div>
  </div>
</div>

{{-- Template oculto de passo --}}
<template id="stepTemplate">
  @include('dashboard.faqs.partials._step', ['index' => '__INDEX__', 'step' => ['title'=>'','body'=>'','image_caption'=>'','existing_image'=>'']])
</template>

<script>
  (function() {
    const wrapper = document.getElementById('stepsWrapper');
    const tpl = document.getElementById('stepTemplate').innerHTML;
    const addBtn = document.getElementById('addStepBtn');

    let nextIndex = {{ max(0, count($oldSteps)) }};

    function addStep() {
      const html = tpl.replaceAll('__INDEX__', nextIndex);
      const div = document.createElement('div');
      div.innerHTML = html.trim();
      wrapper.appendChild(div.firstElementChild);
      nextIndex++;
    }

    addBtn?.addEventListener('click', addStep);

    window.removeStep = function(el) {
      const card = el.closest('[data-step-card]');
      if (card) card.remove();
    }
  })();
</script>
