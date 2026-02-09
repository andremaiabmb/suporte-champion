@extends('layouts.app')

@section('title', 'Presenças • '.($event->title ?? 'Evento'))

@section('hero')
@php
  $today = \Illuminate\Support\Carbon::parse($date);
  $prev  = $today->copy()->subDay()->toDateString();
  $next  = $today->copy()->addDay()->toDateString();
@endphp
<div class="mt-6 rounded-3xl bg-gradient-to-br from-sky-50 via-white to-indigo-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 border border-slate-200/70 dark:border-white/10 p-6 md:p-10">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">
        Presenças — {{ $event->title ?? 'Evento' }}
      </h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">
        Por dia. Use Eyoyo (leitor), câmera do dispositivo ou entrada manual.
      </p>
    </div>

    {{-- Seletor de data --}}
    <form method="GET" action="{{ route('admin.events.attendance', $event->id) }}" class="flex items-center gap-2">
      <a href="{{ route('admin.events.attendance', [$event->id, 'date'=>$prev]) }}"
         class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-slate-800" title="Dia anterior">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      </a>

      <input type="date" name="date" value="{{ $date }}"
             class="rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-sm">
      <button class="rounded-lg border border-slate-200 dark:border-white/10 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
        Ir
      </button>

      <a href="{{ route('admin.events.attendance', [$event->id, 'date'=>$next]) }}"
         class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-slate-800" title="Próximo dia">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
      </a>
    </form>
  </div>
</div>
@endsection

@section('content')
<div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
  {{-- Painel principal --}}
  <section class="xl:col-span-2 rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-6">

    {{-- Métricas do dia --}}
    <div class="grid grid-cols-3 gap-3">
      <div class="rounded-xl border border-slate-200 dark:border-white/10 p-4">
        <div class="text-xs text-slate-500">Inscritos</div>
        <div class="mt-1 text-2xl font-semibold">{{ $metrics['total_regs'] ?? 0 }}</div>
      </div>
      <div class="rounded-xl border border-slate-200 dark:border-white/10 p-4">
        <div class="text-xs text-slate-500">Check-ins ({{ $date }})</div>
        <div class="mt-1 text-2xl font-semibold text-emerald-600">{{ $metrics['checkins'] ?? 0 }}</div>
      </div>
      <div class="rounded-xl border border-slate-200 dark:border-white/10 p-4">
        <div class="text-xs text-slate-500">Check-outs ({{ $date }})</div>
        <div class="mt-1 text-2xl font-semibold text-sky-600">{{ $metrics['checkouts'] ?? 0 }}</div>
      </div>
    </div>

    {{-- Tabs modo --}}
    <div class="mt-6 flex items-center gap-2">
      <button data-mode="checkin" class="mode-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm bg-emerald-600 text-white">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
        Check-in do dia
      </button>
      <button data-mode="checkout" class="mode-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-slate-800">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
        Check-out do dia
      </button>
      <div class="ml-auto text-xs text-slate-500">Data selecionada: <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</span></div>
    </div>

    {{-- Eyoyo (teclado) --}}
    <div class="mt-6 rounded-xl border border-slate-200 dark:border-white/10 p-4">
      <div class="flex items-center justify-between gap-2">
        <div class="font-medium">Leitor (Eyoyo)</div>
        <div class="text-xs text-slate-500">Aguardando leitura… (pressione <kbd>Enter</kbd> no fim)</div>
      </div>
      <input id="hiddenEyoyo" class="sr-only" autocomplete="off" />
      <div class="mt-3 text-xs text-slate-500">Dia: {{ $date }}</div>
      <div id="eyoyo-last" class="mt-1 text-sm text-slate-500">—</div>
    </div>

    {{-- Câmera (QR) --}}
    <div class="mt-6 rounded-xl border border-slate-200 dark:border-white/10 p-4">
      <div class="flex items-center justify-between gap-2">
        <div class="font-medium">Câmera (QR)</div>
        <button id="camToggle" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 dark:border-white/10 px-3 py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
          Iniciar câmera
        </button>
      </div>
      <div id="camWrap" class="mt-3 hidden">
        <video id="camVideo" class="w-full rounded-lg bg-black/60" playsinline></video>
        <canvas id="camCanvas" class="hidden"></canvas>
        <div id="camInfo" class="mt-2 text-xs text-slate-500">Lendo QR…</div>
      </div>
      <div id="camSupport" class="mt-2 text-xs text-slate-500"></div>
    </div>

    {{-- Entrada Manual --}}
    <div class="mt-6 rounded-xl border border-slate-200 dark:border-white/10 p-4">
      <div class="font-medium">Entrada manual (QR/URL do QR ou e-mail do aluno)</div>
      <form id="manualForm" class="mt-3 flex items-center gap-2">
        <input id="manualPayload" type="text" placeholder="Cole o código/URL do QR do aluno, ou digite o e-mail"
               class="flex-1 rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2 text-sm" />
        <button class="rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm px-3 py-2">Confirmar</button>
      </form>
      <div class="mt-2 text-xs text-slate-500">Para buscar por <b>nome</b>, posso habilitar — basta pedir.</div>
    </div>

    {{-- Resultado --}}
    <div id="resultBox" class="mt-6 rounded-2xl border border-slate-200 dark:border-white/10 p-6 bg-slate-50 dark:bg-white/5 hidden">
      <div class="flex items-center gap-3">
        <div id="resultFlag" class="text-3xl">🏳️</div>
        <div>
          <div id="resultTitle" class="text-lg font-semibold">—</div>
          <div id="resultMsg" class="text-slate-600 dark:text-slate-300">—</div>
        </div>
      </div>
      <div id="resultMeta" class="mt-3 text-xs text-slate-500">—</div>
    </div>
  </section>

  {{-- Histórico (persiste do banco + acrescenta novas leituras) --}}
  <aside class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-6">
    <div class="font-medium">Últimas leituras ({{ $date }})</div>
    <div id="history"
         class="mt-3 space-y-2 text-sm max-h-[70vh] overflow-y-auto pr-1"
         style="scrollbar-gutter: stable;">
      @forelse($history ?? [] as $h)
        <div class="flex items-center justify-between rounded-lg border border-slate-200 dark:border-white/10 px-3 py-2">
          <div class="flex items-center gap-2">
            <span class="text-lg">{{ $h['action'] === 'checkout' ? '🚪' : '✅' }}</span>
            <span class="font-medium">{{ $h['name'] }}</span>
          </div>
          <div class="text-xs text-slate-500">
            {{ $h['action'] }} • {{ \Illuminate\Support\Carbon::parse($h['timestamp'])->format('d/m/Y H:i:s') }}
          </div>
        </div>
      @empty
        <div class="text-slate-500">Sem leituras ainda.</div>
      @endforelse
    </div>
  </aside>
</div>

@push('scripts')
<script>
(function(){
  const dateStr = @json($date);
  const scanUrl = @json(route('admin.events.attendance.scan', $event->id));
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  let mode = 'checkin';

  // Troca de modo
  const modeBtns = document.querySelectorAll('.mode-btn');
  function setMode(m){
    mode = m;
    modeBtns.forEach(btn => {
      if (btn.dataset.mode === m){
        btn.className = 'mode-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm bg-emerald-600 text-white';
      } else {
        btn.className = 'mode-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-slate-800';
      }
    });
  }
  modeBtns.forEach(b => b.addEventListener('click', () => setMode(b.dataset.mode)));

  // 1) Eyoyo (teclado)
  const eyoyoInfo = document.getElementById('eyoyo-last');
  let buffer = '', timer = null;
  const TIMEOUT = 300;
  function resetBuffer(){ buffer=''; if (timer){ clearTimeout(timer); timer=null; } }
  function handleBufferDone(){
    const code = buffer.trim();
    resetBuffer();
    if (!code) return;
    eyoyoInfo.textContent = 'Lido: ' + code;
    sendScan({ payload: code, method: 'eyoyo' });
  }
  window.addEventListener('keydown', (e) => {
    const tag = (e.target && e.target.tagName) ? e.target.tagName.toUpperCase() : '';
    if (tag === 'INPUT' || tag === 'TEXTAREA' || e.metaKey || e.ctrlKey || e.altKey) return;
    if (e.key === 'Enter'){ e.preventDefault(); handleBufferDone(); return; }
    if (e.key.length === 1){ buffer += e.key; if (timer) clearTimeout(timer); timer = setTimeout(handleBufferDone, TIMEOUT); }
  });

  // 2) Câmera (BarcodeDetector)
  const camToggle = document.getElementById('camToggle');
  const camWrap   = document.getElementById('camWrap');
  const camInfo   = document.getElementById('camInfo');
  const video     = document.getElementById('camVideo');
  const canvas    = document.getElementById('camCanvas');
  const supportEl = document.getElementById('camSupport');
  let stream = null, running = false, detector = null;

  async function initDetector(){
    if ('BarcodeDetector' in window) {
      const supported = await window.BarcodeDetector.getSupportedFormats();
      if (supported.includes('qr_code')) {
        detector = new window.BarcodeDetector({ formats: ['qr_code'] });
        supportEl.textContent = 'Leitura por QR habilitada.';
        return true;
      }
    }
    supportEl.textContent = 'Navegador sem BarcodeDetector. Use Eyoyo ou entrada manual.';
    return false;
  }

  async function startCamera(){
    if (running) return;
    if (!await initDetector()) return;
    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }});
      video.srcObject = stream;
      await video.play();
      camWrap.classList.remove('hidden');
      running = true;
      requestAnimationFrame(scanLoop);
      camToggle.textContent = 'Parar câmera';
    } catch (err){
      supportEl.textContent = 'Erro ao acessar câmera. Permita o acesso ou use Eyoyo.';
    }
  }
  function stopCamera(){
    running = false;
    camToggle.textContent = 'Iniciar câmera';
    if (stream){ stream.getTracks().forEach(t => t.stop()); stream = null; }
    camWrap.classList.add('hidden');
  }
  async function scanLoop(){
    if (!running) return;
    try {
      const w = video.videoWidth, h = video.videoHeight;
      if (w && h){
        canvas.width = w; canvas.height = h;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, w, h);
        const bitmap = await createImageBitmap(canvas);
        const barcodes = await detector.detect(bitmap);
        if (barcodes && barcodes.length){
          const code = (barcodes[0].rawValue || '').trim();
          if (code){
            camInfo.textContent = 'Lido: ' + code;
            stopCamera();
            sendScan({ payload: code, method: 'camera' });
            return;
          }
        }
      }
    } catch (e) {}
    requestAnimationFrame(scanLoop);
  }
  camToggle.addEventListener('click', () => running ? stopCamera() : startCamera());

  // 3) Manual
  const manualForm = document.getElementById('manualForm');
  manualForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const raw = (document.getElementById('manualPayload').value || '').trim();
    if (!raw) return;
    sendScan({ payload: raw, method: 'manual' });
    document.getElementById('manualPayload').value = '';
  });

  // Resultado / histórico
  const resultBox  = document.getElementById('resultBox');
  const resultFlag = document.getElementById('resultFlag');
  const resultTit  = document.getElementById('resultTitle');
  const resultMsg  = document.getElementById('resultMsg');
  const resultMeta = document.getElementById('resultMeta');
  const history    = document.getElementById('history');

  function appendHistory(entry){
    const row = document.createElement('div');
    row.className = 'flex items-center justify-between rounded-lg border border-slate-200 dark:border-white/10 px-3 py-2';
    row.innerHTML = `
      <div class="flex items-center gap-2">
        <span class="text-lg">${entry.icon}</span>
        <span class="font-medium">${entry.name}</span>
      </div>
      <div class="text-xs text-slate-500">${entry.action} • ${entry.time}</div>
    `;
    if (history.firstElementChild && history.firstElementChild.classList.contains('text-slate-500')){
      history.innerHTML = '';
    }
    history.prepend(row);
  }

  function showSuccess(data){
    const actionLabel = (data.action === 'checkout') ? 'Check-out confirmado' : 'Check-in confirmado';

    if (data.flag) {
      resultFlag.innerHTML = `<img src="${data.flag}" alt="flag" class="w-7 h-7 rounded-sm ring-1 ring-black/5">`;
    } else {
      resultFlag.textContent = '🏳️';
    }

    resultTit.textContent  = data.user?.name || '—';
    resultMsg.textContent  = `${data.greet} ${actionLabel} (dia ${data.date}).`;
    resultMeta.textContent = `Modo: ${data.action} • ${data.now} • E-mail: ${data.user?.email || '—'}`;
    resultBox.classList.remove('hidden');

    appendHistory({
      icon: (data.action === 'checkout') ? '🚪' : '✅',
      name: data.user?.name || '—',
      action: data.action,
      time: data.now
    });
  }

  function showErrorCode(code){
    const map = {
      EMPTY_PAYLOAD: 'Leitura vazia. Tente novamente.',
      USER_NOT_FOUND: 'QR/URL/e-mail não encontrado.',
      EVENT_NOT_FOUND: 'Evento inexistente.',
      NOT_REGISTERED: 'Usuário não está inscrito neste evento.',
      CHECKIN_REQUIRED: 'Faça o check-in antes do check-out.',
      ALREADY_CHECKED_IN: 'Este participante já realizou check-in hoje.',
      ALREADY_CHECKED_OUT: 'Este participante já realizou check-out hoje.',
      ALREADY_COMPLETED: 'Check-in e check-out do dia já foram realizados.',
      NETWORK: 'Falha de rede.',
      UNKNOWN: 'Erro ao processar leitura.'
    };
    showError(map[code] || map.UNKNOWN);
  }

  function showError(message){
    resultFlag.textContent = '⚠️';
    resultTit.textContent  = 'Não permitido';
    resultMsg.textContent  = message || 'Operação não permitida.';
    resultMeta.textContent = '';
    resultBox.classList.remove('hidden');
  }

  async function sendScan({ payload, method }){
    // feedback imediato
    resultFlag.textContent = '…';
    resultTit.textContent  = 'Processando leitura…';
    resultMsg.textContent  = '';
    resultMeta.textContent = '';
    resultBox.classList.remove('hidden');

    try {
      const res = await fetch(scanUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          payload,
          mode,
          date: dateStr,
          method: method || 'eyoyo'
        })
      });

      const data = await res.json();
      if (res.ok && data && data.ok) {
        showSuccess(data);
      } else {
        showErrorCode(data?.error || 'UNKNOWN');
      }
    } catch (err){
      showErrorCode('NETWORK');
    }
  }
})();
</script>
@endpush
@endsection
