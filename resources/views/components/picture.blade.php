{{-- resources/views/components/picture.blade.php --}}
@props([
  'srcsets' => null,  // array ['webp' => '...', 'jpeg' => '...', 'fallback' => '...']
  'alt'     => '',
  'class'   => '',
  'sizes'   => '(max-width: 768px) 100vw, 33vw',
])

@if ($srcsets)
  <picture>
    <source type="image/webp" srcset="{{ $srcsets['webp'] }}" sizes="{{ $sizes }}">
    <source type="image/jpeg" srcset="{{ $srcsets['jpeg'] }}" sizes="{{ $sizes }}">
    <img loading="lazy" decoding="async" alt="{{ $alt }}" class="{{ $class }}" src="{{ $srcsets['fallback'] }}">
  </picture>
@endif
