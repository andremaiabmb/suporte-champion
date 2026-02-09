<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $query = Event::query();

        if (Schema::hasColumn('events', 'starts_at')) {
            $query->orderBy('starts_at', 'desc');
        } else {
            $query->latest();
        }

        $events = $query->paginate(10);

        $counts = DB::table('event_registrations')
            ->select('event_id', DB::raw('count(*) as total'))
            ->whereIn('event_id', $events->pluck('id'))
            ->groupBy('event_id')
            ->pluck('total', 'event_id');

        return view('dashboard.admin.events.index', compact('events', 'counts'));
    }

    public function create()
    {
        return view('dashboard.admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'starts_at' => 'required|date',
            'ends_at'   => 'required|date|after_or_equal:starts_at',
            'location'  => 'nullable|string|max:255',
            'cover'     => 'nullable|image|max:2048', // até ~2MB
        ]);

        $data = $request->only(['title','starts_at','ends_at','location']);
        $data['starts_at'] = Carbon::parse($data['starts_at'])->toDateTimeString();
        $data['ends_at']   = Carbon::parse($data['ends_at'])->toDateTimeString();

        // Upload de capa (opcional)
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('events', 'public'); // storage/app/public/events/...
            $data['cover_path'] = $path; // ex: events/abc.jpg
        }

        Event::create($data);

        return redirect()
            ->route('admin.events.index')
            ->with('status', 'Evento criado com sucesso.');
    }

    public function show(Event $event)
    {
        $registrations = DB::table('event_registrations as er')
            ->join('users as u', 'u.id', '=', 'er.user_id')
            ->select('er.id','er.status','er.created_at','u.id as user_id','u.name','u.email')
            ->where('er.event_id', $event->id)
            ->orderByDesc('er.created_at')
            ->paginate(15);

        return view('dashboard.admin.events.show', compact('event','registrations'));
    }

    public function edit(Event $event)
    {
        return view('dashboard.admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'starts_at'    => 'required|date',
            'ends_at'      => 'required|date|after_or_equal:starts_at',
            'location'     => 'nullable|string|max:255',
            'cover'        => 'nullable|image|max:2048',
            'remove_cover' => 'nullable|boolean',
        ]);

        $data = $request->only(['title','starts_at','ends_at','location']);
        $data['starts_at'] = Carbon::parse($data['starts_at'])->toDateTimeString();
        $data['ends_at']   = Carbon::parse($data['ends_at'])->toDateTimeString();

        // Remover capa atual (se marcado)
        if ($request->boolean('remove_cover') && $event->cover_path) {
            Storage::disk('public')->delete($event->cover_path);
            $data['cover_path'] = null;
        }

        // Substituir capa (se enviada)
        if ($request->hasFile('cover')) {
            // apaga antiga se existir
            if ($event->cover_path) {
                Storage::disk('public')->delete($event->cover_path);
            }
            $path = $request->file('cover')->store('events', 'public');
            $data['cover_path'] = $path;
        }

        $event->update($data);

        return redirect()
            ->route('admin.events.index')
            ->with('status', 'Evento atualizado com sucesso.');
    }

    public function destroy(Event $event)
    {
        // apaga a capa armazenada
        if ($event->cover_path) {
            Storage::disk('public')->delete($event->cover_path);
        }

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('status', 'Evento removido com sucesso.');
    }
}
