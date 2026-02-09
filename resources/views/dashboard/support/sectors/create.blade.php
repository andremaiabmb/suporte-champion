@extends('layouts.app')

@section('title','Novo Setor • Suporte')

@section('content')
<div class="space-y-6">

  <!-- Breadcrumbs / Header -->
  <div class="flex items-start justify-between gap-3">
    <div>
      <nav class="text-xs text-slate-500 dark:text-slate-400 mb-1" aria-label="Breadcrumb">
        <ol class="inline-flex items-center gap-1">
          <li><a href="{{ route('admin.sectors.index') }}" class="hover:underline">Setores</a></li>
          <li aria-hidden="true">/</li>
          <li class="text-slate-600 dark:text-slate-300">Novo</li>
        </ol>
      </nav>
      <h1 class="text-xl font-semibold">Novo Setor</h1>
      <p class="text-sm text-slate-600 dark:text-slate-300">Cadastre um setor e vincule membros de staff.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.sectors.index') }}"
         class="rounded-lg border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:border-white/10 dark:hover:bg-slate-800">
        Voltar
      </a>
    </div>
  </div>

  <!-- Errors -->
  @if ($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:bg-red-900/20 dark:border-red-900/30 dark:text-red-200">
      <strong class="block mb-1 text-sm">Por favor, corrija os erros abaixo:</strong>
      <ul class="list-disc pl-5 text-sm space-y-0.5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Form -->
  <form method="POST"
        action="{{ route('admin.sectors.store') }}"
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/10 p-5">
    @csrf

    @include('dashboard.support.sectors._form')

    <div class="mt-5 flex items-center justify-end gap-2">
      <a href="{{ route('admin.sectors.index') }}"
         class="rounded-lg border border-slate-200 px-4 py-2 text-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:border-white/10 dark:hover:bg-slate-800">
        Cancelar
      </a>
      <button
        class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
        Salvar
      </button>
    </div>
  </form>
</div>
@endsection
