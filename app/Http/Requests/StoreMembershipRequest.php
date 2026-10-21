<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'dob' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'district' => ['required', 'string', 'max:100'],
            'block' => ['nullable', 'string', 'max:100'],
            'worker_category_id' => ['required', Rule::exists('worker_categories', 'id')->where('is_active', true)],
            'aadhaar_number' => ['required', 'digits:12'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'id_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'terms' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'कृपया पूरा नाम भरें।',
            'name.min' => 'नाम कम से कम 2 अक्षरों का होना चाहिए।',
            'phone.required' => 'कृपया मोबाइल नंबर भरें।',
            'phone.digits_between' => 'मोबाइल नंबर 10 से 15 अंकों का होना चाहिए।',
            'email.email' => 'कृपया सही ईमेल पता भरें।',
            'dob.before' => 'जन्म तिथि आज से पहले की होनी चाहिए।',
            'address.required' => 'कृपया पूरा पता भरें।',
            'district.required' => 'कृपया जनपद चुनें।',
            'worker_category_id.required' => 'कृपया श्रमिक श्रेणी चुनें।',
            'worker_category_id.exists' => 'चुनी गई श्रमिक श्रेणी मान्य नहीं है।',
            'aadhaar_number.required' => 'कृपया 12 अंकों का आधार नंबर भरें।',
            'aadhaar_number.digits' => 'आधार नंबर 12 अंकों का होना चाहिए।',
            'photo.image' => 'फ़ोटो एक मान्य चित्र होनी चाहिए।',
            'photo.mimes' => 'फ़ोटो JPG, PNG या WEBP में होनी चाहिए।',
            'photo.max' => 'फ़ोटो 2 MB से छोटी होनी चाहिए।',
            'id_document.mimes' => 'दस्तावेज़ JPG, PNG या PDF में होना चाहिए।',
            'id_document.max' => 'दस्तावेज़ 4 MB से छोटा होना चाहिए।',
            'terms.accepted' => 'सदस्यता के लिए नियम एवं शर्तें स्वीकार करना आवश्यक है।',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'नाम',
            'phone' => 'मोबाइल नंबर',
            'email' => 'ईमेल',
            'address' => 'पता',
            'aadhaar_number' => 'आधार नंबर',
            'district' => 'जनपद',
            'worker_category_id' => 'श्रमिक श्रेणी',
            'photo' => 'फ़ोटो',
            'id_document' => 'दस्तावेज़',
        ];
    }
}
