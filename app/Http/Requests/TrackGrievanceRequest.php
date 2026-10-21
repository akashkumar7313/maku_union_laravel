<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackGrievanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => clean_digits($this->input('phone')),
            'case_no' => strtoupper(trim((string) $this->input('case_no'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'case_no' => ['required', 'string', 'max:40'],
            'phone' => ['required', 'digits_between:10,15'],
        ];
    }

    public function messages(): array
    {
        return [
            'case_no.required' => 'कृपया शिकायत संख्या भरें।',
            'phone.required' => 'कृपया पंजीकृत मोबाइल नंबर भरें।',
        ];
    }
}
