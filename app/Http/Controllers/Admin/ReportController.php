<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GrievanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Grievance;
use App\Models\Member;
use App\Models\WorkerCategory;
use App\Services\StatisticsService;

class ReportController extends Controller
{
    public function index(StatisticsService $statistics)
    {
        $categories = WorkerCategory::query()
            ->withCount([
                'members',
                'members as active_members_count' => fn ($q) => $q->active(),
            ])
            ->ordered()
            ->get();

        $grievanceStatuses = Grievance::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthlyRegistrations = Member::query()
            ->whereYear('joined_on', now()->year)
            ->get(['joined_on'])
            ->groupBy(fn ($member) => $member->joined_on?->month)
            ->map->count()
            ->sortKeys();

        return view('admin.reports.index', [
            'yearly' => $statistics->yearly(),
            'categories' => $categories,
            'grievanceStatuses' => $grievanceStatuses,
            'statusLabels' => collect(GrievanceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]),
            'monthlyRegistrations' => $monthlyRegistrations,
            'currentYear' => now()->year,
        ]);
    }

    public function export(StatisticsService $statistics)
    {
        $yearly = $statistics->yearly();

        return response()->streamDownload(function () use ($yearly) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['वर्ष', 'पंजीकृत सदस्य', 'सक्रिय सदस्य', 'कुल शिकायतें', 'समाधान किए गए', 'लंबित']);

            foreach ($yearly as $year => $row) {
                fputcsv($out, [
                    $year === 'combined' ? 'संयुक्त (सर्वकालिक)' : $year,
                    $row['registered'],
                    $row['active'],
                    $row['grievances'],
                    $row['resolved'],
                    $row['pending'],
                ]);
            }

            fclose($out);
        }, 'maku-report-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
