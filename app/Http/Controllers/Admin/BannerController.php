<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        return view('admin.banners.index', [
            'banners' => Banner::query()->orderBy('sort_order')->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $data = $this->validated($request);
        $data['image'] = $this->storeUpload($request->file('image'), 'banners');

        $banner = Banner::create($data);
        $audit->log('banner.created', $banner, [], ['title' => $banner->title]);

        return back()->with('success', 'बैनर जोड़ा गया।');
    }

    public function update(Request $request, Banner $banner, AuditLogger $audit)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $this->deleteUpload($banner->image);
            $data['image'] = $this->storeUpload($request->file('image'), 'banners');
        }

        $banner->update($data);
        $audit->log('banner.updated', $banner, [], ['title' => $banner->title]);

        return back()->with('success', 'बैनर अपडेट हो गया।');
    }

    public function destroy(Banner $banner, AuditLogger $audit)
    {
        $this->deleteUpload($banner->image);
        $audit->log('banner.deleted', $banner, ['title' => $banner->title], []);
        $banner->delete();

        return back()->with('success', 'बैनर हटा दिया गया।');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'highlight' => ['nullable', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'image.mimes' => 'बैनर चित्र JPG, PNG या WEBP में होना चाहिए।',
            'image.max' => 'बैनर चित्र 5 MB से छोटा होना चाहिए।',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
