@php
  $appLocale = str_replace('_','-', app()->getLocale());
  $pageTitle = trim($__env->yieldContent('title') ?: 'Suporte ao Aluno');
@endphp
<!doctype html>
<html lang="{{ $appLocale }}" class="h-full antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ $pageTitle }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    :root { color-scheme: light dark; }
    html { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; }
  </style>

  <script>
    (function () {
      try {
        const pref = localStorage.getItem('theme');
        if (pref === 'dark') document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
      } catch (_) {}
    })();
  </script>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
  {{-- Navbar --}}
  @include('layouts._navbar')

  {{-- Main --}}
  <main class="container flex-1">
    @yield('hero')
    <div class="py-8">
      @yield('content')
    </div>
  </main>

  {{-- Footer --}}
  @include('layouts._footer')

  {{-- Scripts layout --}}
  <script>
    const themeToggle = document.getElementById('themeToggle');
    const sun = document.getElementById('sun');
    const moon = document.getElementById('moon');
    if (themeToggle) {
      themeToggle.addEventListener('click', () => {
        const root = document.documentElement;
        const isDark = root.classList.toggle('dark');
        try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (_) {}
        if (sun && moon) {
          sun.classList.toggle('hidden', isDark);
          moon.classList.toggle('hidden', !isDark);
        }
      });
    }
    (function syncThemeIcons() {
      const isDark = document.documentElement.classList.contains('dark');
      if (sun && moon) {
        sun.classList.toggle('hidden', isDark);
        moon.classList.toggle('hidden', !isDark);
      }
    })();
  </script>

  @stack('scripts')
  @include('partials.lucide')
</body>
</html>
