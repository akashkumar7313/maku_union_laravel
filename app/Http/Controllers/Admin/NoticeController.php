<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        return view('admin.notices.index', [
            'notices' => Notice::query()->latest()->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function create()
    {
        return view('admin.notices.form', ['notice' => new Notice]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $notice = Notice::create($this->validated($request));
        $audit->log('notice.created', $notice, [], ['title' => $notice->title]);

        return redirect()->route('admin.notices.index')->with('success', 'सूचना जोड़ी गई।');
    }

    public function edit(Notice $notice)
    {
        return view('admin.notices.form', ['notice' => $notice]);
    }

    public function update(Request $request, Notice $notice, AuditLogger $audit)
    {
        $notice->update($this->validated($request));
        $audit->log('notice.updated', $notice, [], ['title' => $notice->title]);

        return redirect()->route('admin.notices.index')->with('success', 'सूचना अपडेट हो गई।');
    }

    public function destroy(Notice $notice, AuditLogger $audit)
    {
        $audit->log('notice.deleted', $notice, ['title' => $notice->title], []);
        $notice->delete();

        return redirect()->route('admin.notices.index')->with('success', 'सूचना हटा दी गई।');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['nullable', 'string', 'max:2000'],
            'url' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'सूचना का शीर्षक आवश्यक है।',
            'ends_at.after_or_equal' => 'समाप्ति तिथि प्रारंभ तिथि के बाद होनी चाहिए।',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
