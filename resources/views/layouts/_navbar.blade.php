<header class="sticky top-0 z-50 bg-brand-900/95 backdrop-blur border-b border-white/10">
  <div class="container">
    <div class="flex h-16 items-center justify-between">
      <a href="{{ route('home') }}" class="flex items-center gap-2">
        <span class="inline-block h-8 w-8 rounded-lg bg-white/90"></span>
        <span class="text-white font-semibold">Suporte ao Aluno</span>
      </a>

      <nav class="hidden md:flex items-center gap-6">
        <a href="{{ route('home') }}"
           class="text-white/90 hover:text-white text-sm {{ request()->routeIs('home') ? 'underline underline-offset-4' : '' }}">
          Início
        </a>
        <a href="{{ route('faqs.index') }}"
           class="text-white/90 hover:text-white text-sm {{ request()->routeIs('faqs.*') ? 'underline underline-offset-4' : '' }}">
          Dúvidas Frequentes
        </a>
      </nav>

      {{-- Theme toggle (dark/light) --}}
      <button id="themeToggle"
              class="ml-3 inline-flex items-center justify-center h-9 w-9 rounded-lg border border-white/15 text-white/90 hover:bg-white/10"
              title="Alternar tema">
        <svg id="sun" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.8 1.42-1.42zm10.45 0l1.79-1.8 1.41 1.41-1.8 1.79-1.4-1.4zM12 4V1h-0v3h0zm0 19v-3h0v3h0zm8-8h3v0h-3v0zm-19 0h3v0H1v0zm14.24 7.16l1.8 1.79 1.41-1.41-1.79-1.8-1.42 1.42zM4.84 17.24l-1.79 1.8 1.41 1.41 1.8-1.79-1.42-1.42zM12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
        <svg id="moon" class="h-4 w-4 hidden" viewBox="0 0 24 24" fill="currentColor"><path d="M21.64 13a9 9 0 11-10.63-10.6 1 1 0 00.9 1.45A7 7 0 1020.2 12.1a1 1 0 001.44.9z"/></svg>
      </button>

      {{-- Mobile menu button --}}
      <button id="menuBtn" class="md:hidden ml-2 inline-flex items-center justify-center h-9 w-9 rounded-lg border border-white/15 text-white/90 hover:bg-white/10">
        <svg class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor" fill="none"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
    </div>

    {{-- Mobile menu --}}
    <div id="mobileMenu" class="md:hidden hidden pb-4">
      <nav class="flex flex-col gap-2">
        <a href="{{ route('home') }}" class="text-white/90 hover:text-white text-sm py-2">Início</a>
        <a href="{{ route('faqs.index') }}" class="text-white/90 hover:text-white text-sm py-2">Dúvidas Frequentes</a>
      </nav>
    </div>
  </div>
</header>
