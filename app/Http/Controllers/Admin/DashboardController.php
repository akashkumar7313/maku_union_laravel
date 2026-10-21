<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Grievance;
use App\Models\Member;
use App\Models\MembershipPayment;
use App\Models\News;
use App\Services\StatisticsService;

class DashboardController extends Controller
{
    public function __invoke(StatisticsService $statistics)
    {
        return view('admin.dashboard', [
            'stats' => $statistics->summary(),
            'yearly' => $statistics->yearly(),
            'pendingMembers' => Member::query()->where('status', 'pending')->count(),
            'pendingPayments' => MembershipPayment::query()->where('status', 'pending')->count(),
            'openGrievances' => Grievance::query()->whereIn('status', ['pending', 'in_progress'])->count(),
            'unreadMessages' => ContactMessage::query()->whereNull('read_at')->count(),
            'draftNews' => News::query()->where('status', 'draft')->count(),
            'recentMembers' => Member::query()->latest()->limit(6)->get(),
            'recentGrievances' => Grievance::query()->latest()->limit(6)->get(),
        ]);
    }
}
