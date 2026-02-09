<footer class="mt-8 border-t border-slate-200 dark:border-white/10">
  <div class="container">
    <div class="py-6 text-sm text-slate-600 dark:text-slate-400 flex items-center justify-between">
      <span>© <?php echo e(date('Y')); ?> <?php echo e(__('footer.brand')); ?></span>
      <nav class="flex gap-4">
        <a href="<?php echo e(route('faqs.index')); ?>" class="hover:text-slate-900 dark:hover:text-white">
          <?php echo e(__('footer.faqs')); ?>

        </a>
        <a href="#eventos" class="hover:text-slate-900 dark:hover:text-white">
          <?php echo e(__('footer.events')); ?>

        </a>
      </nav>
    </div>
  </div>
</footer>
<?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/layouts/_footer.blade.php ENDPATH**/ ?>