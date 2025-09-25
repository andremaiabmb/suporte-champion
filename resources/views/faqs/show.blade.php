<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>{{ $faq->question }}</title>
</head>
<body>
  <p><a href="{{ route('faqs.index') }}">← Voltar às dúvidas</a></p>

  <h1>{{ $faq->question }}</h1>
  <div>{!! nl2br(e($faq->answer ?? '')) !!}</div>

  @if(!empty($faq->published_at))
    <small>Publicado em {{ \Illuminate\Support\Carbon::parse($faq->published_at)->format('d/m/Y H:i') }}</small>
  @endif
</body>
</html>
