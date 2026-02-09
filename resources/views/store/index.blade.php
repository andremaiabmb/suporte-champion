@extends('layouts.app')

@section('title', 'Loja')

@section('hero')
{{-- HERO / PROMO --}}
<div class="mt-6 overflow-hidden rounded-3xl bg-gradient-to-br from-sky-50 via-white to-indigo-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 border border-slate-200/70 dark:border-white/10">
  <div class="relative px-6 py-10 md:px-10 md:py-14">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
      <div class="max-w-2xl">
        <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-white/80 px-3 py-1 text-xs font-medium text-sky-700 dark:text-sky-300 dark:bg-white/5 dark:border-white/10">
          {{-- tag icon --}}
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12V3h9l9 9-9 9-9-9z"/><path d="M7.5 7.5h.01"/></svg>
          Ofertas de lançamento
        </div>
        <h1 class="mt-3 text-3xl md:text-4xl font-semibold tracking-tight">Loja Acadêmica</h1>
        <p class="mt-3 text-slate-600 dark:text-slate-300 leading-relaxed">
          Materiais, serviços e itens oficiais. Qualidade, suporte e entrega rápida — tudo em um só lugar.
        </p>
        <div class="mt-6 flex flex-wrap items-center gap-3">
          <a href="#grid" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 transition">
            {{-- shopping bag --}}
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2l1 5h10l1-5"/><path d="M3 7h18l-1.5 13a2 2 0 0 1-2 2H6.5a2 2 0 0 1-2-2L3 7z"/><path d="M9 12v2"/><path d="M15 12v2"/></svg>
            Ver produtos
          </a>
          <a href="{{ route('store.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            {{-- sparkles --}}
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 3v4m-2-2h4"/><path d="M19 17v4m-2-2h4"/><path d="M11 21l-1-3-3-1 3-1 1-3 1 3 3 1-3 1-1 3z"/><path d="M14 5l1 2 2 1-2 1-1 2-1-2-2-1 2-1 1-2z"/></svg>
            Novidades
          </a>
        </div>
      </div>

      {{-- imagem decorativa --}}
      <div class="relative shrink-0 w-full lg:w-[32rem]">
        <div class="aspect-[5/3] rounded-2xl border border-slate-200 dark:border-white/10 bg-gradient-to-br from-indigo-100 via-sky-100 to-teal-100 dark:from-indigo-900/40 dark:via-sky-900/30 dark:to-teal-900/30 overflow-hidden">
          <div class="absolute inset-0 grid grid-cols-6 opacity-40">
            @for($i=0;$i<36;$i++)
              <div class="border border-white/20 dark:border-white/5"></div>
            @endfor
          </div>
          <div class="absolute inset-0 flex items-center justify-center">
            {{-- big bag line --}}
            <svg class="w-40 h-40 text-slate-800/20 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M6 2l1 5h10l1-5"/><path d="M3 7h18l-1.5 13a2 2 0 0 1-2 2H6.5a2 2 0 0 1-2-2L3 7z"/><path d="M9 12v2"/><path d="M15 12v2"/>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
@php
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Facades\Schema;

  // BUSCA SEGURA (não quebra se tabela não existir)
  $hasProducts = Schema::hasTable('products');
  $hasCategories = Schema::hasTable('product_categories');

  $categories = [];
  if ($hasCategories) {
    try {
      $categories = DB::table('product_categories')->select('id','name','slug')->orderBy('name')->get()->toArray();
    } catch (\Throwable $e) { $categories = []; }
  } else {
    $categories = collect([
      (object)['id'=>1,'name'=>'Materiais','slug'=>'materiais'],
      (object)['id'=>2,'name'=>'Vestuário','slug'=>'vestuario'],
      (object)['id'=>3,'name'=>'Acessórios','slug'=>'acessorios'],
      (object)['id'=>4,'name'=>'Serviços','slug'=>'servicos'],
    ])->toArray();
  }

  $query = request('q');
  $cat   = request('cat');
  $sort  = request('sort','new');

  if ($hasProducts) {
    $builder = DB::table('products')
      ->select('id','title','price','compare_at_price','thumbnail_url','slug','category_id','stock','created_at')
      ->when($query, fn($q) => $q->where('title','like',"%{$query}%"))
      ->when($cat, fn($q)  => $q->where('category_id',$cat))
      ->when($sort==='price_asc', fn($q)=>$q->orderBy('price','asc'))
      ->when($sort==='price_desc', fn($q)=>$q->orderBy('price','desc'))
      ->when($sort==='new', fn($q)=>$q->orderBy('created_at','desc'));

    try {
      $products = $builder->limit(12)->get()->toArray();
    } catch (\Throwable $e) { $products = []; }
  } else {
    // PLACEHOLDER: vitrine fake para design
    $products = [];
    $seed = ['Caderno argolado','Moletom oficial','Caneca térmica','Adesivos','Curso de Redação','Planner 2025','Ecobag','Mousepad','Licença Software','Bloco de notas','Garrafa 600ml','Jaqueta Corta-Vento'];
    foreach ($seed as $i => $name) {
      $products[] = (object)[
        'id' => $i+1,
        'title' => $name,
        'price' => [39.9, 199.9, 79.9, 19.9, 149.0, 59.9, 49.9, 29.9, 249.0, 24.9, 69.9, 229.9][$i],
        'compare_at_price' => $i%3===0 ? ([59.9, 249.9, 109.9, 29.9, 199.0, 89.9, 79.9, 39.9, 299.0, 34.9, 99.9, 279.9][$i]) : null,
        'thumbnail_url' => null,
        'slug' => 'produto-'.$i,
        'category_id' => ($i%count($categories))+1,
        'stock' => $i%5===0 ? 0 : 12+$i,
        'created_at' => now()->subDays($i),
      ];
    }
    // filtros fakes
    if ($query) $products = array_values(array_filter($products, fn($p)=>stripos($p->title,$query)!==false));
    if ($cat)   $products = array_values(array_filter($products, fn($p)=>$p->category_id==(int)$cat));
    if ($sort==='price_asc')  usort($products, fn($a,$b)=>$a->price<=>$b->price);
    if ($sort==='price_desc') usort($products, fn($a,$b)=>$b->price<=>$a->price);
  }

  $activeCat = (int)($cat ?? 0);
@endphp

{{-- BARRA DE CONTROLES --}}
<div class="mt-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
  <form method="GET" class="flex-1">
    <div class="flex items-center gap-2">
      <div class="relative w-full md:w-96">
        {{-- search --}}
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3"/></svg>
        </span>
        <input name="q" value="{{ $query }}" placeholder="Buscar produtos..."
               class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
        @if ($activeCat)
          <input type="hidden" name="cat" value="{{ $activeCat }}">
        @endif
        <input type="hidden" name="sort" value="{{ $sort }}">
      </div>

      <button class="inline-flex items-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-4 py-2.5 transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 21l-4.3-4.3"/><circle cx="11" cy="11" r="8"/></svg>
        Buscar
      </button>
    </div>
  </form>

  <div class="flex items-center gap-2">
    {{-- sort --}}
    <form method="GET" class="flex items-center gap-2">
      <input type="hidden" name="q" value="{{ $query }}">
      <input type="hidden" name="cat" value="{{ $activeCat }}">
      <label class="text-sm text-slate-500 dark:text-slate-400">Ordenar:</label>
      <select name="sort" class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 text-sm px-3 py-2.5">
        <option value="new" {{ $sort==='new'?'selected':'' }}>Mais novos</option>
        <option value="price_asc" {{ $sort==='price_asc'?'selected':'' }}>Menor preço</option>
        <option value="price_desc" {{ $sort==='price_desc'?'selected':'' }}>Maior preço</option>
      </select>
      <button class="rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">Aplicar</button>
    </form>

    {{-- cart --}}
    <a href="{{ route('store.cart') ?? url('/carrinho') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 3h3l3.6 12.3A2 2 0 0 0 10.5 17h7.9a2 2 0 0 0 1.9-1.4L23 7H6"/></svg>
      Carrinho
    </a>
  </div>
</div>

{{-- FILTROS / CATEGORIAS --}}
<div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
  <aside class="lg:col-span-3">
    <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5">
      <div class="flex items-center justify-between">
        <h3 class="font-medium">Categorias</h3>
        {{-- layers --}}
        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 2 10 5-10 5L2 7l10-5z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
      </div>
      <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ route('store.index') }}" class="px-3 py-1.5 rounded-full text-sm border {{ $activeCat? 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' : 'border-brand-200 bg-brand-50 text-brand-700 dark:text-brand-200 dark:bg-brand-900/20 dark:border-brand-800' }}">
          Todos
        </a>
        @foreach ($categories as $c)
          <a href="{{ route('store.index', ['cat'=>$c->id, 'q'=>$query, 'sort'=>$sort]) }}"
             class="px-3 py-1.5 rounded-full text-sm border {{ $activeCat===$c->id? 'border-brand-200 bg-brand-50 text-brand-700 dark:text-brand-200 dark:bg-brand-900/20 dark:border-brand-800' : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            {{ $c->name }}
          </a>
        @endforeach
      </div>

      {{-- faixa de preço (somente UI) --}}
      <div class="mt-6">
        <h4 class="text-sm font-medium text-slate-700 dark:text-slate-200">Faixa de preço</h4>
        <div class="mt-3 space-y-2">
          <input id="priceRange" type="range" min="0" max="300" value="150" class="w-full accent-brand-600">
          <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <span>R$ 0</span><span id="priceOut">R$ 150,00</span><span>R$ 300+</span>
          </div>
        </div>
      </div>
    </div>

    {{-- destaque lateral --}}
    <div class="mt-6 rounded-2xl border bg-gradient-to-br from-amber-50 via-white to-rose-50 dark:from-amber-900/20 dark:via-slate-900 dark:to-rose-900/20 border-slate-200 dark:border-white/10 p-5">
      <div class="flex items-start gap-3">
        <span class="inline-flex p-2 rounded-xl bg-amber-100 dark:bg-amber-900/40">
          <svg class="w-5 h-5 text-amber-700 dark:text-amber-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4.2L18 22l-6-4-6 4 1.5-8.8L2 9h7z"/></svg>
        </span>
        <div>
          <div class="font-medium">Programa de pontos</div>
          <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Acumule pontos a cada compra e troque por descontos exclusivos.</p>
          <a href="{{ route('store.index') }}" class="mt-3 inline-flex items-center gap-1 text-sm text-amber-700 dark:text-amber-200 hover:underline">
            Saiba mais
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M7 7h10v10"/></svg>
          </a>
        </div>
      </div>
    </div>
  </aside>

  {{-- GRID DE PRODUTOS --}}
  <section id="grid" class="lg:col-span-9">
    @if (count($products) === 0)
      <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-10 text-center">
        <div class="mx-auto w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-slate-500 dark:text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/></svg>
        </div>
        <p class="mt-4 font-medium">Nenhum produto encontrado</p>
        <p class="text-sm text-slate-500 mt-1">Ajuste os filtros ou tente outra busca.</p>
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach ($products as $p)
          <article class="group rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 overflow-hidden hover:shadow transition">
            {{-- thumb --}}
            <a href="{{ route('store.show', $p->slug ?? $p->id) ?? url('/loja/'.$p->slug) }}" class="block">
              <div class="aspect-[4/3] relative bg-gradient-to-br from-slate-100 to-slate-50 dark:from-slate-800 dark:to-slate-900">
                @if (!empty($p->thumbnail_url))
                  <img src="{{ $p->thumbnail_url }}" alt="{{ $p->title }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                  <div class="absolute inset-0 flex items-center justify-center">
                    {{-- placeholder box --}}
                    <svg class="w-14 h-14 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <rect x="3" y="3" width="18" height="18" rx="3"/>
                      <path d="M3 16l5-5 4 4 5-6 4 5"/>
                    </svg>
                  </div>
                @endif

                {{-- badge estoque/preço --}}
                <div class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-slate-900/80 backdrop-blur px-2 py-1 border border-slate-200 dark:border-white/10 text-xs">
                  @if(($p->stock ?? 1) > 0)
                    <span class="inline-flex w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Em estoque
                  @else
                    <span class="inline-flex w-1.5 h-1.5 rounded-full bg-rose-500"></span> Indisponível
                  @endif
                </div>
                @if($p->compare_at_price && $p->compare_at_price > $p->price)
                  <div class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-amber-500 text-white px-2 py-1 text-xs">
                    {{-- percent --}}
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 5L5 19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                    Oferta
                  </div>
                @endif
              </div>
            </a>

            {{-- content --}}
            <div class="p-5">
              <div class="flex items-start justify-between gap-3">
                <h3 class="font-medium leading-tight line-clamp-2">
                  <a href="{{ route('store.show', $p->slug ?? $p->id) ?? url('/loja/'.$p->slug) }}" class="hover:underline">
                    {{ $p->title }}
                  </a>
                </h3>

                {{-- quick add --}}
                <form action="{{ route('store.cart.add', $p->id ?? null) ?? url('/carrinho/adicionar') }}" method="POST" class="shrink-0">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $p->id }}">
                  <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 h-9 w-9 hover:bg-slate-50 dark:hover:bg-slate-800 transition" title="Adicionar ao carrinho">
                    <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h3l3.6 12.3A2 2 0 0 0 12.5 17h5.9a2 2 0 0 0 1.9-1.4L23 7H6"/><path d="M16 11l-3 3-2-2"/></svg>
                  </button>
                </form>
              </div>

              {{-- price --}}
              <div class="mt-3 flex items-baseline gap-2">
                <div class="text-lg font-semibold">R$ {{ number_format($p->price, 2, ',', '.') }}</div>
                @if($p->compare_at_price && $p->compare_at_price > $p->price)
                  <div class="text-sm text-slate-500 line-through">R$ {{ number_format($p->compare_at_price, 2, ',', '.') }}</div>
                @endif
              </div>

              {{-- actions --}}
              <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('store.show', $p->slug ?? $p->id) ?? url('/loja/'.$p->slug) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-3.5 py-2 text-sm hover:opacity-90 transition">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                  Ver detalhes
                </a>
                <a href="{{ route('store.cart') ?? url('/carrinho') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-3.5 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 3h3l3.6 12.3A2 2 0 0 0 10.5 17h7.9a2 2 0 0 0 1.9-1.4L23 7H6"/></svg>
                  Ir ao carrinho
                </a>
              </div>
            </div>
          </article>
        @endforeach
      </div>

      {{-- paginação mock (se não usa paginator) --}}
      <div class="mt-8 flex items-center justify-center gap-2">
        <a class="inline-flex items-center h-9 px-3 rounded-lg border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-slate-800" href="{{ request()->fullUrlWithQuery(['page'=>max(1,(int)request('page',1)-1)]) }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <span class="text-sm text-slate-500 dark:text-slate-400 px-3 py-2 rounded-lg border border-slate-200 dark:border-white/10">Página {{ (int)request('page',1) }}</span>
        <a class="inline-flex items-center h-9 px-3 rounded-lg border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-slate-800" href="{{ request()->fullUrlWithQuery(['page'=>(int)request('page',1)+1]) }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
      </div>
    @endif
  </section>
</div>

{{-- benefícios --}}
<div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5 flex items-start gap-3">
    <span class="inline-flex p-2 rounded-xl bg-emerald-100 dark:bg-emerald-900/40">
      <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
    </span>
    <div><div class="font-medium">Compra segura</div><div class="text-sm text-slate-500 dark:text-slate-400">Pagamentos criptografados, proteção ao comprador.</div></div>
  </div>
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5 flex items-start gap-3">
    <span class="inline-flex p-2 rounded-xl bg-sky-100 dark:bg-sky-900/40">
      <svg class="w-5 h-5 text-sky-700 dark:text-sky-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/></svg>
    </span>
    <div><div class="font-medium">Nota fiscal & suporte</div><div class="text-sm text-slate-500 dark:text-slate-400">Emissão fiscal e time dedicado para ajudar.</div></div>
  </div>
  <div class="rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-white/10 p-5 flex items-start gap-3">
    <span class="inline-flex p-2 rounded-xl bg-amber-100 dark:bg-amber-900/40">
      <svg class="w-5 h-5 text-amber-700 dark:text-amber-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v14a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4z"/><path d="M8 21v-7h8v7"/></svg>
    </span>
    <div><div class="font-medium">Retirada ou entrega</div><div class="text-sm text-slate-500 dark:text-slate-400">Escolha como receber: campus ou endereço.</div></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Atualiza a saída do range de preço (apenas UI)
  (function(){
    const r = document.getElementById('priceRange');
    const o = document.getElementById('priceOut');
    if (!r || !o) return;
    const fmt = v => 'R$ ' + Number(v).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});
    o.textContent = fmt(r.value);
    r.addEventListener('input', () => o.textContent = fmt(r.value));
  })();
</script>
@endpush
