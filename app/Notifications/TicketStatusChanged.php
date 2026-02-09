<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $old,
        public string $new
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Status atualizado: '.$this->ticket->code)
            ->greeting('Olá,')
            ->line('O status do seu chamado foi alterado:')
            ->line('De: '.strtoupper($this->old).' → Para: '.strtoupper($this->new))
            ->action('Ver detalhes', url('/s/'.$this->ticket->code));
    }
}
