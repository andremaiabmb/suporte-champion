

<?php $__env->startSection('title','Suporte • Moderação'); ?>

<?php $__env->startSection('hero'); ?>
<div class="mt-6 rounded-3xl shadow-soft bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-8">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Moderação de Chamados</h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">Filtre, assuma, reatribua e mude status.</p>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
  // Mapas para rótulos/cores de status e prioridade
  $statusLabel = [
    'open'        => 'Aberto',
    'in_progress' => 'Em andamento',
    'waiting'     => 'Aguardando',
    'resolved'    => 'Resolvido',
    'closed'      => 'Fechado',
    'reopened'    => 'Reaberto',
  ];
  $statusStyle = [
    'open'        => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
    'in_progress' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-200',
    'waiting'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
    'resolved'    => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-200',
    'closed'      => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
    'reopened'    => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
  ];
  $prioStyle = [
    'low'    => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
    'normal' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-200',
    'high'   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
    'urgent' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
  ];
?>

<div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-6">
  
  <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
    <input
      name="q"
      value="<?php echo e(request('q')); ?>"
      placeholder="Buscar por código/assunto/autor…"
      class="md:col-span-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm"
    />

    <select name="status" class="rounded-xl border-slate-200 dark:border-white/10" onchange="this.form.submit()">
      <option value="">Todos status</option>
      <?php $__currentLoopData = $statusLabel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($k); ?>" <?php if(request('status')===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <select name="sector" class="rounded-xl border-slate-200 dark:border-white/10" onchange="this.form.submit()">
      <option value="">Todos setores</option>
      <?php $__currentLoopData = ($sectors ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($s->id); ?>" <?php if(request('sector')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <select name="priority" class="rounded-xl border-slate-200 dark:border-white/10" onchange="this.form.submit()">
      <option value="">Todas prioridades</option>
      <?php $__currentLoopData = ['low'=>'Baixa','normal'=>'Normal','high'=>'Alta','urgent'=>'Urgente']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($k); ?>" <?php if(request('priority')===$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </form>

  
  <div class="mt-5 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-2 pr-3">Código</th>
          <th class="py-2 pr-3">Assunto</th>
          <th class="py-2 pr-3">Setor</th>
          <th class="py-2 pr-3">Autor</th>
          <th class="py-2 pr-3">Responsável</th>
          <th class="py-2 pr-3">Prioridade</th>
          <th class="py-2 pr-3">Status</th>
          <th class="py-2">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            // Nomes com fallback seguro (controller pode usar relations ou joins)
            $reporter = $t->author->name ?? $t->user->name ?? ($t->reporter_name ?? '—');
            $assignee = $t->assignee->name ?? ($t->agent_name ?? '—');
            $sector   = $t->sector->name ?? ($t->sector_name ?? '—');

            $st   = $t->status ?? 'open';
            $prio = $t->priority ?? 'normal';
          ?>
          <tr class="border-t border-slate-100 dark:border-white/10 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
            <td class="py-2 pr-3 font-mono text-xs"><?php echo e($t->code); ?></td>
            <td class="py-2 pr-3">
              <div class="font-medium"><?php echo e($t->subject); ?></div>
              <div class="text-xs text-slate-500"><?php echo e(\Illuminate\Support\Str::limit($t->description ?? '', 80)); ?></div>
            </td>
            <td class="py-2 pr-3"><?php echo e($sector); ?></td>
            <td class="py-2 pr-3"><?php echo e($reporter); ?></td>
            <td class="py-2 pr-3"><?php echo e($assignee); ?></td>
            <td class="py-2 pr-3">
              <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($prioStyle[$prio] ?? 'bg-slate-100'); ?>">
                <?php echo e(ucfirst(__($prio))); ?>

              </span>
            </td>
            <td class="py-2 pr-3">
              <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($statusStyle[$st] ?? 'bg-slate-100'); ?>">
                <?php echo e($statusLabel[$st] ?? strtoupper($st)); ?>

              </span>
            </td>
            <td class="py-2">
              <a
                href="<?php echo e(route('admin.supports.show', $t)); ?><?php if(request()->getQueryString()): ?>?<?php echo e(request()->getQueryString()); ?><?php endif; ?>"
                class="text-brand-700 hover:underline"
                title="Abrir chamado <?php echo e($t->code); ?>"
              >Abrir</a>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="8" class="py-6 text-center text-slate-500">
              Nenhum chamado encontrado com os filtros atuais.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-4"><?php echo e($tickets->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/dashboard/support/moderation.blade.php ENDPATH**/ ?>