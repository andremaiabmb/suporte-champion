@extends('layouts.app')
@section('title','Suporte • Meus Chamados')

@section('hero')
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <h1 class="text-2xl font-semibold">Suporte • Meus Chamados</h1>
  <p class="mt-2 text-slate-600 dark:text-slate-300">Abra, acompanhe e responda seus chamados.</p>
  <a href="{{ route('support.create') }}" class="mt-4 inline-flex rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">Abrir chamado</a>
</div>
@endsection

@section('content')
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl p-6">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2 pr-3">Código</th>
          <th class="py-2 pr-3">Assunto</th>
          <th class="py-2 pr-3">Setor</th>
          <th class="py-2 pr-3">Status</th>
          <th class="py-2">Atualizado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tickets as $t)
        <tr class="border-t border-slate-100 dark:border-white/10">
          <td class="py-2 pr-3"><a class="text-brand-700 hover:underline" href="{{ route('support.show',$t->code) }}">{{ $t->code }}</a></td>
          <td class="py-2 pr-3">{{ $t->subject }}</td>
          <td class="py-2 pr-3">{{ $t->sector->name ?? '—' }}</td>
          <td class="py-2 pr-3"><span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 dark:bg-slate-800">{{ strtoupper($t->status) }}</span></td>
          <td class="py-2">{{ optional($t->updated_at)->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="py-6 text-center text-slate-500">Nenhum chamado ainda.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-4">{{ $tickets->links() }}</div>
</div>
@endsection
