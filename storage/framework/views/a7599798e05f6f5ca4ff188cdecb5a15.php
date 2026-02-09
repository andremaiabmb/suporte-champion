

<?php $__env->startSection('title', __('home.meta.title')); ?>

<?php $__env->startSection('hero'); ?>
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl mt-6 shadow-soft border border-slate-200/70 dark:border-white/10">
  <div class="px-5 py-8 sm:px-8">
    <div class="max-w-3xl">
      <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
        <?php echo e(__('home.hero.title')); ?>

      </h1>
      <p class="mt-2 text-slate-600 dark:text-slate-300">
        <?php echo e(__('home.hero.subtitle')); ?>

      </p>
      <div class="mt-4 flex flex-wrap gap-2.5">
        <a href="<?php echo e(route('faqs.index')); ?>" class="inline-flex items-center rounded-xl bg-brand-600 px-4 py-2.5 text-white text-sm font-medium shadow hover:bg-brand-700 transition">
          <?php echo e(__('home.hero.cta_faqs')); ?>

        </a>
        <a href="#eventos" class="inline-flex items-center rounded-xl bg-white px-4 py-2.5 text-slate-700 text-sm font-medium shadow ring-1 ring-slate-900/10 hover:bg-slate-50 transition dark:bg-slate-900 dark:text-slate-200 dark:ring-white/10">
          <?php echo e(__('home.hero.cta_events')); ?>

        </a>
      </div>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

$now = \Carbon\Carbon::now();

$resolveImg = function (?string $path): ?string {
  if (!$path) return null;
  $trim = ltrim($path, '/');
  if (Str::startsWith($trim, ['http://','https://','//'])) return $path;
  if (Str::startsWith($trim, 'public/')) return asset(Str::replaceFirst('public/', 'storage/', $trim));
  if (Str::startsWith($trim, 'storage/')) return asset($trim);
  return asset('storage/'.$trim);
};

/* ==== POSTS ==== */
$latestPosts = collect();
if (Schema::hasTable('posts')) {
  $postCols = Schema::getColumnListing('posts');
  $imgCols = collect(['thumbnail_path','cover_path','image','featured_image','thumb','photo'])->filter(fn($c)=>in_array($c,$postCols))->values()->all();
  $select = ['id','title'];
  foreach (array_merge(['slug','excerpt','updated_at','published_at'],$imgCols) as $c)
    if (in_array($c,$postCols)) $select[]=$c;

  $latestPosts = DB::table('posts')
    ->select($select)
    ->when(in_array('published_at',$postCols), fn($q)=>$q->whereNotNull('published_at')->where('published_at','<=',$now))
    ->orderByDesc(in_array('updated_at',$postCols)?'updated_at':'id')
    ->limit(4)
    ->get()
    ->map(function($p) use($imgCols,$resolveImg){
      $img=null;
      foreach($imgCols as $c) if(!empty($p->{$c})){ $img=$p->{$c}; break; }
      $p->image_url=$resolveImg($img);
      return $p;
    });
}

/* ==== EVENTOS ==== */
$events = collect();
$eventsTable = Schema::hasTable('events') ? 'events' : (Schema::hasTable('dashboard_events') ? 'dashboard_events' : null);
if ($eventsTable) {
  $evCols = Schema::getColumnListing($eventsTable);
  $startCol = collect(['starts_at','start_at','start_date','begin_at','date'])->first(fn($c)=>in_array($c,$evCols));
  $endCol   = collect(['ends_at','end_at','end_date','finish_at'])->first(fn($c)=>in_array($c,$evCols));
  $imgCols  = collect(['cover_path','thumbnail_path','image','banner','photo'])->filter(fn($c)=>in_array($c,$evCols))->values()->all();

  $select = ['id','title'];
  foreach (array_merge(['slug','location'],$imgCols) as $c)
    if (in_array($c,$evCols)) $select[]=$c;
  if($startCol) $select[]=$startCol;
  if($endCol) $select[]=$endCol;

  $events = DB::table($eventsTable)
    ->select($select)
    ->when($startCol && $endCol, fn($q)=>$q->where($endCol, '>=', $now))
    ->when(!$startCol && $endCol, fn($q)=>$q->where($endCol, '>=', $now))
    ->when($startCol && !$endCol, fn($q)=>$q->where($startCol, '>=', $now))
    ->orderBy($startCol ?? $endCol ?? 'id')
    ->limit(4)
    ->get()
    ->map(function($e) use($startCol,$endCol,$imgCols,$resolveImg){
      $img=null;
      foreach($imgCols as $c) if(!empty($e->{$c})){ $img=$e->{$c}; break; }
      $e->start_at=$startCol?$e->{$startCol}:null;
      $e->end_at=$endCol?$e->{$endCol}:null;
      $e->image_url=$resolveImg($img);
      return $e;
    });
}
?>


<section class="mt-8">
  <div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold"><?php echo e(__('home.news.title')); ?></h2>
    <?php if(Route::has('news.index')): ?>
      <a href="<?php echo e(route('news.index')); ?>" class="text-xs text-brand-600 hover:text-brand-700"><?php echo e(__('home.news.see_all')); ?></a>
    <?php endif; ?>
  </div>

  <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <?php $__empty_1 = true; $__currentLoopData = $latestPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php $key = !empty($p->slug)?$p->slug:$p->id; ?>
      <article class="group rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 overflow-hidden hover:shadow transition">
        <a href="<?php echo e(route('news.show',$key)); ?>" class="block">
          <div class="aspect-[16/11] bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
            <?php if(!empty($p->image_url)): ?>
              <img src="<?php echo e($p->image_url); ?>" alt="<?php echo e($p->title); ?>"
                   class="max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-105"
                   loading="lazy">
            <?php else: ?>
              <div class="text-slate-400 text-xs"><?php echo e(__('home.empty.image')); ?></div>
            <?php endif; ?>
          </div>
          <div class="p-5">
            <h3 class="font-medium leading-tight line-clamp-2"><?php echo e($p->title); ?></h3>
            <?php if(!empty($p->excerpt)): ?>
              <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 line-clamp-3"><?php echo e($p->excerpt); ?></p>
            <?php endif; ?>
          </div>
        </a>
      </article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <?php for($i=0;$i<4;$i++): ?>
        <article class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 overflow-hidden hover:shadow transition">
          <div class="aspect-[16/11] bg-slate-100 dark:bg-slate-800 flex items-center justify-center"><?php echo e(__('home.empty.image')); ?></div>
          <div class="p-5">
            <h3 class="font-medium leading-tight"><?php echo e(__('home.placeholder.post_title')); ?></h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300"><?php echo e(__('home.placeholder.post_excerpt')); ?></p>
          </div>
        </article>
      <?php endfor; ?>
    <?php endif; ?>
  </div>
</section>


<section id="eventos" class="mt-10">
  <div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold"><?php echo e(__('home.events.title')); ?></h2>
  </div>

  <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php if(auth()->guard()->guest()): ?>
      <button type="button"
        class="group cursor-pointer text-left rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 overflow-hidden hover:shadow transition event-enroll-btn"
        data-event-title="<?php echo e($e->title); ?>">
        <div class="aspect-[16/11] bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
          <?php if(!empty($e->image_url)): ?>
            <img src="<?php echo e($e->image_url); ?>" alt="<?php echo e($e->title); ?>"
                 class="max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-105"
                 loading="lazy">
          <?php endif; ?>
        </div>
        <div class="p-5">
          <h3 class="font-medium leading-tight line-clamp-2"><?php echo e($e->title); ?></h3>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
            <?php echo e($e->start_at ? \Carbon\Carbon::parse($e->start_at)->format('d/m/Y H:i') : __('home.events.date_tbd')); ?>

            <?php if($e->end_at): ?> — <?php echo e(\Carbon\Carbon::parse($e->end_at)->format('d/m/Y H:i')); ?> <?php endif; ?>
          </p>
        </div>
      </button>
      <?php endif; ?>

      <?php if(auth()->guard()->check()): ?>
      <a href="<?php echo e(route('events.subscribe', $e->id)); ?>"
         class="group block text-left rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 overflow-hidden hover:shadow transition">
        <div class="aspect-[16/11] bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
          <?php if(!empty($e->image_url)): ?>
            <img src="<?php echo e($e->image_url); ?>" alt="<?php echo e($e->title); ?>"
                 class="max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-105"
                 loading="lazy">
          <?php endif; ?>
        </div>
        <div class="p-5">
          <h3 class="font-medium leading-tight line-clamp-2"><?php echo e($e->title); ?></h3>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
            <?php echo e($e->start_at ? \Carbon\Carbon::parse($e->start_at)->format('d/m/Y H:i') : __('home.events.date_tbd')); ?>

            <?php if($e->end_at): ?> — <?php echo e(\Carbon\Carbon::parse($e->end_at)->format('d/m/Y H:i')); ?> <?php endif; ?>
          </p>
        </div>
      </a>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <?php for($i=0;$i<4;$i++): ?>
        <article class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 overflow-hidden hover:shadow transition">
          <div class="aspect-[16/11] bg-slate-100 dark:bg-slate-800 flex items-center justify-center"><?php echo e(__('home.empty.image')); ?></div>
          <div class="p-5">
            <h3 class="font-medium leading-tight"><?php echo e(__('home.placeholder.event_title')); ?></h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300"><?php echo e(__('home.placeholder.event_datetime')); ?></p>
          </div>
        </article>
      <?php endfor; ?>
    <?php endif; ?>
  </div>
</section>


<?php if(auth()->guard()->guest()): ?>
<div id="eventLoginModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
  <div class="relative mx-auto max-w-lg p-4 sm:p-5">
    <div class="mt-20 sm:mt-28 rounded-2xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 shadow-xl ring-1 ring-slate-900/10 dark:ring-white/10 overflow-hidden">
      <div class="p-5">
        <h3 class="text-base font-semibold"><?php echo e(__('home.modal_login.title')); ?></h3>
        <p class="mt-2 text-sm"><?php echo e(__('home.modal_login.subtitle')); ?></p>
      </div>
      <div class="px-5 pb-5 flex items-center justify-end gap-2">
        <?php if(Route::has('register')): ?>
          <a href="<?php echo e(route('register')); ?>" class="rounded-lg px-3 py-1.5 text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800">
            <?php echo e(__('home.modal_login.cta_register')); ?>

          </a>
        <?php endif; ?>
        <?php if(Route::has('login')): ?>
          <a href="<?php echo e(route('login')); ?>" class="rounded-lg px-3 py-1.5 text-sm font-semibold bg-brand-600 text-white hover:bg-brand-700">
            <?php echo e(__('home.modal_login.cta_login')); ?>

          </a>
        <?php endif; ?>
        <button id="eventModalClose" class="rounded-lg px-3 py-1.5 text-sm font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700">
          <?php echo e(__('home.modal_login.close')); ?>

        </button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if(auth()->guard()->guest()): ?>
<script>
(function(){
  const modal=document.getElementById('eventLoginModal');
  const close=document.getElementById('eventModalClose');
  function open(){modal.classList.remove('hidden');document.documentElement.classList.add('overflow-y-hidden');}
  function hide(){modal.classList.add('hidden');document.documentElement.classList.remove('overflow-y-hidden');}
  document.querySelectorAll('.event-enroll-btn').forEach(b=>b.addEventListener('click',open));
  close?.addEventListener('click',hide);
  modal?.addEventListener('click',e=>{if(e.target===modal)hide();});
  document.addEventListener('keydown',e=>{if(e.key==='Escape')hide();});
})();
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/public/home.blade.php ENDPATH**/ ?>