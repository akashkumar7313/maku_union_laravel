<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $slug = Str::slug((string) $this->input('slug'));
        $slug = $slug ?: Str::slug((string) $this->input('title'));

        $this->merge([
            'slug' => $slug !== '' ? $slug : 'samachar-'.Str::lower(Str::random(8)),
            'status' => $this->input('status', 'draft'),
        ]);
    }

    public function rules(): array
    {
        $news = $this->route('news');

        return [
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'slug' => ['required', 'string', 'max:200', Rule::unique('news', 'slug')->ignore($news?->id)],
            'news_category_id' => ['nullable', Rule::exists('news_categories', 'id')],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'समाचार का शीर्षक आवश्यक है।',
            'slug.unique' => 'यह स्लग पहले से प्रयोग में है।',
            'body.required' => 'समाचार का विवरण आवश्यक है।',
            'image.mimes' => 'चित्र JPG, PNG या WEBP में होना चाहिए।',
            'status.required' => 'स्थिति चुनें।',
        ];
    }
}
