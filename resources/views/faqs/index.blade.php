<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>FAQs</title>
</head>
<body>
  <h1>Dúvidas Frequentes</h1>

  <form method="GET" action="{{ route('faqs.index') }}">
    <input type="search" name="q" value="{{ $search ?? '' }}" placeholder="Pesquisar...">
    <button type="submit">Buscar</button>
  </form>

  <ul>
    @forelse($faqs as $faq)
      <li>
        <a href="{{ route('faqs.show', $faq) }}">{{ $faq->question }}</a>
      </li>
    @empty
      <li>Nenhuma dúvida encontrada.</li>
    @endforelse
  </ul>

  @if(method_exists($faqs,'hasPages') && $faqs->hasPages())
    <div>{{ $faqs->onEachSide(1)->links() }}</div>
  @endif

  <p><a href="{{ route('home') }}">← Voltar</a></p>
</body>
</html>
