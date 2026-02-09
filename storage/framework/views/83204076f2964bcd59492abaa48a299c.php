
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
  'srcsets' => null,  // array ['webp' => '...', 'jpeg' => '...', 'fallback' => '...']
  'alt'     => '',
  'class'   => '',
  'sizes'   => '(max-width: 768px) 100vw, 33vw',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
  'srcsets' => null,  // array ['webp' => '...', 'jpeg' => '...', 'fallback' => '...']
  'alt'     => '',
  'class'   => '',
  'sizes'   => '(max-width: 768px) 100vw, 33vw',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($srcsets): ?>
  <picture>
    <source type="image/webp" srcset="<?php echo e($srcsets['webp']); ?>" sizes="<?php echo e($sizes); ?>">
    <source type="image/jpeg" srcset="<?php echo e($srcsets['jpeg']); ?>" sizes="<?php echo e($sizes); ?>">
    <img loading="lazy" decoding="async" alt="<?php echo e($alt); ?>" class="<?php echo e($class); ?>" src="<?php echo e($srcsets['fallback']); ?>">
  </picture>
<?php endif; ?>
<?php /**PATH C:\Users\Maia\Projetos\Suporte_Champion\suporte-champion\resources\views/components/picture.blade.php ENDPATH**/ ?>