<?php
  $appLocale = str_replace('_','-', app()->getLocale());
  $pageTitle = trim($__env->yieldContent('title') ?: 'Suporte ao Aluno');
?>
<!doctype html>
<html lang="<?php echo e($appLocale); ?>" class="h-full antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
  <title><?php echo e($pageTitle); ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

  <style>
    :root { color-scheme: light dark; }
    html { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; }
  </style>

  <script>
    (function () {
      try {
        const pref = localStorage.getItem('theme');
        if (pref === 'dark') document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
      } catch (_) {}
    })();
  </script>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
  
  <?php echo $__env->make('layouts._navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  
  <main class="container flex-1">
    <?php echo $__env->yieldContent('hero'); ?>
    <div class="py-8">
      <?php echo $__env->yieldContent('content'); ?>
    </div>
  </main>

  
  <?php echo $__env->make('layouts._footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  
  <script>
    const themeToggle = document.getElementById('themeToggle');
    const sun = document.getElementById('sun');
    const moon = document.getElementById('moon');
    if (themeToggle) {
      themeToggle.addEventListener('click', () => {
        const root = document.documentElement;
        const isDark = root.classList.toggle('dark');
        try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (_) {}
        if (sun && moon) {
          sun.classList.toggle('hidden', isDark);
          moon.classList.toggle('hidden', !isDark);
        }
      });
    }
    (function syncThemeIcons() {
      const isDark = document.documentElement.classList.contains('dark');
      if (sun && moon) {
        sun.classList.toggle('hidden', isDark);
        moon.classList.toggle('hidden', !isDark);
      }
    })();
  </script>

  <?php echo $__env->yieldPushContent('scripts'); ?>
  <?php echo $__env->make('partials.lucide', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/layouts/app.blade.php ENDPATH**/ ?>