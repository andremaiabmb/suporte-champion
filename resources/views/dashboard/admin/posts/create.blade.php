@extends('layouts.app')

@section('title', 'Novo Post • Admin')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Novo Post</h1>

<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
  @csrf

  <div>
    <label class="text-sm font-medium">Título</label>
    <input type="text" name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm font-medium">Slug (opcional)</label>
    <input type="text" name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="minha-noticia-123">
    @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm font-medium">Resumo</label>
    <textarea name="excerpt" rows="2" class="mt-1 w-full rounded-lg border px-3 py-2">{{ old('excerpt') }}</textarea>
    @error('excerpt') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="text-sm font-medium">Conteúdo</label>
    <textarea name="content" rows="8" class="mt-1 w-full rounded-lg border px-3 py-2" required>{{ old('content') }}</textarea>
    @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium">Publicar em</label>
      <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="mt-1 w-full rounded-lg border px-3 py-2">
      @error('published_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
      <label class="text-sm font-medium">Capa (imagem)</label>
      <input type="file" name="cover" accept="image/*" class="mt-1 w-full rounded-lg border px-3 py-2">
      @error('cover') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
  </div>

  <div class="pt-2">
    <button class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">Criar</button>
    <a href="{{ route('admin.posts.index') }}" class="ml-2 text-sm underline">Cancelar</a>
  </div>
</form>
@endsection
