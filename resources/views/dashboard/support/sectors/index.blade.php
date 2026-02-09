@extends('layouts.app')
@section('title','Suporte • Moderação')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Moderação de Chamados</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">Filtre, assuma, reatribua e mude status.</p>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
  {{-- Filtros --}}
  <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
    <select name="status" class="rounded-xl border-slate-200 dark:border-white/10" onchange="this.form.submit()">
      <option value="">Todos status</option>
      @foreach(['open'=>'Abertos','in_progress'=>'Em andamento','waiting'=>'Aguardando','resolved'=>'Resolvidos','closed'=>'Fechados','reopened'=>'Reabertos'] as $k=>$v)
        <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
      @endforeach
    </select>

    <select name="sector" class="rounded-xl border-slate-200 dark:border-white/10" onchange="this.form.submit()">
      <option value="">Todos setores</option>
      @foreach($sectors as $s)
        <option value="{{ $s->id }}" @selected(request('sector')==$s->id)>{{ $s->name }}</option>
      @endforeach
    </select>

    <div class="md:col-span-2 flex gap-2">
      <a href="{{ route('admin.supports.index') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 dark:border-white/10 dark:hover:bg-slate-800">
        Limpar
      </a>
    </div>
  </form>

  {{-- Tabela --}}
  <div class="mt-4 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2 pr-3">Código</th>
          <th class="py-2 pr-3">Assunto</th>
          <th class="py-2 pr-3">Setor</th>
          <th class="py-2 pr-3">Autor</th>
          <th class="py-2 pr-3">Status</th>
          <th class="py-2">Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tickets as $t)
        <tr class="border-t border-slate-100 dark:border-white/10 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
          <td class="py-2 pr-3 font-mono text-xs">{{ $t->code }}</td>
          <td class="py-2 pr-3">{{ $t->subject }}</td>
          <td class="py-2 pr-3">{{ $t->sector->name ?? '—' }}</td>
          <td class="py-2 pr-3">{{ $t->author->name ?? '—' }}</td>
          <td class="py-2 pr-3">
            <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 dark:bg-slate-800">
              {{ strtoupper($t->status) }}
            </span>
          </td>
          <td class="py-2">
            {{-- Rota CORRETA --}}
            <a
              href="{{ route('admin.supports.show', $t) }}@if(request()->getQueryString())?{{ request()->getQueryString() }}@endif"
              class="text-brand-700 hover:underline"
              title="Abrir chamado {{ $t->code }}"
            >
              Abrir
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $tickets->withQueryString()->links() }}</div>
</div>
@endsection
