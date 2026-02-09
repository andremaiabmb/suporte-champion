

<?php $__env->startSection('title', 'Posts • Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-semibold">Posts</h1>
  <a href="<?php echo e(route('admin.posts.create')); ?>" class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-semibold">Novo Post</a>
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
  <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <article class="rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-white/10 shadow-sm overflow-hidden">
      <a href="<?php echo e(route('admin.posts.edit', $post)); ?>" class="block">
        <div class="aspect-[16/9] bg-slate-100 dark:bg-slate-800">
          <?php if($post->coverSrcsets()): ?>
            <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['srcsets' => $post->coverSrcsets(),'alt' => ''.e($post->title).'','class' => 'w-full h-full object-cover']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['srcsets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post->coverSrcsets()),'alt' => ''.e($post->title).'','class' => 'w-full h-full object-cover']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $attributes = $__attributesOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $component = $__componentOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__componentOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
          <?php endif; ?>
        </div>
        <div class="p-4">
          <h3 class="font-semibold line-clamp-2"><?php echo e($post->title); ?></h3>
          <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
            <?php echo e($post->published_at ? $post->published_at->format('d/m/Y H:i') : 'Rascunho'); ?>

          </div>
        </div>
      </a>
      <div class="px-4 pb-4 flex items-center gap-2">
        <a href="<?php echo e(route('admin.posts.edit', $post)); ?>" class="text-sm px-3 py-1.5 rounded-lg border hover:bg-slate-50 dark:hover:bg-slate-800">Editar</a>
        <form action="<?php echo e(route('admin.posts.destroy', $post)); ?>" method="POST" onsubmit="return confirm('Remover post?')">
          <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          <button class="text-sm px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Excluir</button>
        </form>
      </div>
    </article>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="text-slate-500">Nenhum post encontrado.</p>
  <?php endif; ?>
</div>

<div class="mt-6">
  <?php echo e($posts->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/dashboard/admin/posts/index.blade.php ENDPATH**/ ?>