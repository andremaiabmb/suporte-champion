<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'subject'       => ['required','string','max:180'],
            'description'   => ['required','string','min:10'],
            'sector_id'     => ['nullable','exists:sectors,id'],
            'priority'      => ['required','in:low,normal,high,urgent'],
            'attachments.*' => ['file','max:10240'],
        ];
    }
}
