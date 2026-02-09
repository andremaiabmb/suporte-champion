<?php

namespace App\Http\Controllers\PublicSite;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StoreController extends \App\Http\Controllers\Controller
{
    /** Lista de produtos (com filtros e ordenação) */
    public function index(Request $request)
    {
        $hasProducts   = Schema::hasTable('products');
        $hasCategories = Schema::hasTable('product_categories');

        // Categorias
        $categories = [];
        if ($hasCategories) {
            try {
                $categories = DB::table('product_categories')
                    ->select('id', 'name', 'slug')
                    ->orderBy('name')->get();
            } catch (\Throwable $e) {
                $categories = collect();
            }
        } else {
            $categories = collect([
                (object)['id'=>1,'name'=>'Materiais','slug'=>'materiais'],
                (object)['id'=>2,'name'=>'Vestuário','slug'=>'vestuario'],
                (object)['id'=>3,'name'=>'Acessórios','slug'=>'acessorios'],
                (object)['id'=>4,'name'=>'Serviços','slug'=>'servicos'],
            ]);
        }

        $query = $request->string('q')->toString();
        $cat   = $request->integer('cat') ?: null;
        $sort  = $request->string('sort', 'new')->toString();

        if ($hasProducts) {
            $builder = DB::table('products')
                ->select('id','title','price','compare_at_price','thumbnail_url','slug','category_id','stock','created_at')
                ->when($query, fn($q) => $q->where('title','like',"%{$query}%"))
                ->when($cat,   fn($q) => $q->where('category_id',$cat));

            $builder = match ($sort) {
                'price_asc'  => $builder->orderBy('price','asc'),
                'price_desc' => $builder->orderBy('price','desc'),
                default      => $builder->orderBy('created_at','desc'),
            };

            try {
                $products = $builder->paginate(12)->withQueryString();
            } catch (\Throwable $e) {
                $products = collect();
            }
        } else {
            // Vitrine FAKE (se não existir tabela)
            $seedNames = ['Caderno argolado','Moletom oficial','Caneca térmica','Adesivos','Curso de Redação','Planner 2025','Ecobag','Mousepad','Licença Software','Bloco de notas','Garrafa 600ml','Jaqueta Corta-Vento'];
            $seedPrices = [39.9, 199.9, 79.9, 19.9, 149.0, 59.9, 49.9, 29.9, 249.0, 24.9, 69.9, 229.9];
            $seedCompare = [59.9, 249.9, 109.9, 29.9, 199.0, 89.9, 79.9, 39.9, 299.0, 34.9, 99.9, 279.9];

            $fake = [];
            foreach ($seedNames as $i => $name) {
                $fake[] = (object)[
                    'id' => $i+1,
                    'title' => $name,
                    'price' => $seedPrices[$i],
                    'compare_at_price' => $i%3===0 ? $seedCompare[$i] : null,
                    'thumbnail_url' => null,
                    'slug' => Str::slug($name).'-'.$i,
                    'category_id' => ($i%$categories->count())+1,
                    'stock' => $i%5===0 ? 0 : 12+$i,
                    'created_at' => now()->subDays($i),
                ];
            }
            // filtros em memória
            if ($query) $fake = array_values(array_filter($fake, fn($p)=>stripos($p->title,$query)!==false));
            if ($cat)   $fake = array_values(array_filter($fake, fn($p)=>$p->category_id===(int)$cat));
            if ($sort==='price_asc')  usort($fake, fn($a,$b)=>$a->price<=>$b->price);
            if ($sort==='price_desc') usort($fake, fn($a,$b)=>$b->price<=>$a->price);

            // paginação simples (fake)
            $page = max(1, (int) $request->integer('page', 1));
            $perPage = 12;
            $offset = ($page-1)*$perPage;
            $slice = array_slice($fake, $offset, $perPage);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice, count($fake), $perPage, $page, ['path' => url('/loja')]
            );
        }

        return view('store.index', [
            'categories' => $categories,
            'products'   => $products,
        ]);
    }

    /** Página de produto */
    public function show(string $slug)
    {
        $hasProducts = Schema::hasTable('products');

        if ($hasProducts) {
            try {
                $product = DB::table('products')
                    ->select('id','title','description','price','compare_at_price','thumbnail_url','slug','stock','created_at')
                    ->where('slug', $slug)
                    ->orWhere('id', is_numeric($slug) ? (int)$slug : 0)
                    ->first();
            } catch (\Throwable $e) {
                $product = null;
            }
        } else {
            // mock minimal se não existir tabela
            $product = (object)[
                'id' => 1,
                'title' => 'Produto Exemplo',
                'description' => 'Descrição do produto. Características, benefícios e informações técnicas.',
                'price' => 199.90,
                'compare_at_price' => 249.90,
                'thumbnail_url' => null,
                'slug' => 'produto-exemplo',
                'stock' => 8,
                'created_at' => now()->subDays(3),
            ];
        }

        if (!$product) {
            abort(404);
        }

        return view('store.show', [
            'p' => $product,
        ]);
    }

    /** Carrinho */
    public function cart(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);
        return view('store.cart', compact('cart','total'));
    }

    /** Adicionar ao carrinho (sessão) */
    public function addToCart(Request $request)
    {
        $id = (int) $request->input('product_id');
        $title = $request->input('title');
        $price = (float) $request->input('price', 0);
        $thumb = $request->input('thumbnail_url');

        // fallback: tentar buscar do DB se não vier preço
        if ($price <= 0 && Schema::hasTable('products')) {
            try {
                $row = DB::table('products')->select('id','title','price','thumbnail_url')->where('id',$id)->first();
                if ($row) {
                    $title = $row->title;
                    $price = (float) $row->price;
                    $thumb = $row->thumbnail_url;
                }
            } catch (\Throwable $e) {}
        }

        if ($id <= 0 || $price <= 0) {
            return back()->with('error', 'Não foi possível adicionar ao carrinho.');
        }

        $cart = $request->session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['qty'] += 1;
        } else {
            $cart[$id] = [
                'id'    => $id,
                'title' => $title ?: 'Produto',
                'price' => $price,
                'thumb' => $thumb,
                'qty'   => 1,
            ];
        }

        $request->session()->put('cart', $cart);
        return redirect()->route('store.cart')->with('success', 'Produto adicionado ao carrinho.');
    }

    /** Remover item */
    public function removeFromCart(Request $request)
    {
        $id = (int) $request->input('product_id');
        $cart = $request->session()->get('cart', []);
        unset($cart[$id]);
        $request->session()->put('cart', $cart);
        return back();
    }

    /** Limpar carrinho */
    public function clearCart(Request $request)
    {
        $request->session()->forget('cart');
        return back();
    }
}
