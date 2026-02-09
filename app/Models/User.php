<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // se usar verificação de e-mail
use Illuminate\Database\Eloquent\Concerns\HasUlids; // opcional se usa ULIDs
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail // remova "implements" se não usa verificação
{
    // use HasUlids; // descomente se seu projeto usa ULIDs
    use Notifiable;

    /**
     * Atributos em massa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'city',
        'country_of_origin',
        'date_of_birth',
    ];

    /**
     * Atributos ocultos em serialização.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversões de tipo.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth'     => 'date',
        'password'          => 'hashed',
    ];
}
