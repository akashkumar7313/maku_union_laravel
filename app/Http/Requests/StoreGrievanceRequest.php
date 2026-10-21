<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGrievanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => clean_digits($this->input('phone')),
            'email' => $this->filled('email') ? strtolower(trim((string) $this->input('email'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'phone' => ['required', 'digits_between:10,15'],
            'email' => ['nullable', 'email', 'max:190'],
            'worker_category_id' => ['nullable', Rule::exists('worker_categories', 'id')->where('is_active', true)],
            'subject' => ['required', 'string', 'min:5', 'max:200'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'documents' => ['nullable', 'array', 'max:5'],
            'documents.*' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:4096'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'कृपया अपना नाम भरें।',
            'phone.required' => 'कृपया मोबाइल नंबर भरें।',
            'phone.digits_between' => 'मोबाइल नंबर 10 से 15 अंकों का होना चाहिए।',
            'subject.required' => 'कृपया शिकायत का विषय भरें।',
            'subject.min' => 'विषय कम से कम 5 अक्षरों का होना चाहिए।',
            'description.required' => 'कृपया समस्या का विवरण भरें।',
            'description.min' => 'कृपया कम से कम 20 अक्षरों में विवरण लिखें।',
            'documents.max' => 'एक बार में अधिकतम 5 दस्तावेज़ अपलोड करें।',
            'documents.*.mimes' => 'दस्तावेज़ JPG, PNG, PDF, DOC या DOCX में होना चाहिए।',
            'documents.*.max' => 'प्रत्येक दस्तावेज़ 4 MB से छोटा होना चाहिए।',
            'consent.accepted' => 'जानकारी की सत्यता की पुष्टि करना आवश्यक है।',
        ];
    }
}
