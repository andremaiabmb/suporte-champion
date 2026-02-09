<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sectors';

    protected $fillable = [
        'name','slug','email','is_active',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'sector_id');
    }
}
