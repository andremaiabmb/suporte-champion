@extends('layouts.app')

@section('title', 'Financeiro • Dashboard')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8 md:p-10">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Painel Financeiro</h1>
      <p class="mt-3 text-slate-600 dark:text-slate-300 leading-relaxed">
        Indicadores e atalhos para rotinas financeiras.
      </p>
    </div>

    {{-- AÇÕES --}}
    <div class="flex items-center gap-2">
      <a href="{{ route('dashboard.financeiro.index') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        <i data-lucide="gauge" class="w-4 h-4 text-slate-700 dark:text-slate-200"></i>
        <span>Visão Geral</span>
      </a>
      <a href="{{ route('admin.supports.index') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        <i data-lucide="life-buoy" class="w-4 h-4 text-slate-700 dark:text-slate-200"></i>
        <span>Chamados</span>
      </a>
    </div>
  </div>
</div>
@endsection

@section('content')
@php
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Facades\Schema;
  $safeSum = function ($t, $c) { try { return Schema::hasTable($t) ? (float) DB::table($t)->sum($c) : 0.0; } catch (\Throwable $e) { return 0.0; } };
  $safeCount = function ($t) { try { return Schema::hasTable($t) ? (int) DB::table($t)->count() : 0; } catch (\Throwable $e) { return 0; } };
  $metrics = [
    'receitas' => $safeSum('transactions','credit'),
    'despesas' => $safeSum('transactions','debit'),
    'faturas'  => $safeCount('invoices'),
    'tickets'  => $safeCount('tickets') ?: $safeCount('support_tickets') ?: $safeCount('chamados'),
  ];
  $labels = ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'];
  mt_srand(crc32('financeiro'.date('Y-m-d')));
  $values = array_map(fn() => max(0, 15 + mt_rand(-10, 10)), range(1,7));
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">
  {{-- Receitas --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex p-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/40">
          <i data-lucide="wallet" class="w-5 h-5 text-emerald-700 dark:text-emerald-200"></i>
        </span>
        <h3 class="font-medium text-slate-700 dark:text-slate-100">Receitas</h3>
      </div>
      <span class="text-sm text-slate-400 dark:text-slate-500">—</span>
    </div>
    <div class="mt-4">
      <div class="text-3xl font-semibold text-slate-900 dark:text-white">${{ number_format($metrics['receitas'], 2) }}</div>
      <div class="text-sm text-slate-500 dark:text-slate-400">Período total</div>
    </div>
  </div>

  {{-- Despesas --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex p-2.5 rounded-xl bg-rose-100 dark:bg-rose-900/40">
          <i data-lucide="credit-card" class="w-5 h-5 text-rose-700 dark:text-rose-200"></i>
        </span>
        <h3 class="font-medium text-slate-700 dark:text-slate-100">Despesas</h3>
      </div>
      <span class="text-sm text-slate-400 dark:text-slate-500">—</span>
    </div>
    <div class="mt-4">
      <div class="text-3xl font-semibold text-slate-900 dark:text-white">${{ number_format($metrics['despesas'], 2) }}</div>
      <div class="text-sm text-slate-500 dark:text-slate-400">Período total</div>
    </div>
  </div>

  {{-- Faturas --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex p-2.5 rounded-xl bg-sky-100 dark:bg-sky-900/40">
          <i data-lucide="file-invoice" class="w-5 h-5 text-sky-700 dark:text-sky-200"></i>
        </span>
        <h3 class="font-medium text-slate-700 dark:text-slate-100">Faturas</h3>
      </div>
      <span class="text-sm text-slate-400 dark:text-slate-500">—</span>
    </div>
    <div class="mt-4">
      <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $metrics['faturas'] }}</div>
      <div class="text-sm text-slate-500 dark:text-slate-400">Criadas</div>
    </div>
  </div>

  {{-- Chamados --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex p-2.5 rounded-xl bg-amber-100 dark:bg-amber-900/40">
          <i data-lucide="life-buoy" class="w-5 h-5 text-amber-700 dark:text-amber-200"></i>
        </span>
        <h3 class="font-medium text-slate-700 dark:text-slate-100">Chamados</h3>
      </div>
      <a href="{{ route('admin.supports.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 inline-flex items-center gap-1">
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </a>
    </div>
    <div class="mt-4">
      <div class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $metrics['tickets'] }}</div>
      <div class="text-sm text-slate-500 dark:text-slate-400">Abertos/Total</div>
    </div>
  </div>
</div>

{{-- Gráfico --}}
<div class="mt-6 rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-6">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2">
      <span class="inline-flex p-2 rounded-xl bg-brand-100 dark:bg-brand-900/40">
        <i data-lucide="chart-line" class="w-5 h-5 text-brand-700 dark:text-brand-200"></i>
      </span>
      <h3 class="font-medium text-slate-700 dark:text-slate-100">Movimentação semanal</h3>
    </div>
  </div>

  <div class="relative h-48">
    <canvas id="miniChartFinanceiro" class="w-full h-full"></canvas>
  </div>

  @push('scripts')
  <script>
    (function () {
      const values = @json($values);
      const el = document.getElementById('miniChartFinanceiro'); if (!el) return;
      const dpr = window.devicePixelRatio || 1, w = el.clientWidth, h = el.clientHeight;
      el.width = w*dpr; el.height = h*dpr; const ctx = el.getContext('2d'); ctx.scale(dpr,dpr);
      const axis = getComputedStyle(document.body).color;
      ctx.strokeStyle = axis + '33'; ctx.lineWidth = 1; ctx.beginPath();
      [0.25,0.5,0.75].forEach(r=>{const y=h*r;ctx.moveTo(0,y);ctx.lineTo(w,y);}); ctx.stroke();
      const pad=24,cw=w-pad*2,ch=h-pad*2,max=Math.max(...values,1),min=Math.min(...values,0),span=Math.max(max-min,1);
      const pts=values.map((v,i)=>({x:pad+(i/(values.length-1))*cw,y:pad+(1-(v-min)/span)*ch}));
      ctx.strokeStyle = axis; ctx.lineWidth = 2; ctx.beginPath();
      pts.forEach((p,i)=> i? ctx.lineTo(p.x,p.y): ctx.moveTo(p.x,p.y)); ctx.stroke();
      ctx.fillStyle = axis; pts.forEach(p=>{ctx.beginPath();ctx.arc(p.x,p.y,3,0,Math.PI*2);ctx.fill();});
      document.dispatchEvent(new Event('lucide:refresh'));
    })();
  </script>
  @endpush
</div>
@endsection
