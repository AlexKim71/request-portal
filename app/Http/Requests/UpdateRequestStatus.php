<?php

namespace App\Http\Requests;

use App\Enums\RequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequestStatus extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isManager();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(RequestStatus::class)],
            'rejection_reason' => [
                'nullable', 'string', 'min:10', 'max:1000',
                Rule::requiredIf(fn () => $this->input('status') === RequestStatus::REJECTED->value),
            ],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required'           => 'Оберіть новий статус.',
            'rejection_reason.required' => 'Вкажіть причину відхилення.',
            'rejection_reason.min'      => 'Причина має містити щонайменше 10 символів.',
        ];
    }
}
