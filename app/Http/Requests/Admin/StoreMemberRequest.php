<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => clean_digits($this->input('phone')),
            'aadhaar_number' => clean_digits($this->input('aadhaar_number')),
            'district' => $this->input('district') ?: 'मिर्जापुर',
            'email' => $this->filled('email') ? strtolower(trim((string) $this->input('email'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'digits_between:10,15'],
            'email' => ['nullable', 'email', 'max:190'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'dob' => ['nullable', 'date', 'before:today'],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'district' => ['required', 'string', 'max:100'],
            'block' => ['nullable', 'string', 'max:100'],
            'worker_category_id' => ['required', Rule::exists('worker_categories', 'id')],
            'aadhaar_number' => ['nullable', 'digits:12'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'id_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'mark_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'सदस्य का नाम आवश्यक है।',
            'phone.required' => 'मोबाइल नंबर आवश्यक है।',
            'phone.digits_between' => 'मोबाइल नंबर 10 से 15 अंकों का होना चाहिए।',
            'address.required' => 'पता आवश्यक है।',
            'worker_category_id.required' => 'श्रमिक श्रेणी चुनें।',
            'aadhaar_number.digits' => 'आधार नंबर 12 अंकों का होना चाहिए।',
        ];
    }
}
