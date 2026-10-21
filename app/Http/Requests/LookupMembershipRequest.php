<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LookupMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => clean_digits($this->input('phone')),
            'membership_no' => strtoupper(trim((string) $this->input('membership_no'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'membership_no' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'digits_between:10,15'],
        ];
    }

    public function messages(): array
    {
        return [
            'membership_no.required' => 'कृपया सदस्यता संख्या भरें।',
            'phone.required' => 'कृपया पंजीकृत मोबाइल नंबर भरें।',
            'phone.digits_between' => 'मोबाइल नंबर 10 से 15 अंकों का होना चाहिए।',
        ];
    }
}
