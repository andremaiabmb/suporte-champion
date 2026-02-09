<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'name'        => ['required','string','max:120'],
            'code'        => ['nullable','string','max:20','unique:sectors,code'],
            'slug'        => ['nullable','string','max:150','unique:sectors,slug'],
            'email'       => ['nullable','email','max:190'],
            'description' => ['nullable','string'],
            'is_active'   => ['sometimes','boolean'],
            'order_column'=> ['nullable','integer','min:0','max:100000'],
            'users'       => ['array'],
            'users.*'     => ['integer','exists:users,id'],
        ];
    }
}
