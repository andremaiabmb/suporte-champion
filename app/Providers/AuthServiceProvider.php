<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Policies\TicketPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
protected $policies = [
    \App\Models\Ticket::class => \App\Policies\TicketPolicy::class,
];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Passe-livre para admin/moderador (pt) / moderator (en), independente de guard
        Gate::before(function ($user, $ability) {
            // Spatie - hasAnyRole/hasRole
            if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin','moderador','moderator'])) {
                return true;
            }
            if (method_exists($user, 'hasRole') && (
                $user->hasRole('admin') || $user->hasRole('moderador') || $user->hasRole('moderator')
            )) {
                return true;
            }

            // Spatie - getRoleNames (guard-agnostic)
            if (method_exists($user, 'getRoleNames')) {
                $names = collect($user->getRoleNames() ?: [])->map(fn($r) => strtolower((string)$r));
                if ($names->intersect(['admin','moderador','moderator'])->isNotEmpty()) {
                    return true;
                }
            }

            // Coluna "role" simples (fallback)
            if (property_exists($user, 'role') &&
                in_array(strtolower((string)$user->role), ['admin','moderador','moderator'], true)) {
                return true;
            }

            return null; // demais casos: seguem para a Policy
        });
    }
}
