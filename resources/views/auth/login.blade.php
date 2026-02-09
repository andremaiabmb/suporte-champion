@extends('layouts.app')

@section('title', 'Entrar · Suporte ao Aluno')

@section('hero')
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl mt-6 shadow-soft">
  <div class="px-6 py-12 sm:px-10">
    <div class="max-w-3xl">
      <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
        Entrar
      </h1>
      <p class="mt-3 text-slate-600 dark:text-slate-300">
        Acesse sua conta para acompanhar avisos, eventos e suporte.
      </p>
    </div>
  </div>
</section>
@endsection

@section('content')
<section class="mt-8">
  <div class="max-w-md mx-auto">
    {{-- Alertas / Status (ex.: link de redefinição enviado) --}}
    @if (session('status'))
      <div class="mb-4 rounded-xl border border-emerald-300/50 bg-emerald-50/70 text-emerald-800 px-4 py-3 dark:bg-emerald-900/30 dark:text-emerald-200">
        {{ session('status') }}
      </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm p-6">
      <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div>
          <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">E-mail</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
          @error('email')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="mt-4">
          <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Senha</label>
          <input id="password" name="password" type="password" required
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
          @error('password')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="mt-4 flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
            <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700">
            Lembrar de mim
          </label>

          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-brand-600 hover:text-brand-700">
              Esqueci minha senha
            </a>
          @endif
        </div>

        <button type="submit"
                class="mt-6 w-full inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-white font-medium shadow hover:bg-brand-700 transition">
          Entrar
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-300">
        Ainda não tem conta?
        @if (Route::has('register'))
          <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 font-medium">Criar conta</a>
        @endif
      </p>
    </div>
  </div>
</section>
@endsection
