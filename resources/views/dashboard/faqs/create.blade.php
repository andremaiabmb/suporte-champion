@extends('layouts.app')

@section('title', 'Admin • Criar FAQ')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <h1 class="text-2xl md:text-3xl font-semibold">Criar FAQ</h1>
  <p class="mt-2 text-slate-600 dark:text-slate-300">Defina a pergunta, resposta e os passos com imagens.</p>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.faqs.store') }}" enctype="multipart/form-data" class="space-y-6">
  @csrf
  @include('dashboard.faqs._form', ['faq' => $faq])
  <div class="flex items-center gap-3">
    <button class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">Salvar</button>
    <a href="{{ route('admin.faqs.index') }}" class="text-sm text-slate-600 hover:underline dark:text-slate-300">Cancelar</a>
  </div>
</form>
@endsection
