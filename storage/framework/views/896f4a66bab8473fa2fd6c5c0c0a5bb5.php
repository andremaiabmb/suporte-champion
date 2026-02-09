

<?php $__env->startSection('title', 'Perfil'); ?>

<?php $__env->startSection('hero'); ?>
<div class="mt-6 rounded-3xl border border-slate-200 dark:border-white/10 bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-800 p-6 md:p-8">
  <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Informações do Perfil</h1>
  <p class="mt-2 text-slate-600 dark:text-slate-300">Atualize seus dados e acesse seu QR para presença.</p>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

  
  <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
    <div class="font-medium">Informações do Perfil</div>
    <div class="mt-4 space-y-3 text-sm">
      <div><span class="text-slate-500">Nome:</span> <span class="font-medium"><?php echo e($user->name); ?></span></div>
      <div><span class="text-slate-500">Email:</span> <span class="font-medium"><?php echo e($user->email); ?></span></div>
      <div><span class="text-slate-500">ID:</span> <span class="font-medium">#<?php echo e($user->id); ?></span></div>
    </div>
  </div>

  
  <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
    <div class="flex items-center justify-between">
      <div class="font-medium">Meu QR Code</div>
      <div class="text-xs text-slate-500">Código: <span class="font-mono"><?php echo e($qrCode); ?></span></div>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-slate-800 p-4 flex items-center justify-center">
        <canvas id="qrCanvas" class="w-40 h-40"></canvas>
      </div>

      <div class="rounded-xl border border-slate-200 dark:border-white/10 p-4">
        <div class="text-sm text-slate-600 dark:text-slate-300">
          Guarde este QR. Ele será usado para confirmar sua presença nos eventos.
        </div>
        <div class="mt-3 flex items-center gap-2">
          <button id="qrDownload" class="rounded-lg border border-slate-200 dark:border-white/10 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
            Baixar QR Code
          </button>
          <a href="<?php echo e(route('profile.qrpage')); ?>"
             class="rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 text-sm">
            Abrir página do meu QR
          </a>
        </div>
        <div class="mt-3 text-xs text-indigo-700/90 dark:text-indigo-300 bg-indigo-50/70 dark:bg-indigo-900/20 border border-indigo-200/60 dark:border-indigo-800/40 rounded-lg p-3">
          <span class="font-semibold">Dica:</span> você pode imprimir este QR ou manter salvo no seu celular.
        </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const code = <?php echo json_encode($qrCode, 15, 512) ?>;
    const canvas = document.getElementById('qrCanvas');
    function render(){
      if (!window.QRCode) { return setTimeout(render, 50); }
      QRCode.toCanvas(canvas, code, { width: 220, margin: 2 }, function (error) { if (error) console.error(error); });
    }
    render();

    document.getElementById('qrDownload').addEventListener('click', function(){
      try {
        const url = canvas.toDataURL('image/png');
        const a = document.createElement('a');
        a.href = url; a.download = 'meu_qrcode.png';
        document.body.appendChild(a); a.click(); a.remove();
      } catch(e){}
    });
  });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/profile/show.blade.php ENDPATH**/ ?>