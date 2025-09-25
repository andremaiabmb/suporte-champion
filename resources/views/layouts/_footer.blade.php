<footer class="mt-16 border-t border-slate-200 dark:border-white/10 bg-white/70 dark:bg-slate-900/70 backdrop-blur">
  <div class="container py-8 text-sm text-slate-600 dark:text-slate-300 flex flex-col sm:flex-row items-center justify-between gap-3">
    <p>© {{ date('Y') }} Suporte ao Aluno. Todos os direitos reservados.</p>
    <div class="flex items-center gap-4">
      <a href="{{ route('faqs.index') }}" class="hover:underline">FAQ</a>
      <a href="{{ route('home') }}#contato" class="hover:underline">Contato</a>
    </div>
  </div>
</footer>
