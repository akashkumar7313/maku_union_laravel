<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsCategoryController extends Controller
{
    public function index()
    {
        return view('admin.news-categories.index', [
            'categories' => NewsCategory::query()->withCount('newsItems')->orderBy('name')->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:news_categories,slug'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'श्रेणी का नाम आवश्यक है।',
            'slug.unique' => 'यह स्लग पहले से प्रयोग में है।',
        ]);

        $data['slug'] = $data['slug'] ?? (Str::slug($data['name']) ?: 'category-'.Str::lower(Str::random(6)));
        $data['is_active'] = $request->boolean('is_active', true);

        $category = NewsCategory::create($data);
        $audit->log('news_category.created', $category, [], ['name' => $category->name]);

        return back()->with('success', 'समाचार श्रेणी जोड़ी गई।');
    }

    public function update(Request $request, NewsCategory $newsCategory, AuditLogger $audit)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('news_categories', 'slug')->ignore($newsCategory->id)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? $newsCategory->slug;
        $data['is_active'] = $request->boolean('is_active', true);

        $old = $newsCategory->only(['name', 'slug', 'is_active']);
        $newsCategory->update($data);
        $audit->log('news_category.updated', $newsCategory, $old, $data);

        return back()->with('success', 'समाचार श्रेणी अपडेट हो गई।');
    }

    public function destroy(NewsCategory $newsCategory, AuditLogger $audit)
    {
        $audit->log('news_category.deleted', $newsCategory, ['name' => $newsCategory->name], []);
        $newsCategory->delete();

        return back()->with('success', 'समाचार श्रेणी हटा दी गई।');
    }
}
