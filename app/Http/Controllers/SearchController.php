<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Page;
use App\Models\WorkerCategory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ], [
            'q.required' => 'कृपया खोज शब्द लिखें।',
            'q.min' => 'कृपया कम से कम 2 अक्षर लिखें।',
        ]);

        $term = '%'.trim((string) $request->input('q')).'%';

        return view('search', [
            'query' => $request->input('q'),
            'newsItems' => News::query()->published()
                ->where(fn ($q) => $q->where('title', 'like', $term)
                    ->orWhere('excerpt', 'like', $term)
                    ->orWhere('body', 'like', $term))
                ->latest('published_at')->limit(20)->get(),
            'pages' => Page::query()->active()
                ->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('body', 'like', $term))
                ->limit(10)->get(),
            'categories' => WorkerCategory::query()->active()
                ->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('description', 'like', $term))
                ->limit(10)->get(),
        ]);
    }
}
