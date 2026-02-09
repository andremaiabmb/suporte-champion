

<?php $__env->startSection('title', 'Eventos • Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

  
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold">Gerir Eventos</h1>
      <p class="text-sm text-slate-600 dark:text-slate-300">Crie, edite e acompanhe as inscrições dos eventos.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="<?php echo e(route('admin.events.create')); ?>"
         class="inline-flex items-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 shadow">
        + Novo Evento
      </a>
    </div>
  </div>

  
  <?php if(session('status')): ?>
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-900/30 dark:text-emerald-200">
      <?php echo e(session('status')); ?>

    </div>
  <?php endif; ?>

  
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:bg-slate-900 dark:border-white/10">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500 dark:text-slate-400">
          <th class="py-3 pl-4 pr-3 w-20">Capa</th>
          <th class="py-3 px-3">Título</th>
          <th class="py-3 px-3">Período</th>
          <th class="py-3 px-3">Local</th>
          <th class="py-3 px-3 text-center">Inscrições</th>
          <th class="py-3 pr-4 pl-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="align-top">
        <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $thumb = $event->cover_path ? asset('storage/'.$event->cover_path) : null;
            $count = $counts[$event->id] ?? 0;
          ?>
          <tr class="border-t border-slate-100 dark:border-white/10">
            <td class="py-2 pl-4 pr-3">
              <div class="h-12 w-20 rounded-md bg-slate-100 dark:bg-slate-800 overflow-hidden">
                <?php if($thumb): ?>
                  <img src="<?php echo e($thumb); ?>" alt="" class="h-full w-full object-cover">
                <?php endif; ?>
              </div>
            </td>
            <td class="py-2 px-3">
              <div class="font-medium"><?php echo e($event->title); ?></div>
              <div class="text-xs text-slate-500 dark:text-slate-400">
                ID #<?php echo e($event->id); ?>

              </div>
            </td>
            <td class="py-2 px-3">
              <div>
                <?php echo e(optional($event->starts_at)->format('d/m/Y H:i') ?? '—'); ?>

                <?php if($event->ends_at): ?>
                  — <?php echo e(optional($event->ends_at)->format('d/m/Y H:i')); ?>

                <?php endif; ?>
              </div>
            </td>
            <td class="py-2 px-3">
              <?php echo e($event->location ?: '—'); ?>

            </td>
            <td class="py-2 px-3 text-center">
              <span class="inline-flex items-center justify-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                <?php echo e($count); ?>

              </span>
            </td>
            <td class="py-2 pr-4 pl-3">
              <div class="flex items-center justify-end gap-2">
                <a href="<?php echo e(route('admin.events.show', $event)); ?>"
                   class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium hover:bg-slate-50 dark:border-white/10 dark:hover:bg-slate-800">
                  Ver
                </a>
                <a href="<?php echo e(route('admin.events.edit', $event)); ?>"
                   class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium hover:bg-slate-50 dark:border-white/10 dark:hover:bg-slate-800">
                  Editar
                </a>
                <form action="<?php echo e(route('admin.events.destroy', $event)); ?>" method="POST"
                      onsubmit="return confirm('Tem certeza que deseja remover este evento?');">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button type="submit"
                          class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50 dark:border-red-900/30 dark:text-red-300 dark:hover:bg-red-900/20">
                    Remover
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="6" class="py-10 text-center text-slate-500 dark:text-slate-400">
              Nenhum evento cadastrado ainda.
              <a href="<?php echo e(route('admin.events.create')); ?>" class="text-brand-600 hover:text-brand-700 font-medium">
                Crie o primeiro evento
              </a>.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <div>
    <?php echo e($events->links()); ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/dashboard/admin/events/index.blade.php ENDPATH**/ ?>