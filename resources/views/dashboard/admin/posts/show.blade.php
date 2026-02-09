@extends('layouts.app')

@section('title', 'Detalhe do Post • Admin')

@section('content')
<h1 class="text-2xl font-semibold mb-6">{{ $post->title }}</h1>

<div class="grid lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 rounded-2xl border p-4 bg-white dark:bg-slate-900 dark:border-white/10">
    @if ($post->coverSrcsets())
      <div class="aspect-[16/9] rounded-xl overflow-hidden mb-4 bg-slate-100 dark:bg-slate-800">
        <x-picture :srcsets="$post->coverSrcsets()" alt="{{ $post->title }}" class="w-full h-full object-cover"/>
      </div>
    @endif

    <div class="prose dark:prose-invert max-w-none">
      {!! nl2br(e($post->content)) !!}
    </div>
  </div>

  <aside class="space-y-3">
    <div class="rounded-xl border p-4 bg-white dark:bg-slate-900 dark:border-white/10">
      <div class="text-sm text-slate-500">Slug</div>
      <div class="font-mono text-sm">{{ $post->slug }}</div>
    </div>
    <div class="rounded-xl border p-4 bg-white dark:bg-slate-900 dark:border-white/10">
      <div class="text-sm text-slate-500">Publicado em</div>
      <div class="text-sm">{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : '—' }}</div>
    </div>
    <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex items-center rounded-xl border px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">Editar</a>
  </aside>
</div>
@endsection
