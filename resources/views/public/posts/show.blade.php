@extends('layouts.app')

@section('title', ($post->title ?? 'Notícia') . ' · Notícias')

@section('hero')
<section class="mt-6 rounded-3xl bg-gradient-to-br from-brand-50 via-white to-sky-50 
dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 border border-slate-200/70 
dark:border-white/10 shadow-soft">
  <div class="px-5 py-7 sm:px-8">
    @php
      use Illuminate\Support\Facades\DB;
      use Illuminate\Support\Str;

      $item = $post ?? null;

      // === Autor: busca direto de users.name ===
      $authorName = 'Redação';
      try {
          if (!empty($item?->user_id)) {
              $author = DB::table('users')->where('id', $item->user_id)->value('name');
              if ($author) $authorName = $author;
          }
      } catch (\Throwable $e) {}

      // === Data de publicação ===
      $publishedRaw = $item?->published_at ?? $item?->created_at ?? $item?->updated_at ?? null;
      try {
          $publishedFmt = $publishedRaw ? \Carbon\Carbon::parse($publishedRaw)->format('d/m/Y H:i') : null;
      } catch (\Throwable $e) { $publishedFmt = null; }
    @endphp

    <div class="max-w-4xl">
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight">{{ $item->title ?? 'Notícia' }}</h1>
      <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
        <div class="inline-flex items-center gap-1">
          <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
          <span>{{ $authorName }}</span>
        </div>
        @if($publishedFmt)
          <span aria-hidden="true">·</span>
          <div class="inline-flex items-center gap-1">
            <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M8 2v4M16 2v4M3 10h18M5 8h14v12H5z"/>
            </svg>
            <time datetime="{{ $publishedRaw }}">{{ $publishedFmt }}</time>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endsection

@section('content')
@php
  // === Normaliza imagem ===
  $resolveImg = function (?string $path): ?string {
    if (!$path) return null;
    $trim = ltrim($path, '/');
    if (Str::startsWith($trim, ['http://','https://','//'])) return $path;
    if (Str::startsWith($trim, 'public/')) return asset(Str::replaceFirst('public/', 'storage/', $trim));
    if (Str::startsWith($trim, 'storage/')) return asset($trim);
    return asset('storage/'.$trim);
  };
  $coverUrl = $resolveImg($item->cover_path ?? null);

  // === Conteúdo ===
  $raw = $item->content ?? $item->body ?? '';
  $isHtml = Str::contains($raw, ['<p','<br','<h1','<h2','<ul','<ol','<div','<img','<section']);
  $contentHtml = $isHtml ? $raw : nl2br(e($raw));

  // === Leia também ===
  $related = DB::table('posts')
      ->select('id','title','slug','published_at','created_at')
      ->where('id','<>',$item->id)
      ->orderByDesc(DB::raw('COALESCE(published_at, created_at)'))
      ->limit(3)
      ->get();
@endphp

<div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
  {{-- TEXTO --}}
  <article class="lg:col-span-7">
    <div class="rounded-2xl border border-slate-200 dark:border-white/10 
                bg-white dark:bg-slate-900 p-5 sm:p-6">
      @if(!empty($item->excerpt))
        <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed">{{ $item->excerpt }}</p>
        <hr class="my-5 border-slate-200 dark:border-white/10">
      @endif

      <div class="prose max-w-none prose-headings:scroll-mt-20 prose-img:rounded-xl
                  prose-a:text-brand-700 hover:prose-a:text-brand-800
                  dark:prose-invert dark:prose-a:text-brand-300 dark:hover:prose-a:text-brand-200">
        {!! $contentHtml ?: '<p class="text-slate-500">Sem conteúdo disponível.</p>' !!}
      </div>

      {{-- COMPARTILHAR --}}
      @php
        $url = urlencode(request()->fullUrl());
        $title = urlencode($item->title ?? '');
      @endphp
      <div class="mt-8 pt-6 border-t border-slate-200 dark:border-white/10">
        <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Compartilhar</div>
        <div class="mt-2 flex flex-wrap items-center gap-2">
          <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" target="_blank" rel="noopener"
             class="inline-flex items-center gap-2 text-sm rounded-lg border border-slate-200 dark:border-white/10 px-2.5 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13 22v-9h3l1-4h-4V7a1 1 0 0 1 1-1h3V2h-3a5 5 0 0 0-5 5v3H6v4h3v9h4z"/></svg>
            Facebook
          </a>
          <a href="https://twitter.com/intent/tweet?url={{ $url }}&text={{ $title }}" target="_blank" rel="noopener"
             class="inline-flex items-center gap-2 text-sm rounded-lg border border-slate-200 dark:border-white/10 px-2.5 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.8c-.7.3-1.4.5-2.2.6.8-.5 1.3-1.2 1.6-2.1-.7.5-1.6.8-2.5 1-1.4-1.5-3.8-1.6-5.3-.2-1 .9-1.5 2.2-1.3 3.5-3-.1-5.8-1.6-7.6-4 0 1.6.8 3.1 2.1 3.9-.6 0-1.2-.2-1.7-.5 0 2.2 1.5 4 3.6 4.4-.4.1-.9.2-1.3.1.4 1.8 2 3.1 3.9 3.1-1.7 1.3-3.7 2-5.9 2-.4 0-.8 0-1.1-.1 2.2 1.4 4.8 2.2 7.4 2.2 8.9 0 13.7-7.4 13.7-13.7v-.6c.9-.6 1.6-1.3 2.1-2.2z"/></svg>
            X/Twitter
          </a>
        </div>
      </div>

      {{-- LEIA TAMBÉM --}}
      @if($related->count())
        <div class="mt-6">
          <div class="text-sm font-medium mb-3">Leia também</div>
          <div class="grid gap-4 sm:grid-cols-2">
            @foreach($related as $r)
              @php
                $rd = $r->published_at ?? $r->created_at ?? null;
                try { $rdFmt = $rd ? \Carbon\Carbon::parse($rd)->format('d/m/Y') : null; } catch (\Throwable $e) { $rdFmt = null; }
                $key = $r->slug ?? $r->id;
              @endphp
              <a href="{{ route('news.show', $key) }}"
                 class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-4 hover:shadow transition">
                <div class="text-sm font-medium line-clamp-2">{{ $r->title }}</div>
                @if($rdFmt)<div class="text-xs text-slate-500 mt-1">{{ $rdFmt }}</div>@endif
              </a>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </article>

  {{-- IMAGEM --}}
  <aside class="lg:col-span-5">
    <div class="lg:sticky lg:top-24">
      <div class="rounded-2xl border border-slate-200 dark:border-white/10 
                  bg-white dark:bg-slate-900 overflow-hidden flex items-center justify-center">
        @if ($coverUrl)
          <img src="{{ $coverUrl }}" alt="{{ $item->title ?? '' }}" class="max-w-full max-h-full object-contain">
        @else
          <div class="text-slate-400 text-xs p-6">Sem imagem</div>
        @endif
      </div>
    </div>
  </aside>
</div>
@endsection
