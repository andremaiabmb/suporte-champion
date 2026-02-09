<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Meu QR • {{ config('app.name') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet">
  <style>
    :root { color-scheme: light dark; }
    html,body { height:100%; margin:0; font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial; }
    .wrap { min-height:100%; display:flex; align-items:center; justify-content:center; background:#f6f7fb; }
    .card { background:white; padding:24px; border-radius:20px; box-shadow: 0 10px 30px rgba(0,0,0,.08); text-align:center; }
    .title { font-weight:700; margin-top:8px; }
    .code { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; color:#64748b; font-size:14px; margin-top:6px;}
    .hint { font-size:12px; margin-top:10px; color:#64748b; }
    @media (prefers-color-scheme: dark){
      .wrap{ background:#0b1220; }
      .card{ background:#0f172a; color:#e2e8f0; }
      .code, .hint{ color:#94a3b8; }
    }
  </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <canvas id="qr" width="320" height="320"></canvas>
    <div class="title">{{ $user->name }}</div>
    <div class="code">{{ $qrCode }}</div>
    <div class="hint">Mostre este QR no credenciamento.</div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
  (function(){
    const code = @json($qrCode);
    const canvas = document.getElementById('qr');
    QRCode.toCanvas(canvas, code, { width: 320, margin: 1 }, function(err){ if (err) console.error(err); });
  })();
</script>
</body>
</html>
