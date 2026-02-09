@extends('layouts.app')

@section('title', 'Posts • Admin')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-semibold">Posts</h1>
  <a href="{{ route('admin.posts.create') }}" class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">Novo Post</a>
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
  @forelse ($posts as $post)
    <article class="rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-white/10 shadow-sm overflow-hidden">
      <a href="{{ route('admin.posts.edit', $post) }}" class="block">
        <div class="aspect-[16/9] bg-slate-100 dark:bg-slate-800">
          @if ($post->coverSrcsets())
            <x-picture :srcsets="$post->coverSrcsets()" alt="{{ $post->title }}" class="w-full h-full object-cover"/>
          @endif
        </div>
        <div class="p-4">
          <h3 class="font-semibold line-clamp-2">{{ $post->title }}</h3>
          <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
            {{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : 'Rascunho' }}
          </div>
        </div>
      </a>
      <div class="px-4 pb-4 flex items-center gap-2">
        <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm px-3 py-1.5 rounded-lg border hover:bg-slate-50 dark:hover:bg-slate-800">Editar</a>
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Remover post?')">
          @csrf @method('DELETE')
          <button class="text-sm px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Excluir</button>
        </form>
      </div>
    </article>
  @empty
    <p class="text-slate-500">Nenhum post encontrado.</p>
  @endforelse
</div>

<div class="mt-6">
  {{ $posts->links() }}
</div>
@endsection
