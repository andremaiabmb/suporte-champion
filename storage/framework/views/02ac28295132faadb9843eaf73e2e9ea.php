<header class="sticky top-0 z-50 bg-brand-900/95 backdrop-blur border-b border-white/10">
  <div class="container">
    <div class="flex h-16 items-center justify-between">
      
      <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2" aria-label="<?php echo e(__('nav.brand_aria')); ?>">
        <img
          src="<?php echo e(asset('images/champion-logo.svg')); ?>"
          alt="Champion Logo"
          class="h-9 w-auto"
          loading="eager"
          fetchpriority="high"
          decoding="async"
        />
        <span class="text-white font-semibold"></span>
      </a>

      
      <nav class="hidden md:flex items-center gap-6">
        <a href="<?php echo e(route('home')); ?>"
           class="text-white/90 hover:text-white text-sm <?php echo e(request()->routeIs('home') ? 'underline underline-offset-4' : ''); ?>">
          <?php echo e(__('nav.home')); ?>

        </a>
        <a href="<?php echo e(route('faqs.index')); ?>"
           class="text-white/90 hover:text-white text-sm <?php echo e(request()->routeIs('faqs.*') ? 'underline underline-offset-4' : ''); ?>">
          <?php echo e(__('nav.faqs')); ?>

        </a>

        
        <?php if(auth()->guard()->check()): ?>
          <?php
            $user = auth()->user();
            $isAdmin = $user->role ?? null;
            $supportRoute = in_array($isAdmin, ['admin', 'moderador', 'staff'])
              ? 'admin.supports.index'
              : 'support.my.index';
          ?>
          <a href="<?php echo e(route($supportRoute)); ?>"
             class="text-white/90 hover:text-white text-sm <?php echo e(request()->routeIs('support.*') || request()->routeIs('admin.supports.*') ? 'underline underline-offset-4' : ''); ?>">
            <?php echo e(__('nav.support')); ?>

          </a>
        <?php endif; ?>

        <?php if(Route::has('store.index')): ?>
          <a href="<?php echo e(route('store.index')); ?>"
             class="text-white/90 hover:text-white text-sm <?php echo e(request()->routeIs('store.*') ? 'underline underline-offset-4' : ''); ?>">
            <?php echo e(__('nav.store')); ?>

          </a>
        <?php endif; ?>

        <?php if(auth()->guard()->check()): ?>
          <a href="<?php echo e(route('dashboard')); ?>"
             class="text-white/90 hover:text-white text-sm <?php echo e(request()->routeIs('dashboard*') ? 'underline underline-offset-4' : ''); ?>">
            <?php echo e(__('nav.dashboard')); ?>

          </a>
        <?php endif; ?>
      </nav>

      
      <div class="flex items-center gap-2">
        
        <?php
          $cur = str_replace('_','-', app()->getLocale());
          if ($cur === 'pt-BR') $cur = 'pt';
          $supported = ['pt','en','es','fr'];
          if (!in_array($cur, $supported)) $cur = 'pt';
          $label = strtoupper($cur);
        ?>

        

        <div class="relative" id="lang-dropdown">
          <button type="button" id="lang-btn"
                  class="inline-flex items-center h-9 rounded-lg border border-white/15 px-2.5 text-sm text-white/90 hover:bg-white/10 transition"
                  aria-haspopup="menu" aria-expanded="false">
            <span class="font-medium" id="lang-current"><?php echo e(strtoupper($cur)); ?></span>
            <svg class="ml-1 h-4 w-4 opacity-80" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 011.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </button>

          <div id="lang-menu"
               class="invisible opacity-0 pointer-events-none absolute right-0 mt-2 w-28 rounded-xl border border-white/15 shadow-lg backdrop-blur transition duration-150"
               style="background-color: #003366;"
               role="menu" aria-labelledby="lang-btn">
            <div class="py-1">
              <?php $__currentLoopData = ['pt'=>'PT','en'=>'EN','es'=>'ES','fr'=>'FR']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" data-lang="<?php echo e($code); ?>" role="menuitem"
                        class="w-full text-left px-3 py-2 text-sm text-white/90 hover:bg-white/10 <?php echo e($cur === $code ? 'bg-white/10' : ''); ?>">
                  <?php echo e($lbl); ?>

                </button>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        </div>

        
        <?php if(auth()->guard()->check()): ?>
          <div class="relative" id="user-dropdown">
            <button id="user-btn" type="button"
                    class="inline-flex items-center h-9 rounded-lg border border-white/15 pl-3 pr-2 text-sm text-white/90 hover:bg-white/10 transition"
                    aria-haspopup="menu" aria-expanded="false">
              <span class="mr-2 truncate max-w-[12ch]"><?php echo e(auth()->user()->name); ?></span>
              <svg class="h-4 w-4 opacity-80" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 011.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
            </button>

            <div id="user-menu"
                 class="invisible opacity-0 pointer-events-none absolute right-0 mt-2 w-44 rounded-xl border border-white/15 shadow-lg backdrop-blur transition duration-150"
                 style="background-color: #003366;"
                 role="menu" aria-labelledby="user-btn">
              <div class="py-1">
                <a href="<?php echo e(route('profile.show')); ?>"
                   class="block px-3 py-2 text-sm text-white/90 hover:bg-white/10" role="menuitem">
                  <?php echo e(__('nav.profile')); ?>

                </a>
                <a href="<?php echo e(route('profile.qrpage')); ?>"
                   class="block px-3 py-2 text-sm text-white/90 hover:bg-white/10" role="menuitem">
                  <?php echo e(__('nav.my_qr')); ?>

                </a>
                <div class="my-1 border-t border-white/10"></div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="w-full text-left px-3 py-2 text-sm text-white/90 hover:bg-white/10" role="menuitem">
                    <?php echo e(__('nav.logout')); ?>

                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php else: ?>
          <?php if(Route::has('login')): ?>
            <a href="<?php echo e(route('login')); ?>"
               class="inline-flex items-center h-9 rounded-lg border border-white/15 px-3 text-sm text-white/90 hover:bg-white/10 transition">
              <?php echo e(__('nav.login')); ?>

            </a>
          <?php endif; ?>
          <?php if(Route::has('register')): ?>
            <a href="<?php echo e(route('register')); ?>"
               class="inline-flex items-center h-9 rounded-lg bg-brand-600 px-3 text-white text-sm font-medium shadow hover:bg-brand-700 transition">
              <?php echo e(__('nav.register')); ?>

            </a>
          <?php endif; ?>
        <?php endif; ?>

        
        <button id="themeToggle"
                class="ml-1 inline-flex items-center justify-center h-9 w-9 rounded-lg border border-white/15 text-white/90 hover:bg-white/10"
                title="<?php echo e(__('nav.theme_toggle')); ?>" type="button">
          <svg id="sun" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.8 1.42-1.42zm10.45 0l1.79-1.8 1.41 1.41-1.8 1.79-1.4-1.4zM12 4V1h-0v3h0zm0 19v-3h0v3h0zm8-8h3v0h-3v0zm-19 0h3v0H1v0zm14.24 7.16l1.8 1.79 1.41-1.41-1.79-1.8-1.42 1.42zM4.84 17.24l-1.79 1.8 1.41 1.41 1.8-1.79-1.42-1.42zM12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
          <svg id="moon" class="h-4 w-4 hidden" viewBox="0 0 24 24" fill="currentColor"><path d="M21.64 13a9 9 0 11-10.63-10.6 1 1 0 00.9 1.45A7 7 0 1020.2 12.1a1 1 0 001.44.9z"/></svg>
        </button>

        
        <button id="menuBtn"
                class="md:hidden ml-2 inline-flex items-center justify-center h-9 w-9 rounded-lg border border-white/15 text-white/90 hover:bg-white/10"
                type="button" aria-label="<?php echo e(__('nav.open_menu')); ?>">
          <svg class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor" fill="none">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const show = (el) => el?.classList.remove("invisible","opacity-0","pointer-events-none");
  const hide = (el) => el?.classList.add("invisible","opacity-0","pointer-events-none");

  const btn = document.getElementById("menuBtn");
  const mobileMenu = document.getElementById("mobileMenu");
  if (btn && mobileMenu) btn.addEventListener("click", () => mobileMenu.classList.toggle("hidden"));

  const langBtn  = document.getElementById("lang-btn");
  const langMenu = document.getElementById("lang-menu");
  const userBtn  = document.getElementById("user-btn");
  const userMenu = document.getElementById("user-menu");

  function closeAll() {
    hide(langMenu);
    hide(userMenu);
    langBtn?.setAttribute("aria-expanded","false");
    userBtn?.setAttribute("aria-expanded","false");
  }

  if (langBtn && langMenu) {
    langBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      const expanded = langBtn.getAttribute("aria-expanded") === "true";
      closeAll();
      if (!expanded) {
        langBtn.setAttribute("aria-expanded","true");
        show(langMenu);
      }
    });

    document.querySelectorAll("#lang-menu [data-lang]").forEach((el) => {
      el.addEventListener("click", async () => {
        try {
          const picked = el.getAttribute("data-lang"); // 'pt' | 'en' | 'es' | 'fr'
          // POST JSON p/ rota do Controller; o Controller normaliza (pt -> pt_BR)
          await fetch("<?php echo e(route('locale.store')); ?>", {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>",
              "Accept": "application/json",
              "Content-Type": "application/json"
            },
            body: JSON.stringify({ locale: picked })
          });
          window.location.reload();
        } catch (err) {
          console.error("Falha ao trocar idioma", err);
        }
      });
    });
  }

  if (userBtn && userMenu) {
    userBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      const expanded = userBtn.getAttribute("aria-expanded") === "true";
      closeAll();
      if (!expanded) {
        userBtn.setAttribute("aria-expanded","true");
        show(userMenu);
      }
    });
  }

  document.addEventListener("click", (e) => {
    if (!langMenu?.contains(e.target) && !userMenu?.contains(e.target)) closeAll();
  });
});
</script>
<?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/layouts/_navbar.blade.php ENDPATH**/ ?>