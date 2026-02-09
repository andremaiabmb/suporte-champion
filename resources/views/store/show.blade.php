@extends('layouts.app')

@section('title', $p->title ?? 'Produto')

@section('hero')
<div class="mt-6 rounded-3xl border border-slate-200 dark:border-white/10 bg-gradient-to-br from-sky-50 via-white to-indigo-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-6 md:p-10">
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">{{ $p->title }}</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">Detalhes, especificações e compra segura.</p>
    </div>
    <a href="{{ route('store.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
      Voltar
    </a>
  </div>
</div>
@endsection

@section('content')
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
  {{-- Galeria --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 overflow-hidden">
    <div class="aspect-[4/3] relative bg-gradient-to-br from-slate-100 to-slate-50 dark:from-slate-800 dark:to-slate-900">
      @if (!empty($p->thumbnail_url))
        <img src="{{ $p->thumbnail_url }}" alt="{{ $p->title }}" class="absolute inset-0 w-full h-full object-cover">
      @else
        <div class="absolute inset-0 flex items-center justify-center">
          <svg class="w-16 h-16 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 16l5-5 4 4 5-6 4 5"/>
          </svg>
        </div>
      @endif
    </div>
  </div>

  {{-- Info / Comprar --}}
  <div>
    <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="inline-flex w-2 h-2 rounded-full {{ ($p->stock ?? 1) > 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
          <span class="text-sm text-slate-500">{{ ($p->stock ?? 1) > 0 ? 'Em estoque' : 'Indisponível' }}</span>
        </div>
        <div class="text-xs text-slate-400">Cód: #{{ $p->id }}</div>
      </div>

      <div class="mt-4 flex items-baseline gap-3">
        <div class="text-2xl font-semibold">R$ {{ number_format($p->price, 2, ',', '.') }}</div>
        @if(($p->compare_at_price ?? null) && $p->compare_at_price > $p->price)
          <div class="text-sm text-slate-500 line-through">R$ {{ number_format($p->compare_at_price, 2, ',', '.') }}</div>
        @endif
      </div>

      <p class="mt-4 text-slate-600 dark:text-slate-300 leading-relaxed">
        {{ $p->description ?? 'Descrição não informada.' }}
      </p>

      <form class="mt-6" method="POST" action="{{ route('store.cart.add') }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $p->id }}">
        <input type="hidden" name="title" value="{{ $p->title }}">
        <input type="hidden" name="price" value="{{ $p->price }}">
        <input type="hidden" name="thumbnail_url" value="{{ $p->thumbnail_url }}">
        <div class="flex items-center gap-3">
          <button type="submit" {{ ($p->stock ?? 1) > 0 ? '' : 'disabled' }}
            class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-white transition {{ ($p->stock ?? 1) > 0 ? 'bg-brand-600 hover:bg-brand-700' : 'bg-slate-400 cursor-not-allowed' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 3h3l3.6 12.3A2 2 0 0 0 10.5 17h7.9a2 2 0 0 0 1.9-1.4L23 7H6"/></svg>
            Adicionar ao carrinho
          </button>
          <a href="{{ route('store.cart') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            Ir ao carrinho
          </a>
        </div>
      </form>
    </div>

    {{-- info extra --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
        <div class="font-medium">Entrega & retirada</div>
        <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">Prazo estimado informado no checkout. Retire no campus sem custo.</div>
      </div>
      <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
        <div class="font-medium">Garantia e suporte</div>
        <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">Troca em até 7 dias por defeito. Suporte pelo painel.</div>
      </div>
    </div>
  </div>
</div>
@endsection
