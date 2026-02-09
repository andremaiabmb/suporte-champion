<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PostController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('posts')) {
            return view('public.posts.index', [
                'posts' => collect(),
            ]);
        }

        $now = Carbon::now();
        $cols = Schema::getColumnListing('posts');

        $select = ['id', 'title'];
        foreach (['slug','excerpt','thumbnail_path','cover_path','updated_at','published_at'] as $c) {
            if (in_array($c, $cols)) $select[] = $c;
        }

        $posts = DB::table('posts')
            ->select($select)
            ->when(in_array('published_at',$cols), fn($q) => $q->whereNotNull('published_at')->where('published_at','<=',$now))
            ->orderByDesc(in_array('updated_at',$cols) ? 'updated_at' : 'id')
            ->paginate(9);

        return view('public.posts.index', compact('posts'));
    }

    public function show(Request $request, string $key)
    {
        if (!Schema::hasTable('posts')) {
            abort(404);
        }

        $now = Carbon::now();
        $cols = Schema::getColumnListing('posts');

        // montar select dinâmico
        $select = ['id', 'title'];
        foreach (['slug','excerpt','content','body','content_html','markdown','cover_path','thumbnail_path','published_at','updated_at','created_at'] as $c) {
            if (in_array($c, $cols)) $select[] = $c;
        }

        // tentar por slug primeiro (se a coluna existir), senão por id
        $query = DB::table('posts')->select($select);

        if (in_array('slug', $cols)) {
            $query = $query->where('slug', $key);
        } elseif (ctype_digit($key)) {
            $query = $query->where('id', (int)$key);
        } else {
            // sem coluna slug e key não é id
            abort(404);
        }

        // filtra publicados se published_at existir
        if (in_array('published_at', $cols)) {
            $query = $query->whereNotNull('published_at')->where('published_at','<=',$now);
        }

        $post = $query->first();
        if (!$post) {
            // fallback: se não achou por slug, tentar por id quando key for numérico
            if (ctype_digit($key)) {
                $post = DB::table('posts')
                    ->select($select)
                    ->when(in_array('published_at', $cols), fn($q)=>$q->whereNotNull('published_at')->where('published_at','<=',$now))
                    ->where('id', (int)$key)
                    ->first();
            }
            if (!$post) abort(404);
        }

        return view('public.posts.show', compact('post'));
    }
}
