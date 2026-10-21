<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GalleryController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        return view('admin.gallery.index', [
            'items' => Gallery::query()->orderBy('sort_order')->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function create()
    {
        return view('admin.gallery.form', ['item' => new Gallery(['type' => 'image'])]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $data = $this->validated($request);
        $data['file'] = $this->storeUpload($request->file('file'), 'gallery');

        $item = Gallery::create($data);
        $audit->log('gallery.created', $item, [], ['title' => $item->title]);

        return redirect()->route('admin.gallery.index')->with('success', 'गैलरी आइटम जोड़ा गया।');
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.gallery.form', ['item' => $gallery]);
    }

    public function update(Request $request, Gallery $gallery, AuditLogger $audit)
    {
        $data = $this->validated($request, $gallery);

        if ($request->hasFile('file')) {
            $this->deleteUpload($gallery->file);
            $data['file'] = $this->storeUpload($request->file('file'), 'gallery');
        }

        $gallery->update($data);
        $audit->log('gallery.updated', $gallery, [], ['title' => $gallery->title]);

        return redirect()->route('admin.gallery.index')->with('success', 'गैलरी आइटम अपडेट हो गया।');
    }

    public function destroy(Gallery $gallery, AuditLogger $audit)
    {
        $this->deleteUpload($gallery->file);
        $audit->log('gallery.deleted', $gallery, ['title' => $gallery->title], []);
        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'गैलरी आइटम हटा दिया गया।');
    }

    private function validated(Request $request, ?Gallery $gallery = null): array
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:150'],
            'type' => ['required', Rule::in(['image', 'video'])],
            'file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'type.required' => 'प्रकार चुनें।',
            'file.mimes' => 'चित्र JPG, PNG या WEBP में होना चाहिए।',
            'video_url.url' => 'मान्य वीडियो लिंक भरें।',
        ]);

        if ($data['type'] === 'image' && ! $request->hasFile('file') && ! $gallery?->file) {
            throw ValidationException::withMessages(['file' => 'चित्र अपलोड करें।']);
        }

        if ($data['type'] === 'video' && empty($data['video_url'])) {
            throw ValidationException::withMessages(['video_url' => 'वीडियो लिंक आवश्यक है।']);
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
