@extends('layouts.app')

@section('title', 'Registrar · Suporte ao Aluno')

@section('hero')
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl mt-6 shadow-soft">
  <div class="px-6 py-12 sm:px-10">
    <div class="max-w-3xl">
      <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
        Criar conta
      </h1>
      <p class="mt-3 text-slate-600 dark:text-slate-300">
        Auto cadastro exclusivo para colaboradores <strong>@champion.edu</strong>.
      </p>
    </div>
  </div>
</section>
@endsection

@section('content')
<section class="mt-8">
  <div class="max-w-xl mx-auto">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm p-6">
      <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        {{-- Nome Completo --}}
        <div>
          <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nome Completo</label>
          <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
          @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        {{-- Cidade onde mora --}}
        <div class="mt-4">
          <label for="city" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Cidade onde mora</label>
          <input id="city" name="city" type="text" value="{{ old('city') }}" required
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
          @error('city') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        {{-- País de origem --}}
        <div class="mt-4">
          <label for="country_of_origin" class="block text-sm font-medium text-slate-700 dark:text-slate-200">País de origem</label>
          <input id="country_of_origin" name="country_of_origin" type="text" value="{{ old('country_of_origin') }}" required
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
          @error('country_of_origin') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        {{-- Data de Nascimento --}}
        <div class="mt-4">
          <label for="date_of_birth" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Data de Nascimento</label>
          <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
          @error('date_of_birth') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        {{-- E-mail (restrito a @champion.edu) --}}
        <div class="mt-4">
          <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">E-mail (@champion.edu)</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" required
                 placeholder="seu.nome@champion.edu"
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
          @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        {{-- Senha --}}
        <div class="mt-4">
          <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Senha</label>
          <input id="password" name="password" type="password" required
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
          @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        {{-- Confirmar Senha --}}
        <div class="mt-4">
          <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Confirmar senha</label>
          <input id="password_confirmation" name="password_confirmation" type="password" required
                 class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600">
        </div>

        <button type="submit"
                class="mt-6 w-full inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2 text-white font-medium shadow hover:bg-brand-700 transition">
          Criar conta
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-300">
        Já possui conta?
        @if (Route::has('login'))
          <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 font-medium">Entrar</a>
        @endif
      </p>
    </div>
  </div>
</section>
@endsection
