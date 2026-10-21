<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\TeamMember;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->is_active, 404);

        return view('pages.show', [
            'page' => $page,
            'teamMembers' => TeamMember::query()->active()->get(),
            'branches' => Branch::query()->active()->get(),
            'galleries' => Gallery::query()->active()->limit(8)->get(),
        ]);
    }

    public function gallery()
    {
        return view('pages.gallery', [
            'galleries' => Gallery::query()->active()->orderBy('sort_order')->paginate(6)->withQueryString(),
        ]);
    }
}
