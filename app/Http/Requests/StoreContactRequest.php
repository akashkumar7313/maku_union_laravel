<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->filled('phone') ? clean_digits($this->input('phone')) : null,
            'email' => $this->filled('email') ? strtolower(trim((string) $this->input('email'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'digits_between:10,15'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'कृपया अपना नाम भरें।',
            'email.email' => 'कृपया सही ईमेल पता भरें।',
            'phone.digits_between' => 'मोबाइल नंबर 10 से 15 अंकों का होना चाहिए।',
            'message.required' => 'कृपया अपना संदेश लिखें।',
            'message.min' => 'संदेश कम से कम 10 अक्षरों का होना चाहिए।',
        ];
    }
}
