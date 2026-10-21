<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category = null;

        $query = News::query()->published()->with('category')->latest('published_at');

        if ($request->filled('category')) {
            $category = NewsCategory::query()
                ->where('slug', $request->string('category'))
                ->where('is_active', true)
                ->first();

            abort_unless($category, 404);

            $query->where('news_category_id', $category->id);
        }

        if ($request->filled('q')) {
            $term = '%'.trim((string) $request->input('q')).'%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('excerpt', 'like', $term));
        }

        return view('news.index', [
            'newsItems' => $query->paginate(config('maku.pagination.public'))->withQueryString(),
            'categories' => NewsCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'activeCategory' => $category,
            'search' => $request->input('q'),
        ]);
    }

    public function show(News $news)
    {
        abort_unless($news->status === 'published' && $news->published_at?->isPast(), 404);

        $news->increment('views');

        return view('news.show', [
            'news' => $news->load('category', 'author'),
            'related' => News::query()
                ->published()
                ->where('id', '!=', $news->id)
                ->when($news->news_category_id, fn ($q) => $q->where('news_category_id', $news->news_category_id))
                ->latest('published_at')
                ->limit(4)
                ->get(),
        ]);
    }
}
