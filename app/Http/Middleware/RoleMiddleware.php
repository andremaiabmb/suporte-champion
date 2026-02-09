<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Suporta:
     *  - Spatie (hasRole / hasAnyRole / getRoleNames)
     *  - Coluna simples "role" no users
     *  - Vários papéis separados por | , ou ;  (ex.: "moderador|admin")
     *  - Normaliza para minúsculas e aceita "moderator" (en) também
     */
    public function handle(Request $request, Closure $next, string $roles = ''): Response
    {
        $user = $request->user();

        if (!$user) {
            // não autenticado -> 401/redirect
            abort(Response::HTTP_UNAUTHORIZED);
        }

        // explode por pipe, vírgula ou ponto-e-vírgula e normaliza
        $wanted = collect(preg_split('/[|,;]+/', $roles, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($r) => strtolower(trim($r)))
            ->filter()
            ->values();

        // se não passou nenhum papel explicitamente, deixar seguir
        if ($wanted->isEmpty()) {
            return $next($request);
        }

        // mapa de equivalências
        $aliases = [
            'moderador' => ['moderador', 'moderator'], // pt/en
            'admin'     => ['admin', 'administrator'],
        ];

        $wantedExpanded = $wanted->flatMap(function ($r) use ($aliases) {
            if (isset($aliases[$r])) {
                return $aliases[$r];
            }
            return [$r];
        })->unique()->values();

        // 1) Spatie: hasAnyRole/hasRole
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($wantedExpanded->all())) {
            return $next($request);
        }
        if (method_exists($user, 'hasRole')) {
            foreach ($wantedExpanded as $role) {
                if ($user->hasRole($role)) {
                    return $next($request);
                }
            }
        }

        // 2) Spatie: getRoleNames (guard-agnostic)
        if (method_exists($user, 'getRoleNames')) {
            $names = collect($user->getRoleNames() ?: [])->map(fn ($r) => strtolower((string) $r));
            if ($names->intersect($wantedExpanded)->isNotEmpty()) {
                return $next($request);
            }
        }

        // 3) Coluna simples "role"
        if (property_exists($user, 'role') || isset($user->role)) {
            $current = strtolower((string) $user->role);
            if ($wantedExpanded->contains($current)) {
                return $next($request);
            }
        }

        // 4) (Opcional) superuser por Gate::before já foi tratado no AuthServiceProvider,
        // mas, se quiser um fallback extra, descomente abaixo:
        /*
        if (in_array('admin', $wantedExpanded->all(), true) && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }
        */

        // Sem permissão
        abort(Response::HTTP_FORBIDDEN);
    }
}
