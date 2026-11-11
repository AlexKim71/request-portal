<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }

    public function rules(): array
    {
        return [
            'service_id'    => ['required', 'exists:services,id'],
            'subject'       => ['required', 'string', 'min:5', 'max:200'],
            'body'          => ['required', 'string', 'min:20', 'max:5000'],
            'contact_name'  => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required'    => 'Оберіть послугу.',
            'service_id.exists'      => 'Обрана послуга недійсна.',
            'subject.required'       => 'Вкажіть тему звернення.',
            'subject.min'            => 'Тема має містити щонайменше 5 символів.',
            'body.required'          => 'Опишіть суть звернення.',
            'body.min'               => 'Опис має містити щонайменше 20 символів.',
            'contact_name.required'  => 'Вкажіть контактну особу.',
            'contact_email.required' => 'Вкажіть email для зв\'язку.',
            'contact_email.email'    => 'Некоректний формат email.',
            'contact_phone.regex'    => 'Некоректний формат телефону.',
        ];
    }
}
