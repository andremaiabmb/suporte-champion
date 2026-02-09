<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTransition extends Model
{
    protected $table = 'ticket_transitions';

    protected $fillable = [
        'ticket_id', 'user_id', 'field', 'old_value', 'new_value',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
