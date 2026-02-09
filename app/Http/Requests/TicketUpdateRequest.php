<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'sector_id'     => ['nullable','exists:sectors,id'],
            'assignee_id'   => ['nullable','exists:users,id'],
            'status'        => ['required','in:open,in_progress,waiting,resolved,closed,reopened'],
            'priority'      => ['required','in:low,normal,high,urgent'],
            'message'       => ['nullable','string'],
            'is_internal'   => ['sometimes','boolean'],
            'attachments.*' => ['file','max:10240'],
        ];
    }
}
