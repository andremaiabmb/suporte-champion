<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // middleware de role/verified cuida do resto
    }

    public function rules(): array
    {
        return [
            'question' => ['required','string','max:255'],
            'slug'     => ['nullable','string','max:255','unique:faqs,slug'],
            'answer'   => ['nullable','string'],
            'publish'  => ['nullable','boolean'],

            // Steps (arrays)
            'steps'                 => ['nullable','array'],
            'steps.*.title'         => ['nullable','string','max:255'],
            'steps.*.body'          => ['nullable','string'],
            'steps.*.image'         => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'steps.*.image_caption' => ['nullable','string','max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique'   => 'Já existe um FAQ com esse slug.',
            'steps.*.image.image' => 'O arquivo do passo deve ser uma imagem válida.',
        ];
    }
}
