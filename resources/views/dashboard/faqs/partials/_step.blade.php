<div data-step-card class="rounded-xl border border-dashed border-slate-300 p-4 dark:border-white/10">
  <div class="flex items-start gap-3">
    <div class="grow grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-slate-500">Título do passo (opcional)</label>
        <input type="text" name="steps[{{ $index }}][title]" value="{{ $step['title'] ?? '' }}"
               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
      </div>
      <div>
        <label class="block text-xs text-slate-500">Legenda da imagem (opcional)</label>
        <input type="text" name="steps[{{ $index }}][image_caption]" value="{{ $step['image_caption'] ?? '' }}"
               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
      </div>
      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500">Descrição</label>
        <textarea name="steps[{{ $index }}][body]" rows="3"
          class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">{{ $step['body'] ?? '' }}</textarea>
      </div>
      <div>
        <label class="block text-xs text-slate-500">Imagem (jpg, png, webp)</label>
        <input type="file" name="steps[{{ $index }}][image]" accept="image/*"
               class="mt-1 w-full text-sm file:mr-3 file:rounded-lg file:border file:border-slate-200 file:bg-white file:px-3 file:py-2 file:text-sm dark:file:bg-slate-800 dark:file:border-white/10">
        @if (!empty($step['existing_image']))
          <input type="hidden" name="steps[{{ $index }}][existing_image]" value="{{ $step['existing_image'] }}">
          <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">Imagem atual: <code>{{ $step['existing_image'] }}</code></div>
          <img src="{{ asset('storage/'.$step['existing_image']) }}" class="mt-2 h-28 rounded-lg border border-slate-200 dark:border-white/10" alt="">
        @endif
      </div>
    </div>

    <div class="shrink-0">
      <button type="button" onclick="removeStep(this)"
              class="rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-300">
        Remover
      </button>
    </div>
  </div>
</div>
