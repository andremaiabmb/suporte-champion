


<?php $__env->startSection('title', ($faq->question ?? 'FAQ').' · Suporte ao Aluno'); ?>

<?php $__env->startSection('hero'); ?>
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl mt-6 shadow-soft border border-slate-200/70 dark:border-white/10">
  <div class="px-6 py-12 sm:px-10">
    <div class="max-w-3xl">
      <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
        <?php echo e($faq->question); ?>

      </h1>
      <?php if($faq->published_at): ?>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
          Publicado em <?php echo e($faq->published_at->format('d/m/Y')); ?>

        </p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php use Illuminate\Support\Str; ?>

<section class="mt-8">
  <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 shadow-sm p-6 md:p-10">

    
    <?php if(filled($faq->answer)): ?>
      <div class="prose prose-slate max-w-none dark:prose-invert">
        <?php echo Str::markdown($faq->answer); ?>

      </div>
    <?php endif; ?>

    
    <?php if($faq->steps->count()): ?>
      <hr class="my-8 border-slate-200 dark:border-white/10">
      <h2 class="text-xl font-semibold mb-4">Passo a passo</h2>

      <ol class="space-y-6">
        <?php $__currentLoopData = $faq->steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li class="border-l-4 border-brand-600 pl-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
              <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-600 text-white text-sm font-bold">
                <?php echo e($step->step_number); ?>

              </span>
              <?php echo e($step->title); ?>

            </h3>

            <?php if(filled($step->body)): ?>
              <div class="mt-2 prose prose-slate max-w-none dark:prose-invert text-slate-700 dark:text-slate-200">
                <?php echo Str::markdown($step->body); ?>

              </div>
            <?php endif; ?>

            <?php if($step->image_path): ?>
              <figure class="mt-4">
                <img
                  src="<?php echo e(asset('storage/'.$step->image_path)); ?>"
                  alt="<?php echo e($step->image_caption); ?>"
                  class="rounded-xl border border-slate-200 dark:border-white/10 shadow-sm max-h-96 object-contain w-full">
                <?php if($step->image_caption): ?>
                  <figcaption class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    <?php echo e($step->image_caption); ?>

                  </figcaption>
                <?php endif; ?>
              </figure>
            <?php endif; ?>
          </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ol>
    <?php endif; ?>

    <div class="mt-10">
      <a href="<?php echo e(route('faqs.index')); ?>"
         class="inline-flex items-center text-sm font-medium text-brand-600 hover:text-brand-700">
        ← Voltar à lista de dúvidas
      </a>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/faqs/show.blade.php ENDPATH**/ ?>