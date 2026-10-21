<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\WorkerCategory;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkerCategoryController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        return view('admin.worker-categories.index', [
            'categories' => WorkerCategory::query()->withCount('members')->ordered()->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function create()
    {
        return view('admin.worker-categories.form', ['category' => new WorkerCategory]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $data = $this->validated($request);
        $data['image'] = $this->storeUpload($request->file('image'), 'categories');

        $category = WorkerCategory::create($data);
        $audit->log('worker_category.created', $category, [], ['name' => $category->name]);

        return redirect()->route('admin.worker-categories.index')->with('success', 'श्रमिक श्रेणी जोड़ी गई।');
    }

    public function edit(WorkerCategory $workerCategory)
    {
        return view('admin.worker-categories.form', ['category' => $workerCategory]);
    }

    public function update(Request $request, WorkerCategory $workerCategory, AuditLogger $audit)
    {
        $data = $this->validated($request, $workerCategory);

        if ($request->hasFile('image')) {
            $this->deleteUpload($workerCategory->image);
            $data['image'] = $this->storeUpload($request->file('image'), 'categories');
        }

        $old = $workerCategory->only(['name', 'slug', 'sort_order', 'is_active']);
        $workerCategory->update($data);
        $audit->log('worker_category.updated', $workerCategory, $old, $data);

        return redirect()->route('admin.worker-categories.index')->with('success', 'श्रमिक श्रेणी अपडेट हो गई।');
    }

    public function destroy(WorkerCategory $workerCategory, AuditLogger $audit)
    {
        if ($workerCategory->members()->exists()) {
            return back()->with('error', 'इस श्रेणी से जुड़े सदस्य मौजूद हैं। पहले उनका विवरण बदलें।');
        }

        $this->deleteUpload($workerCategory->image);
        $audit->log('worker_category.deleted', $workerCategory, ['name' => $workerCategory->name], []);
        $workerCategory->delete();

        return redirect()->route('admin.worker-categories.index')->with('success', 'श्रमिक श्रेणी हटा दी गई।');
    }

    private function validated(Request $request, ?WorkerCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('worker_categories', 'slug')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:60'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'श्रेणी का नाम आवश्यक है।',
            'slug.unique' => 'यह स्लग पहले से प्रयोग में है।',
            'image.mimes' => 'चित्र JPG, PNG या WEBP में होना चाहिए।',
        ]);

        $data['slug'] = $data['slug'] ?? ($category?->slug ?: (Str::slug($data['name']) ?: 'category-'.Str::lower(Str::random(6))));
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
