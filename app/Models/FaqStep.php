<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqStep extends Model
{
    protected $table = 'faq_steps';

    protected $fillable = [
        'faq_id',
        'step_number',
        'title',
        'body',
        'image_path',
        'image_caption',
    ];

    public function faq(): BelongsTo
    {
        return $this->belongsTo(Faq::class, 'faq_id');
    }
}
