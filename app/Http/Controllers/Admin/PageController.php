<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class PageController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        return view('admin.pages.index', [
            'pages' => Page::query()->orderBy('title')->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(Request $request, Page $page, AuditLogger $audit)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:300'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
            'show_in_footer' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $this->deleteUpload($page->image);
            $data['image'] = $this->storeUpload($request->file('image'), 'pages');
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['show_in_footer'] = $request->boolean('show_in_footer');

        $old = $page->only(['title', 'is_active', 'show_in_footer']);
        $page->update($data);
        $audit->log('page.updated', $page, $old, $page->only(['title', 'is_active', 'show_in_footer']));

        return redirect()->route('admin.pages.index')->with('success', 'पृष्ठ सफलतापूर्वक अपडेट हो गया।');
    }
}
