<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return to_route('login');
        }

        $rawRole = $user->role ?? $user->perfil ?? $user->tipo ?? null;
        $role = is_string($rawRole) ? strtolower(trim($rawRole)) : null;

        $route = match ($role) {
            'admin'      => 'dashboard.admin.index',
            'professor'  => 'dashboard.professor.index',
            'moderador'  => 'dashboard.moderador.index',
            'financeiro' => 'dashboard.financeiro.index',
            'dso'        => 'dashboard.dso.index',
            'aluno'      => 'dashboard.aluno.index',
            default      => null,
        };

        if ($route) {
            return to_route($route);
        }

        abort(403, 'Perfil sem rota configurada.');
    }
}
