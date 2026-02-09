@extends('layouts.app')

@section('title', 'Admin • FAQs')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">FAQs</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">Gerencie perguntas frequentes com passos ilustrados.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.faqs.create') }}"
         class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">
        Novo FAQ
      </a>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

  <form method="GET" action="{{ route('admin.faqs.index') }}" class="flex flex-wrap items-end gap-3">
    <div>
      <label class="block text-xs text-slate-500">Buscar</label>
      <input type="text" name="q" value="{{ $q }}" placeholder="Pergunta, resposta, slug..."
             class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
    </div>
    <div>
      <label class="block text-xs text-slate-500">Status</label>
      <select name="status" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
        <option value="all" {{ $status==='all'?'selected':'' }}>Todos</option>
        <option value="published" {{ $status==='published'?'selected':'' }}>Publicados</option>
        <option value="draft" {{ $status==='draft'?'selected':'' }}>Rascunhos</option>
      </select>
    </div>
    <div>
      <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold dark:bg-slate-200 dark:text-slate-900">Filtrar</button>
    </div>
  </form>

  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-white/10">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500 dark:text-slate-400">
          <th class="py-3 pl-4 pr-3">Pergunta</th>
          <th class="py-3 pr-3">Slug</th>
          <th class="py-3 pr-3">Status</th>
          <th class="py-3 pr-4 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
      @forelse ($faqs as $faq)
        <tr class="border-t border-slate-100 dark:border-white/10">
          <td class="py-3 pl-4 pr-3">{{ $faq->question }}</td>
          <td class="py-3 pr-3 text-slate-500">{{ $faq->slug }}</td>
          <td class="py-3 pr-3">
            <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $faq->isPublished() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' }}">
              {{ $faq->isPublished() ? 'Publicado' : 'Rascunho' }}
            </span>
          </td>
          <td class="py-3 pr-4 text-right">
            <a href="{{ route('admin.faqs.show', $faq->id) }}" class="text-sky-700 hover:underline dark:text-sky-400">ver</a>
            <span class="px-1">·</span>
            <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="text-slate-700 hover:underline dark:text-slate-200">editar</a>
            <span class="px-1">·</span>
            <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="inline"
                  onsubmit="return confirm('Remover este FAQ? Esta ação é irreversível.');">
              @csrf @method('DELETE')
              <button class="text-red-600 hover:underline">remover</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="py-10 text-center text-slate-400">Nenhum FAQ encontrado.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  @if ($faqs->hasPages())
    <div>{{ $faqs->links() }}</div>
  @endif

</div>
@endsection
