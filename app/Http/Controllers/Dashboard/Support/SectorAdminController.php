<?php

namespace App\Http\Controllers\Dashboard\Support;

use App\Http\Controllers\Controller;
use App\Http\Requests\SectorStoreRequest;
use App\Http\Requests\SectorUpdateRequest;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SectorAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->string('q')->toString());
        $onlyActive = $request->boolean('active');

        $query = Sector::query()->withCount('users')->orderBy('order_column')->orderBy('name');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name','like',"%{$q}%")
                   ->orWhere('code','like',"%{$q}%")
                   ->orWhere('slug','like',"%{$q}%");
            });
        }
        if ($onlyActive) $query->where('is_active', true);

        $sectors = $query->paginate(15)->withQueryString();

        return view('dashboard.support.sectors.index', compact('sectors','q','onlyActive'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id','name','email']);
        return view('dashboard.support.sectors.create', compact('users'));
    }

    public function store(SectorStoreRequest $request)
    {
        $data = $request->validated();

        if (empty($data['slug'])) $data['slug'] = Str::slug($data['name']);
        if (empty($data['code'])) $data['code'] = Str::upper(Str::substr(Str::slug($data['name']), 0, 6));

        $sector = Sector::create($data);

        if (!empty($data['users'])) {
            $sector->users()->sync($data['users']);
        }

        return redirect()->route('dashboard.support.sectors.index')->with('status', 'Setor criado com sucesso.');
    }

    public function show(Sector $sector)
    {
        $sector->load('users:id,name,email');
        return view('dashboard.support.sectors.show', compact('sector'));
    }

    public function edit(Sector $sector)
    {
        $users = User::orderBy('name')->get(['id','name','email']);
        $selected = $sector->users()->pluck('users.id')->toArray();

        return view('dashboard.support.sectors.edit', compact('sector','users','selected'));
    }

    public function update(SectorUpdateRequest $request, Sector $sector)
    {
        $data = $request->validated();

        if (empty($data['slug'])) $data['slug'] = Str::slug($data['name']);
        if (empty($data['code'])) $data['code'] = Str::upper(Str::substr(Str::slug($data['name']), 0, 6));

        $sector->update($data);

        if (array_key_exists('users', $data)) {
            $sector->users()->sync($data['users'] ?? []);
        }

        return redirect()->route('dashboard.support.sectors.index')->with('status', 'Setor atualizado com sucesso.');
    }

    public function destroy(Sector $sector)
    {
        $sector->delete();
        return back()->with('status', 'Setor removido.');
    }
}
