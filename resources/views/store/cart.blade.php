@extends('layouts.app')

@section('title', 'Carrinho')

@section('hero')
<div class="mt-6 rounded-3xl border border-slate-200 dark:border-white/10 bg-gradient-to-br from-emerald-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-6 md:p-10">
  <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Carrinho</h1>
  <p class="mt-2 text-slate-600 dark:text-slate-300">Revise seus itens antes de finalizar.</p>
</div>
@endsection

@section('content')
@if (session('success'))
  <div class="mt-6 rounded-xl border border-emerald-200/70 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200 dark:border-emerald-800 px-4 py-3">
    {{ session('success') }}
  </div>
@endif

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10">
    @if (empty($cart))
      <div class="p-8 text-center">
        <div class="mx-auto w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-slate-500 dark:text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 3h3l3.6 12.3A2 2 0 0 0 10.5 17h7.9a2 2 0 0 0 1.9-1.4L23 7H6"/></svg>
        </div>
        <p class="mt-4 font-medium">Seu carrinho está vazio</p>
        <a href="{{ route('store.index') }}" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 transition">
          Voltar à loja
        </a>
      </div>
    @else
      <div class="divide-y divide-slate-100 dark:divide-white/10">
        @foreach ($cart as $item)
          <div class="p-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                @if (!empty($item['thumb']))
                  <img src="{{ $item['thumb'] }}" alt="" class="w-full h-full object-cover">
                @else
                  <svg class="w-6 h-6 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 16l5-5 4 4 5-6 4 5"/>
                  </svg>
                @endif
              </div>
              <div>
                <div class="font-medium">{{ $item['title'] }}</div>
                <div class="text-sm text-slate-500">R$ {{ number_format($item['price'], 2, ',', '.') }} × {{ $item['qty'] }}</div>
              </div>
            </div>

            <form method="POST" action="{{ route('store.cart.remove') }}">
              @csrf
              <input type="hidden" name="product_id" value="{{ $item['id'] }}">
              <button class="inline-flex items-center gap-1 text-sm rounded-lg border border-slate-200 dark:border-white/10 px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800">
                Remover
              </button>
            </form>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Resumo --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="font-medium">Resumo</div>
    <div class="mt-3 flex items-center justify-between text-sm">
      <span>Subtotal</span>
      <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
    </div>
    <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">Frete e impostos calculados no checkout.</div>

    <div class="mt-5 flex items-center gap-2">
      <form method="POST" action="{{ route('store.cart.clear') }}">
        @csrf
        <button class="rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
          Limpar carrinho
        </button>
      </form>
      <a href="#" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 text-sm transition">
        Finalizar compra
      </a>
    </div>
  </div>
</div>
@endsection
