{{-- Inicialização do Lucide (JS via CDN) --}}
<script type="module">
  import lucide from "https://unpkg.com/lucide@latest/dist/esm/lucide.js";
  // Renderiza todos os <i data-lucide="...">
  lucide.createIcons();

  // Re-render opcional quando trocar de tema ou conteúdo dinâmico:
  document.addEventListener('lucide:refresh', () => lucide.createIcons());
</script>
