@extends('layouts.app')

@section('title', 'Editar Post • Admin')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Editar Post</h1>

@if (session('status'))
  <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-700">{{ session('status') }}</div>
@endif

<form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
  @csrf @method('PUT')

  <div>
    <label class="text-sm font-medium">Título</label>
    <input type="text" name="title" value="{{ old('title', $post->title) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm font-medium">Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" class="mt-1 w-full rounded-lg border px-3 py-2">
    @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm font-medium">Resumo</label>
    <textarea name="excerpt" rows="2" class="mt-1 w-full rounded-lg border px-3 py-2">{{ old('excerpt', $post->excerpt) }}</textarea>
    @error('excerpt') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm font-medium">Conteúdo</label>
    <textarea name="content" rows="8" class="mt-1 w-full rounded-lg border px-3 py-2" required>{{ old('content', $post->content) }}</textarea>
    @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="grid sm:grid-cols-2 gap-6 items-start">
    <div>
      <label class="text-sm font-medium">Publicar em</label>
      <input type="datetime-local" name="published_at"
             value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}"
             class="mt-1 w-full rounded-lg border px-3 py-2">
      @error('published_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="text-sm font-medium">Capa</label>
      <div class="mt-1 space-y-2">
        <div class="aspect-[16/9] rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800">
          @if ($post->coverSrcsets())
            <x-picture :srcsets="$post->coverSrcsets()" alt="{{ $post->title }}" class="w-full h-full object-cover"/>
          @endif
        </div>
        <div class="flex items-center gap-2">
          <label class="inline-flex items-center rounded-lg border px-3 py-2 text-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800">
            Trocar imagem
            <input type="file" name="cover" accept="image/*" class="hidden">
          </label>

          @if ($post->cover_path)
            <label class="inline-flex items-center gap-2 text-sm">
              <input type="checkbox" name="remove_cover" value="1" class="rounded border">
              Remover capa
            </label>
          @endif
        </div>
        @error('cover') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>
  </div>

  <div class="pt-2">
    <button class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">Salvar</button>
    <a href="{{ route('admin.posts.index') }}" class="ml-2 text-sm underline">Voltar</a>
  </div>
</form>
@endsection
