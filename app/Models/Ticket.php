<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'code',
        'user_id',
        'sector_id',
        'assignee_id',
        'subject',
        'description',
        'priority',
        'status',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /* -----------------------------------------------------------------
     | Relationships
     |------------------------------------------------------------------*/

    /** Compatibilidade com controllers/view que usam $ticket->user */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Quem abriu o chamado */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Responsável (atendente) */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /** Setor do chamado */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    /** Todas as mensagens (públicas + internas), mais recentes primeiro */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')->latest();
    }

    /** Somente mensagens visíveis ao aluno */
    public function publicMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')
            ->where('is_internal', 0)
            ->latest();
    }

    /** Notas internas (ocultas do aluno) */
    public function internalNotes(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')
            ->where('is_internal', 1)
            ->latest();
    }

    /** Histórico de transições */
    public function transitions(): HasMany
    {
        return $this->hasMany(TicketTransition::class, 'ticket_id')->latest();
    }

    /* -----------------------------------------------------------------
     | Scopes
     |------------------------------------------------------------------*/

    /** Filtro por status (ex.: open, in_progress, waiting, resolved, closed, reopened) */
    public function scopeFilterStatus($query, ?string $status)
    {
        return filled($status) ? $query->where('status', $status) : $query;
    }

    /** Filtro por setor */
    public function scopeFilterSector($query, $sectorId)
    {
        return filled($sectorId) ? $query->where('sector_id', $sectorId) : $query;
    }

    /** Filtro por responsável (assignee) */
    public function scopeFilterAssignee($query, $assigneeId)
    {
        return filled($assigneeId) ? $query->where('assignee_id', $assigneeId) : $query;
    }

    /** Busca por código ou assunto */
    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('code', 'like', "%{$term}%")
              ->orWhere('subject', 'like', "%{$term}%");
        });
    }

    /* -----------------------------------------------------------------
     | Accessors helpers
     |------------------------------------------------------------------*/

    /** Rótulo legível do status (em pt) */
    protected function statusLabel(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->status) {
                'open'        => 'Aberto',
                'in_progress' => 'Em andamento',
                'waiting'     => 'Aguardando',
                'resolved'    => 'Resolvido',
                'closed'      => 'Fechado',
                'reopened'    => 'Reaberto',
                default       => strtoupper((string) $this->status),
            };
        });
    }

    /** Rótulo legível da prioridade */
    protected function priorityLabel(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->priority) {
                'low'    => 'Baixa',
                'normal' => 'Normal',
                'high'   => 'Alta',
                'urgent' => 'Urgente',
                default  => strtoupper((string) $this->priority),
            };
        });
    }
}
