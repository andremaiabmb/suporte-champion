<?php
// app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\ImageVariants;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'content',
        'published_at', 'cover_path',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Retorna array com srcsets para o componente <x-picture>.
     * Se não houver cover_path, retorna null.
     */
    public function coverSrcsets(): ?array
    {
        if (!$this->cover_path) return null;
        return ImageVariants::srcsets($this->cover_path);
    }
}
