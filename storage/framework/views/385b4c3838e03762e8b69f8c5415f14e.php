
<?php $__env->startSection('title', "Chamado {$ticket->code}"); ?>

<?php $__env->startSection('content'); ?>
<div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
  <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div>
      <h1 class="text-xl font-semibold"><?php echo e($ticket->subject); ?></h1>
      <p class="text-sm text-slate-500">
        Código: <span class="font-mono"><?php echo e($ticket->code); ?></span> ·
        Setor: <?php echo e($ticket->sector->name ?? '—'); ?> ·
        Autor: <?php echo e($ticket->author->name ?? '—'); ?> ·
        Responsável: <?php echo e($ticket->assignee->name ?? '—'); ?>

      </p>
    </div>
    <form method="POST" action="<?php echo e(route('admin.supports.status', $ticket)); ?>" class="flex items-center gap-2">
      <?php echo csrf_field(); ?>
      <select name="status" class="rounded-xl border-slate-200 dark:border-white/10">
        <?php $__currentLoopData = ['open','in_progress','waiting','resolved','closed','reopened']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($st); ?>" <?php if($ticket->status===$st): echo 'selected'; endif; ?>><?php echo e(strtoupper($st)); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <button class="rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10">Atualizar</button>
    </form>
  </div>

  <div class="mt-6 grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
      <h2 class="font-medium mb-3">Mensagens</h2>
      <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $ticket->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="rounded-xl border border-slate-200 dark:border-white/10 p-3 <?php if($m->is_internal): ?> bg-amber-50/50 dark:bg-amber-900/10 <?php endif; ?>">
            <div class="text-xs text-slate-500 mb-1">
              <?php echo e($m->author->name ?? '—'); ?> • <?php echo e($m->created_at?->format('d/m/Y H:i')); ?>

              <?php if($m->is_internal): ?> <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] bg-amber-200/80 text-amber-900">INTERNAL</span> <?php endif; ?>
            </div>
            <div class="text-sm whitespace-pre-line"><?php echo e($m->message); ?></div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="text-sm text-slate-500">Sem mensagens ainda.</div>
        <?php endif; ?>
      </div>

      <form method="POST" action="<?php echo e(route('admin.supports.note', $ticket)); ?>" class="mt-4 space-y-2">
        <?php echo csrf_field(); ?>
        <textarea name="message" rows="3" class="w-full rounded-xl border-slate-200 dark:border-white/10" placeholder="Adicionar mensagem..."></textarea>
        <label class="text-sm inline-flex items-center gap-2">
          <input type="checkbox" name="is_internal" value="1"> Nota interna (oculta do aluno)
        </label>
        <div><button class="rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10">Enviar</button></div>
      </form>
    </div>

    <aside>
      <h2 class="font-medium mb-3">Ações</h2>
      <form method="POST" action="<?php echo e(route('admin.supports.assign', $ticket)); ?>" class="mb-2">
        <?php echo csrf_field(); ?>
        <button class="w-full rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10">Assumir</button>
      </form>

      <form method="POST" action="<?php echo e(route('admin.supports.reassign', $ticket)); ?>" class="space-y-2">
        <?php echo csrf_field(); ?>
        <input name="assignee_id" type="number" class="w-full rounded-xl border-slate-200 dark:border-white/10" placeholder="ID do responsável">
        <button class="w-full rounded-xl px-3 py-2 text-sm border border-slate-200 dark:border-white/10">Reatribuir</button>
      </form>

      <h2 class="font-medium mt-6 mb-3">Histórico</h2>
      <div class="space-y-2 text-sm">
        <?php $__empty_1 = true; $__currentLoopData = $ticket->transitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="rounded-xl border border-slate-200 dark:border-white/10 p-2">
            <?php echo e($t->created_at?->format('d/m/Y H:i')); ?> —
            <strong><?php echo e($t->field); ?></strong>:
            "<?php echo e($t->old_value ?? '—'); ?>" → "<?php echo e($t->new_value ?? '—'); ?>"
            <?php if($t->user): ?> • por <?php echo e($t->user->name); ?> <?php endif; ?>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="text-slate-500">Sem histórico.</div>
        <?php endif; ?>
      </div>
    </aside>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/dashboard/support/show.blade.php ENDPATH**/ ?>