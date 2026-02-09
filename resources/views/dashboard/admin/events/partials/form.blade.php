{{-- Parâmetros esperados:
  - $action (string)      => rota do form
  - $method (string)      => 'POST' | 'PUT'
  - $event  (Model|null)  => evento ou null
  - $submitLabel (string) => texto do botão
--}}
<div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
  <form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
      @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      {{-- Título --}}
      <div class="col-span-1 md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Título</label>
        <input type="text" name="title" required
               value="{{ old('title', $event->title ?? '') }}"
               class="mt-1 w-full rounded-xl border-slate-200 dark:border-white/10 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
        @error('title') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      {{-- Início --}}
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Início</label>
        <input type="datetime-local" name="starts_at" required
               value="{{ old('starts_at', isset($event->starts_at) ? $event->starts_at->format('Y-m-d\TH:i') : '') }}"
               class="mt-1 w-full rounded-xl border-slate-200 dark:border-white/10 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
        @error('starts_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      {{-- Fim --}}
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Fim</label>
        <input type="datetime-local" name="ends_at" required
               value="{{ old('ends_at', isset($event->ends_at) ? $event->ends_at->format('Y-m-d\TH:i') : '') }}"
               class="mt-1 w-full rounded-xl border-slate-200 dark:border-white/10 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
        @error('ends_at') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="mt-6 flex items-center gap-2">
      <button class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">
        {{ $submitLabel }}
      </button>

      <a href="{{ route('admin.events.index') }}"
         class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50 dark:bg-slate-800 dark:border-white/10 dark:hover:bg-slate-700">
        Cancelar
      </a>

      @isset($event)
        @if (Route::has('events.subscribe'))
          <a href="{{ route('events.subscribe', $event) }}"
             class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50 dark:bg-slate-800 dark:border-white/10 dark:hover:bg-slate-700">
            Ver página de inscrição
          </a>
        @endif
      @endisset
    </div>
  </form>
</div>
