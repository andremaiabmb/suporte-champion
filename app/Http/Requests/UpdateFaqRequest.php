<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $faqId = $this->route('faq'); // route-model-binding ou ID simples

        return [
            'question' => ['required','string','max:255'],
            'slug'     => ['nullable','string','max:255', Rule::unique('faqs','slug')->ignore($faqId)],
            'answer'   => ['nullable','string'],
            'publish'  => ['nullable','boolean'],

            'steps'                 => ['nullable','array'],
            'steps.*.title'         => ['nullable','string','max:255'],
            'steps.*.body'          => ['nullable','string'],
            'steps.*.image'         => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'steps.*.image_caption' => ['nullable','string','max:255'],
        ];
    }
}
