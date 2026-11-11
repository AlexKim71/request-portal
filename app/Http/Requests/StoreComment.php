<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComment extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'comment'     => ['required', 'string', 'min:2', 'max:2000'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Введіть текст коментаря.',
            'comment.min'      => 'Коментар має містити щонайменше 2 символи.',
        ];
    }
}
