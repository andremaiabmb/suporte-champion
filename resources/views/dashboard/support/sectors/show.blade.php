@extends('layouts.app')

@section('title','Setor • '.$sector->name)

@section('content')
<div class="space-y-6">
  <!-- Cabeçalho -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl font-semibold">{{ $sector->name }}</h1>
      <p class="text-sm text-slate-600 dark:text-slate-300">
        Código: <span class="font-medium">{{ $sector->code }}</span>
        • Slug: <span class="font-mono text-xs">{{ $sector->slug }}</span>
      </p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.sectors.edit', $sector) }}"
         class="rounded-lg border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:border-white/10 dark:hover:bg-slate-800">
        Editar
      </a>
      <a href="{{ route('admin.sectors.index') }}"
         class="rounded-lg border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:border-white/10 dark:hover:bg-slate-800">
        Voltar
      </a>
    </div>
  </div>

  <!-- Painéis principais -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:bg-slate-900 dark:border-white/10">
      <div class="text-sm text-slate-500">Email</div>
      <div class="mt-1 font-medium">
        @if($sector->email)
          <a href="mailto:{{ $sector->email }}" class="hover:underline">{{ $sector->email }}</a>
        @else
          —
        @endif
      </div>
      <div class="mt-4 text-sm text-slate-500">Ativo</div>
      <div class="mt-1">
        <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $sector->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
          {{ $sector->is_active ? 'SIM' : 'NÃO' }}
        </span>
      </div>
    </div>

    <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 dark:bg-slate-900 dark:border-white/10">
      <div class="text-sm text-slate-500">Descrição</div>
      <div class="mt-1 whitespace-pre-line">{{ $sector->description ?: '—' }}</div>
    </div>
  </div>

  <!-- Staff -->
  <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:bg-slate-900 dark:border-white/10">
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-semibold">Staff do setor</h2>
      <a href="{{ route('admin.sectors.edit', $sector) }}"
         class="text-xs text-sky-700 hover:underline dark:text-sky-400">
        gerenciar
      </a>
    </div>

    <div class="mt-3 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-slate-500 dark:text-slate-400">
            <th class="py-2 pr-3">Nome</th>
            <th class="py-2 pr-3">E-mail</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sector->users as $u)
            <tr class="border-t border-slate-100 dark:border-white/10">
              <td class="py-2 pr-3">{{ $u->name }}</td>
              <td class="py-2 pr-3">
                <a href="mailto:{{ $u->email }}" class="hover:underline">{{ $u->email }}</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="py-6 text-center text-slate-400">Nenhum membro associado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
