<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Faq extends Model
{
    protected $table = 'faqs';

    protected $fillable = [
        'user_id',
        'question',
        'slug',
        'answer',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /* --------------------------------------------------------
     | Relações
     |---------------------------------------------------------*/
    public function steps(): HasMany
    {
        return $this->hasMany(FaqStep::class, 'faq_id')->orderBy('step_number');
    }

    /* --------------------------------------------------------
     | Ajudantes
     |---------------------------------------------------------*/
    public function isPublished(): bool
    {
        return (bool) $this->published_at && Carbon::parse($this->published_at)->isPast();
    }

    /* --------------------------------------------------------
     | Escopos (agora permite ->published())
     |---------------------------------------------------------*/

    /**
     * Escopo para filtrar apenas FAQs publicados (published_at não nulo e no passado).
     *
     * Uso: Faq::published()->get();
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now());
    }

    /**
     * (Opcional) Escopo de busca textual simples.
     *
     * Uso: Faq::search($q)->get();
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('question', 'like', "%{$term}%")
              ->orWhere('answer', 'like', "%{$term}%")
              ->orWhere('slug', 'like', "%{$term}%");
        });
    }
}
