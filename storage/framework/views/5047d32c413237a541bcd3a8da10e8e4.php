<?php
  $isEdit = isset($faq) && $faq->exists;
?>

<?php if(session('status')): ?>
  <div class="mb-4 rounded-xl border border-slate-200 bg-white p-3 text-sm dark:bg-slate-900 dark:border-white/10">
    <?php echo e(session('status')); ?>

  </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Pergunta</label>
        <input type="text" name="question" value="<?php echo e(old('question', $faq->question)); ?>" required
               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 dark:bg-slate-900 dark:border-white/10">
        <?php $__errorArgs = ['question'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-sm text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div>
        <label class="block text-sm font-medium">Slug</label>
        <input type="text" name="slug" value="<?php echo e(old('slug', $faq->slug)); ?>" placeholder="(opcional – deixei em branco para gerar automaticamente)"
               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 dark:bg-slate-900 dark:border-white/10">
        <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-sm text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div>
        <label class="block text-sm font-medium">Resposta (resumo/introdução)</label>
        <textarea name="answer" rows="5" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 dark:bg-slate-900 dark:border-white/10"><?php echo e(old('answer', $faq->answer)); ?></textarea>
        <?php $__errorArgs = ['answer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-sm text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <div class="space-y-3">
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="publish" value="1" <?php echo e(old('publish', $faq->published_at ? 1 : 0) ? 'checked' : ''); ?>>
        Publicar FAQ
      </label>
      <p class="text-xs text-slate-500 dark:text-slate-400">
        Se marcado, o FAQ será publicado (campo <code>published_at</code>).
      </p>
      <p class="text-xs text-slate-500 dark:text-slate-400">
        Para exibir imagens, garanta o symlink: <code>php artisan storage:link</code>.
      </p>
    </div>
  </div>

  
  <div class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-6 dark:bg-slate-900 dark:border-white/10">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Passo a passo com imagens</h2>
      <button type="button" id="addStepBtn"
              class="rounded-xl bg-slate-900 text-white px-3 py-2 text-sm font-medium dark:bg-slate-200 dark:text-slate-900">
        Adicionar passo
      </button>
    </div>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Você pode incluir título, texto, imagem e legenda para cada passo. A ordem é a do formulário.</p>

    <div id="stepsWrapper" class="mt-4 space-y-4">
      <?php
        $oldSteps = old('steps', $isEdit ? $faq->steps->map(function($s){
          return [
            'title' => $s->title,
            'body'  => $s->body,
            'image_caption' => $s->image_caption,
            'existing_image' => $s->image_path,
          ];
        })->toArray() : []);
      ?>

      <?php $__empty_1 = true; $__currentLoopData = $oldSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php echo $__env->make('dashboard.faqs.partials._step', ['index' => $i, 'step' => $s], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        
      <?php endif; ?>
    </div>
  </div>
</div>


<template id="stepTemplate">
  <?php echo $__env->make('dashboard.faqs.partials._step', ['index' => '__INDEX__', 'step' => ['title'=>'','body'=>'','image_caption'=>'','existing_image'=>'']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</template>

<script>
  (function() {
    const wrapper = document.getElementById('stepsWrapper');
    const tpl = document.getElementById('stepTemplate').innerHTML;
    const addBtn = document.getElementById('addStepBtn');

    let nextIndex = <?php echo e(max(0, count($oldSteps))); ?>;

    function addStep() {
      const html = tpl.replaceAll('__INDEX__', nextIndex);
      const div = document.createElement('div');
      div.innerHTML = html.trim();
      wrapper.appendChild(div.firstElementChild);
      nextIndex++;
    }

    addBtn?.addEventListener('click', addStep);

    window.removeStep = function(el) {
      const card = el.closest('[data-step-card]');
      if (card) card.remove();
    }
  })();
</script>
<?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/dashboard/faqs/_form.blade.php ENDPATH**/ ?>