<?php

namespace App\Http\Controllers;

use App\Models\ActionCard;
use App\Models\Banner;
use App\Models\Gallery;
use App\Models\MissionCard;
use App\Models\News;
use App\Models\TeamMember;
use App\Models\WorkerCategory;
use App\Services\StatisticsService;

class HomeController extends Controller
{
    public function __invoke(StatisticsService $statistics)
    {
        $banners = Banner::query()->active()->get();

        return view('home', [
            'banner' => $banners->first(),
            'banners' => $banners,
            'actionCards' => ActionCard::query()->active()->get(),
            'categories' => WorkerCategory::query()->active()->ordered()->get(),
            'missionCards' => MissionCard::query()->active()->get(),
            'newsItems' => News::query()->published()->latest('published_at')->limit(6)->get(),
            'galleries' => Gallery::query()->active()->limit(4)->get(),
            'teamMembers' => TeamMember::query()->active()->limit(6)->get(),
            'statistics' => $statistics->summary(),
        ]);
    }
}
