<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsRequest;
use App\Models\News;
use App\Models\NewsCategory;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    use HandlesUploads;

    public function index(Request $request)
    {
        $news = News::query()
            ->with(['category', 'author'])
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.trim((string) $request->input('q')).'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(config('maku.pagination.admin'))
            ->withQueryString();

        return view('admin.news.index', [
            'newsItems' => $news,
            'filters' => $request->only(['q', 'status']),
        ]);
    }

    public function create()
    {
        return view('admin.news.form', [
            'news' => new News(['status' => 'draft']),
            'categories' => NewsCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreNewsRequest $request, AuditLogger $audit)
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;
        $data['image'] = $this->storeUpload($request->file('image'), 'news');

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $news = News::create($data);
        $audit->log('news.created', $news, [], ['title' => $news->title, 'status' => $news->status]);

        return redirect()->route('admin.news.index')->with('success', 'समाचार सफलतापूर्वक जोड़ा गया।');
    }

    public function edit(News $news)
    {
        return view('admin.news.form', [
            'news' => $news,
            'categories' => NewsCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(StoreNewsRequest $request, News $news, AuditLogger $audit)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $this->deleteUpload($news->image);
            $data['image'] = $this->storeUpload($request->file('image'), 'news');
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = $news->published_at ?? now();
        }

        $old = $news->only(['title', 'status', 'published_at']);
        $news->update($data);
        $audit->log('news.updated', $news, $old, $news->only(['title', 'status', 'published_at']));

        return redirect()->route('admin.news.index')->with('success', 'समाचार अपडेट हो गया।');
    }

    public function destroy(News $news, AuditLogger $audit)
    {
        $this->deleteUpload($news->image);
        $audit->log('news.deleted', $news, ['title' => $news->title], []);
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'समाचार हटा दिया गया।');
    }
}
