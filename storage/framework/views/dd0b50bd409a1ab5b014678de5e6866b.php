

<?php $__env->startSection('title', 'Admin • FAQs'); ?>

<?php $__env->startSection('hero'); ?>
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">FAQs</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">Gerencie perguntas frequentes com passos ilustrados.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="<?php echo e(route('admin.faqs.create')); ?>"
         class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">
        Novo FAQ
      </a>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  <form method="GET" action="<?php echo e(route('admin.faqs.index')); ?>" class="flex flex-wrap items-end gap-3">
    <div>
      <label class="block text-xs text-slate-500">Buscar</label>
      <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Pergunta, resposta, slug..."
             class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
    </div>
    <div>
      <label class="block text-xs text-slate-500">Status</label>
      <select name="status" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:bg-slate-900 dark:border-white/10">
        <option value="all" <?php echo e($status==='all'?'selected':''); ?>>Todos</option>
        <option value="published" <?php echo e($status==='published'?'selected':''); ?>>Publicados</option>
        <option value="draft" <?php echo e($status==='draft'?'selected':''); ?>>Rascunhos</option>
      </select>
    </div>
    <div>
      <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold dark:bg-slate-200 dark:text-slate-900">Filtrar</button>
    </div>
  </form>

  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-white/10">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500 dark:text-slate-400">
          <th class="py-3 pl-4 pr-3">Pergunta</th>
          <th class="py-3 pr-3">Slug</th>
          <th class="py-3 pr-3">Status</th>
          <th class="py-3 pr-4 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="border-t border-slate-100 dark:border-white/10">
          <td class="py-3 pl-4 pr-3"><?php echo e($faq->question); ?></td>
          <td class="py-3 pr-3 text-slate-500"><?php echo e($faq->slug); ?></td>
          <td class="py-3 pr-3">
            <span class="inline-flex rounded-full px-2 py-0.5 text-xs <?php echo e($faq->isPublished() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300'); ?>">
              <?php echo e($faq->isPublished() ? 'Publicado' : 'Rascunho'); ?>

            </span>
          </td>
          <td class="py-3 pr-4 text-right">
            <a href="<?php echo e(route('admin.faqs.show', $faq->id)); ?>" class="text-sky-700 hover:underline dark:text-sky-400">ver</a>
            <span class="px-1">·</span>
            <a href="<?php echo e(route('admin.faqs.edit', $faq->id)); ?>" class="text-slate-700 hover:underline dark:text-slate-200">editar</a>
            <span class="px-1">·</span>
            <form action="<?php echo e(route('admin.faqs.destroy', $faq->id)); ?>" method="POST" class="inline"
                  onsubmit="return confirm('Remover este FAQ? Esta ação é irreversível.');">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button class="text-red-600 hover:underline">remover</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
          <td colspan="4" class="py-10 text-center text-slate-400">Nenhum FAQ encontrado.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($faqs->hasPages()): ?>
    <div><?php echo e($faqs->links()); ?></div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/dashboard/faqs/index.blade.php ENDPATH**/ ?>