

<?php $__env->startSection('title', 'Criar Evento'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:bg-slate-900 dark:border-white/10">
    <h1 class="text-xl font-semibold">Criar evento</h1>

    <form action="<?php echo e(route('admin.events.store')); ?>" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
      <?php echo csrf_field(); ?>

      <div>
        <label class="block text-sm font-medium mb-1">Título</label>
        <input type="text" name="title" value="<?php echo e(old('title')); ?>"
               class="w-full rounded-xl border px-3 py-2 bg-white dark:bg-slate-900 border-slate-300 dark:border-white/10">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Início</label>
          <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at')); ?>"
                 class="w-full rounded-xl border px-3 py-2 bg-white dark:bg-slate-900 border-slate-300 dark:border-white/10">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Fim</label>
          <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at')); ?>"
                 class="w-full rounded-xl border px-3 py-2 bg-white dark:bg-slate-900 border-slate-300 dark:border-white/10">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Local (opcional)</label>
        <input type="text" name="location" value="<?php echo e(old('location')); ?>"
               class="w-full rounded-xl border px-3 py-2 bg-white dark:bg-slate-900 border-slate-300 dark:border-white/10">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Capa (opcional)</label>
        <input type="file" name="cover" accept="image/*"
               class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-2 file:text-white hover:file:bg-brand-700">
        <p class="text-xs text-slate-500">Formatos comuns (JPG/PNG). Máx. 4MB.</p>
      </div>

      <div class="pt-2 flex items-center justify-end gap-2">
        <a href="<?php echo e(route('admin.events.index')); ?>"
           class="rounded-xl px-4 py-2 text-sm font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700">
          Cancelar
        </a>
        <button type="submit"
                class="rounded-xl px-4 py-2 text-sm font-semibold bg-brand-600 text-white hover:bg-brand-700">
          Criar
        </button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/dashboard/admin/events/create.blade.php ENDPATH**/ ?>