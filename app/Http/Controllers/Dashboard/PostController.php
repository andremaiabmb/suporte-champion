<?php
// app/Http/Controllers/Dashboard/PostController.php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->paginate(12);

        return view('dashboard.admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('dashboard.admin.posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:posts,slug',
            'excerpt'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'published_at' => 'nullable|date',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120', // 5MB
        ]);

        $data['user_id'] = Auth::id();
        $data['slug']    = $data['slug'] ?? Str::slug($data['title']).'-'.Str::random(6);

        // cria primeiro para ter ID, se precisar usar em outras lógicas
        $post = Post::create($data);

        // Upload da capa no MESMO padrão dos eventos: "posts/<hash>.<ext>" no disco "public"
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('posts', 'public'); // ex.: posts/18Q3qg...kU1vSA.png
            $post->update(['cover_path' => $path]);
        }

        return redirect()->route('admin.posts.index')->with('status', 'Post criado com sucesso.');
    }

    public function show(Post $post)
    {
        return view('dashboard.admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('dashboard.admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:posts,slug,'.$post->id,
            'excerpt'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'published_at' => 'nullable|date',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'remove_cover' => 'nullable|boolean',
        ]);

        // mantém slug atual se vier vazio
        $data['slug'] = $data['slug'] ?: $post->slug;

        // Remover capa?
        if (!empty($data['remove_cover'])) {
            if ($post->cover_path && Storage::disk('public')->exists($post->cover_path)) {
                Storage::disk('public')->delete($post->cover_path);
            }
            $data['cover_path'] = null;
        }

        // Substituir por nova capa?
        if ($request->hasFile('cover')) {
            // apaga a antiga, se existir
            if ($post->cover_path && Storage::disk('public')->exists($post->cover_path)) {
                Storage::disk('public')->delete($post->cover_path);
            }
            // salva no padrão dos eventos
            $path = $request->file('cover')->store('posts', 'public');
            $data['cover_path'] = $path;
        }

        $post->update($data);

        return redirect()->route('admin.posts.edit', $post)->with('status', 'Post atualizado com sucesso.');
    }

    public function destroy(Post $post)
    {
        if ($post->cover_path && Storage::disk('public')->exists($post->cover_path)) {
            Storage::disk('public')->delete($post->cover_path);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post removido com sucesso.');
    }
}
