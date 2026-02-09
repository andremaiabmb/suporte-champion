@extends('layouts.app')
@section('title','Abrir Chamado')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Abrir Chamado</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">Descreva seu problema e selecione um setor.</p>
    </div>
    <a href="{{ route('support.my.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
      Voltar
    </a>
  </div>
</div>
@endsection

@section('content')
<form method="POST"
      action="{{ route('support.my.store') }}"
      enctype="multipart/form-data"
      class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl p-6 space-y-5">
  @csrf

  <div>
    <label class="block text-sm font-medium">Assunto <span class="text-red-600">*</span></label>
    <input type="text" name="subject"
           class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2.5"
           required maxlength="180" value="{{ old('subject') }}">
    @error('subject')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Setor</label>
      <select name="sector_id"
              class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2.5">
        <option value="">(Escolher depois)</option>
        @foreach($sectors as $s)
          <option value="{{ $s->id }}" @selected(old('sector_id')==$s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
      @error('sector_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Prioridade</label>
      <select name="priority"
              class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2.5">
        @foreach(['low'=>'Baixa','normal'=>'Normal','high'=>'Alta','urgent'=>'Urgente'] as $k=>$v)
          <option value="{{ $k }}" @selected(old('priority','normal')==$k)>{{ $v }}</option>
        @endforeach
      </select>
      @error('priority')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium">Descrição <span class="text-red-600">*</span></label>
    <textarea name="description" rows="6"
              class="mt-1 w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2.5"
              required>{{ old('description') }}</textarea>
    @error('description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium">Anexos</label>
    <input type="file" name="attachments[]" multiple
           class="mt-1 block w-full text-sm"
           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt">
    <p class="text-xs text-slate-500 mt-1">Até 10MB por arquivo. Tipos: JPG, PNG, PDF, DOC(X), XLS(X), TXT.</p>
    @error('attachments')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    @error('attachments.*')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  <div class="pt-2 flex items-center gap-2">
    <button class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 font-semibold">
      Enviar
    </button>
    <a href="{{ route('support.my.index') }}"
       class="rounded-xl border border-slate-200 dark:border-white/10 px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-800">
      Cancelar
    </a>
    <span class="ml-auto text-xs text-slate-500">Dica: pressione <kbd class="px-1.5 py-0.5 border rounded">Esc</kbd> para voltar</span>
  </div>
</form>

{{-- Atalho: Esc volta para a lista --}}
<script>
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') window.location.href = @json(route('support.my.index'));
  });
</script>
@endsection
