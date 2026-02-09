{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.app')

@section('title', 'Verifique seu e-mail')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <h1 class="text-2xl font-semibold">Confirme seu e-mail</h1>
  <p class="mt-2 text-slate-600 dark:text-slate-300">
    Obrigado por se registrar! Antes de começar, por favor verifique seu endereço de e-mail clicando no link que acabamos de enviar.
    Se você não recebeu o e-mail, podemos enviar outro.
    <br>
    <span class="text-slate-500 dark:text-slate-400 text-sm">
      Enviado para <strong>{{ auth()->user()->email }}</strong>
    </span>
  </p>

  @if (session('status') === 'verification-link-sent')
    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-700 dark:text-emerald-300">
      Um novo link de verificação foi enviado para o e-mail informado no cadastro.
    </div>
  @endif
</div>
@endsection

@section('content')
<div class="mx-auto max-w-xl">
  <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      {{-- Reenviar link --}}
      <form method="POST" action="{{ route('verification.send') }}" class="order-2 sm:order-1">
        @csrf
        <button type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600/50">
          Reenviar link de verificação
        </button>
      </form>

      {{-- Sair --}}
      <form method="POST" action="{{ route('logout') }}" class="order-1 sm:order-2">
        @csrf
        <button type="submit"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-white/10 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-300">
          Sair
        </button>
      </form>
    </div>

    {{-- Ajuda/observações --}}
    <div class="mt-6 text-sm text-slate-600 dark:text-slate-300">
      <p>
        Dica: verifique a pasta <strong>Spam</strong> ou <strong>Promoções</strong>.
        Se o e-mail não aparecer, use o botão acima para reenviar.
      </p>
    </div>
  </div>
</div>
@endsection
