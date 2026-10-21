<?php

namespace App\Services;

use App\Enums\GrievanceStatus;
use App\Models\Grievance;
use App\Models\Member;
use Illuminate\Support\Carbon;

class StatisticsService
{
    /**
     * Counting rules (also documented in README):
     * - registered: members whose joined_on falls inside the selected year.
     * - active: for a completed year, membership valid on 31 December of that year
     *   (valid_from <= 31 Dec AND valid_until >= 31 Dec); for the current year / all
     *   time, members currently active.
     * - grievances: grievances created in the selected year.
     * - resolved: grievances with resolved_at in the selected year.
     */
    public function summary(?int $year = null): array
    {
        $registered = Member::query()
            ->when($year, fn ($q) => $q->whereYear('joined_on', $year))
            ->count();

        if ($year && $year < now()->year) {
            $reference = Carbon::create($year, 12, 31)->endOfDay();
            $active = Member::query()
                ->whereNotNull('valid_from')
                ->whereDate('valid_from', '<=', $reference)
                ->whereNotNull('valid_until')
                ->whereDate('valid_until', '>=', $reference)
                ->count();
        } else {
            $active = Member::query()->active()->count();
        }

        $grievances = Grievance::query()
            ->when($year, fn ($q) => $q->whereYear('created_at', $year))
            ->count();

        $resolved = Grievance::query()
            ->whereNotNull('resolved_at')
            ->when($year, fn ($q) => $q->whereYear('resolved_at', $year))
            ->count();

        $pending = Grievance::query()
            ->whereIn('status', [GrievanceStatus::Pending->value, GrievanceStatus::InProgress->value])
            ->count();

        return [
            'year' => $year,
            'registered' => $registered,
            'active' => $active,
            'grievances' => $grievances,
            'resolved' => $resolved,
            'pending' => $pending,
        ];
    }

    public function yearly(): array
    {
        $years = [];

        foreach ([2024, 2025, 2026] as $year) {
            $years[$year] = $this->summary($year);
        }

        $years['combined'] = $this->summary(null);

        return $years;
    }
}
