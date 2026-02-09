<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventRegistration;

class EventController extends Controller
{
    /**
     * Exibe a tela de inscrição para o evento.
     * Rota: GET /eventos/{event}/inscricao
     */
    public function subscribe(Event $event)
    {
        // Verifica se já está inscrito para mostrar mensagem na view
        $already = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        return view('public.events.subscribe', [
            'event' => $event,
            'already_registered' => $already,
        ]);
    }

    /**
     * Efetiva a inscrição do usuário no evento.
     * Rota: POST /eventos/{event}/inscricao
     */
    public function storeRegistration(Request $request, Event $event)
    {
        $userId = Auth::id();

        // Evita duplicidade
        $existing = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            return redirect()
                ->route('events.subscribe', $event)
                ->with('status', 'Você já está inscrito neste evento.');
        }

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id'  => $userId,
            'status'   => 'confirmed',
        ]);

        return redirect()
            ->route('events.subscribe', $event)
            ->with('status', 'Inscrição realizada com sucesso!');
    }
}
