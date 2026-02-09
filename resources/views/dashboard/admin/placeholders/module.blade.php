@extends('layouts.app')

@section('title', $titulo ?? 'Módulo')

@section('content')
  <div class="mt-8 rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-8">
    <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $titulo ?? 'Módulo' }}</h2>
    <p class="mt-2 text-slate-600 dark:text-slate-300">{{ $mensagem ?? 'Módulo ainda não instalado.' }}</p>
  </div>
@endsection
