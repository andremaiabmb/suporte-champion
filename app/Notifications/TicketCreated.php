<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Chamado aberto: '.$this->ticket->code)
            ->greeting('Olá,')
            ->line('Seu chamado foi criado com sucesso.')
            ->line('Código: '.$this->ticket->code)
            ->line('Assunto: '.$this->ticket->subject)
            ->action('Acompanhar chamado', url('/s/'.$this->ticket->code));
    }
}
