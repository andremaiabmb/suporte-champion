<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

// app/Policies/TicketPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin
            || in_array($user->role, ['moderador','admin'])
            || $user->sectors()->exists(); // se usar relação many-to-many
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $this->viewAny($user); // ou regras mais restritas
    }
}
