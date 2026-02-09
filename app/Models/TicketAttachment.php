<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id','message_id','original_name','disk','path','size','mime'
    ];

    public function ticket()  { return $this->belongsTo(Ticket::class); }
    public function message() { return $this->belongsTo(TicketMessage::class, 'message_id'); }
}
