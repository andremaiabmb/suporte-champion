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

  {{-- Fonte (opcional): Inter --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    :root { color-scheme: light dark; }
    html { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; }
  </style>

  <script>
    // Tema inicial (darkMode: 'class')
    (function() {
      const pref = localStorage.getItem('theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if ((pref === 'dark') || (!pref && prefersDark)) {
        document.documentElement.classList.add('dark');
      }
    })();
  </script>
</head>
<body class="min-h-full bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
  {{-- Navbar --}}
  @include('layouts._navbar')

  {{-- Main --}}
  <main class="container">
    @yield('hero')
    <div class="py-8">
      @yield('content')
    </div>
  </main>

  {{-- Footer --}}
  @include('layouts._footer')

  {{-- Scripts layout (menu e tema) --}}
  <script>
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const themeToggle = document.getElementById('themeToggle');
    const sun = document.getElementById('sun');
    const moon = document.getElementById('moon');

    if (menuBtn && mobileMenu) {
      menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    }

    if (themeToggle && sun && moon) {
      themeToggle.addEventListener('click', () => {
        const root = document.documentElement;
        const isDark = root.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        sun.classList.toggle('hidden', isDark);
        moon.classList.toggle('hidden', !isDark);
      });
      // estado inicial do ícone
      const isDark = document.documentElement.classList.contains('dark');
      sun.classList.toggle('hidden', isDark);
      moon.classList.toggle('hidden', !isDark);
    }
  </script>
</body>
</html>
