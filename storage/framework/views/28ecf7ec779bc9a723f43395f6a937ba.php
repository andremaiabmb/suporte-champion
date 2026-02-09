

<?php $__env->startSection('title', 'Editar Evento • Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

  <div>
    <h1 class="text-xl font-semibold">Editar evento</h1>
    <p class="text-sm text-slate-600 dark:text-slate-300">Atualize as informações e a capa do evento.</p>
  </div>

  
  <?php if($errors->any()): ?>
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:bg-red-900/20 dark:border-red-900/30 dark:text-red-200">
      <ul class="list-disc ml-4 text-sm">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if(session('status')): ?>
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-900/30 dark:text-emerald-200">
      <?php echo e(session('status')); ?>

    </div>
  <?php endif; ?>

  <form action="<?php echo e(route('admin.events.update', $event)); ?>" method="POST" enctype="multipart/form-data"
        class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:bg-slate-900 dark:border-white/10">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    
    <div>
      <label class="block text-sm font-medium mb-1" for="title">Título</label>
      <input type="text" name="title" id="title"
             class="w-full rounded-xl border-slate-300 dark:border-white/10 dark:bg-slate-800 dark:text-slate-100"
             value="<?php echo e(old('title', $event->title)); ?>" required>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1" for="starts_at">Início</label>
        <input type="datetime-local" name="starts_at" id="starts_at"
               class="w-full rounded-xl border-slate-300 dark:border-white/10 dark:bg-slate-800 dark:text-slate-100"
               value="<?php echo e(old('starts_at', optional($event->starts_at)->format('Y-m-d\TH:i'))); ?>" required>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1" for="ends_at">Fim</label>
        <input type="datetime-local" name="ends_at" id="ends_at"
               class="w-full rounded-xl border-slate-300 dark:border-white/10 dark:bg-slate-800 dark:text-slate-100"
               value="<?php echo e(old('ends_at', optional($event->ends_at)->format('Y-m-d\TH:i'))); ?>" required>
      </div>
    </div>

    
    <div>
      <label class="block text-sm font-medium mb-1" for="location">Local</label>
      <input type="text" name="location" id="location"
             class="w-full rounded-xl border-slate-300 dark:border-white/10 dark:bg-slate-800 dark:text-slate-100"
             value="<?php echo e(old('location', $event->location)); ?>" placeholder="Opcional">
    </div>

    
    <div class="space-y-3">
      <label class="block text-sm font-medium">Capa do evento</label>

      
      <div class="flex items-start gap-4">
        <div class="h-28 w-48 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 ring-1 ring-slate-900/10 dark:ring-white/10">
          <?php
            $coverUrl = $event->cover_path ? asset('storage/'.$event->cover_path) : null;
          ?>
          <img id="coverPreview" src="<?php echo e($coverUrl); ?>" alt="" class="h-full w-full object-cover <?php echo e($coverUrl ? '' : 'hidden'); ?>">
          <div id="coverPlaceholder" class="h-full w-full flex items-center justify-center text-xs text-slate-500 dark:text-slate-400 <?php echo e($coverUrl ? 'hidden' : ''); ?>">
            Sem imagem
          </div>
        </div>

        <div class="space-y-2">
          <input type="file" name="cover" id="cover"
                 accept="image/*"
                 class="block w-full text-sm file:mr-3 file:rounded-lg file:border file:border-slate-300 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-slate-50 dark:file:bg-slate-800 dark:file:border-white/10">

          <?php if($coverUrl): ?>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Capa atual: <span class="underline"><?php echo e(basename($event->cover_path)); ?></span>
            </p>
          <?php endif; ?>
          
          <?php if($coverUrl): ?>
            <label class="inline-flex items-center gap-2 text-sm">
              <input type="checkbox" name="remove_cover" value="1"
                     class="rounded border-slate-300 dark:border-white/10 dark:bg-slate-800">
              Remover capa atual
            </label>
          <?php endif; ?>
        </div>
      </div>

      <p class="text-xs text-slate-500 dark:text-slate-400">Formatos aceitos: JPG, PNG, WEBP. Tamanho máximo recomendado: 2MB.</p>
    </div>

    
    <div class="flex items-center justify-end gap-2">
      <a href="<?php echo e(route('admin.events.index')); ?>"
         class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium hover:bg-slate-50 dark:border-white/10 dark:hover:bg-slate-800">
        Voltar
      </a>
      <button type="submit"
              class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 shadow">
        Salvar alterações
      </button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  // Preview instantâneo da capa escolhida
  (function () {
    const input = document.getElementById('cover');
    const img = document.getElementById('coverPreview');
    const ph = document.getElementById('coverPlaceholder');

    if (!input || !img || !ph) return;

    input.addEventListener('change', (e) => {
      const [file] = e.target.files || [];
      if (!file) {
        img.classList.add('hidden');
        ph.classList.remove('hidden');
        img.removeAttribute('src');
        return;
      }
      const url = URL.createObjectURL(file);
      img.src = url;
      img.classList.remove('hidden');
      ph.classList.add('hidden');
    });
  })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/dashboard/admin/events/edit.blade.php ENDPATH**/ ?>