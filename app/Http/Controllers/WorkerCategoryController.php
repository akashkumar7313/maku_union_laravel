<?php

namespace App\Http\Controllers;

use App\Models\WorkerCategory;

class WorkerCategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', [
            'categories' => WorkerCategory::query()->active()->ordered()->get(),
        ]);
    }

    public function show(WorkerCategory $workerCategory)
    {
        abort_unless($workerCategory->is_active, 404);

        return view('categories.show', [
            'category' => $workerCategory,
            'otherCategories' => WorkerCategory::query()
                ->active()
                ->where('id', '!=', $workerCategory->id)
                ->ordered()
                ->limit(6)
                ->get(),
            'memberCount' => $workerCategory->members()->active()->count(),
        ]);
    }
}
