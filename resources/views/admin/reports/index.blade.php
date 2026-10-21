@extends('layouts.admin')

@section('title', 'रिपोर्ट')
@section('heading', 'रिपोर्ट')
@section('subheading', 'सांख्यिकीय विवरण')

@section('content')
    <div class="page-heading">
        <div><h1>रिपोर्ट</h1><p>वर्षवार सदस्यता एवं शिकायत आँकड़े</p></div>
        <a class="btn btn-sm btn-admin" href="{{ route('admin.reports.export') }}"><i class="bi bi-download me-1"></i> CSV डाउनलोड</a>
    </div>

    <div class="card-admin mb-4">
        <div class="card-header">वर्षवार सारांश</div>
        <div class="table-responsive">
            <table class="table table-admin mb-0">
                <thead><tr><th>वर्ष</th><th>पंजीकृत सदस्य</th><th>सक्रिय सदस्य</th><th>कुल शिकायतें</th><th>समाधान</th><th>लंबित</th></tr></thead>
                <tbody>
                    @foreach ($yearly as $year => $row)
                        <tr>
                            <td class="fw-semibold">{{ $year === 'combined' ? 'संयुक्त (सर्वकालिक)' : $year }}</td>
                            <td>{{ number_format($row['registered']) }}</td>
                            <td>{{ number_format($row['active']) }}</td>
                            <td>{{ number_format($row['grievances']) }}</td>
                            <td>{{ number_format($row['resolved']) }}</td>
                            <td>{{ number_format($row['pending']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-admin h-100">
                <div class="card-header">कार्यक्षेत्रवार सदस्य ({{ $currentYear }})</div>
                <div class="card-body">
                    @forelse ($categories as $category)
                        @php($max = max(1, $categories->max('members_count')))
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $category->name_hi }}</span>
                                <span class="text-muted">{{ number_format($category->members_count) }} / सक्रिय {{ number_format($category->active_members_count) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-orange" style="width: {{ round($category->members_count / $max * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">कोई श्रेणी नहीं।</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card-admin mb-4">
                <div class="card-header">शिकायत स्थिति</div>
                <div class="card-body">
                    @forelse ($grievanceStatuses as $status => $total)
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <span class="badge text-bg-{{ \App\Enums\GrievanceStatus::tryFrom($status)?->color() ?? 'secondary' }}">{{ $statusLabels[$status] ?? $status }}</span>
                            <strong>{{ number_format($total) }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">कोई शिकायत दर्ज नहीं।</p>
                    @endforelse
                </div>
            </div>

            <div class="card-admin">
                <div class="card-header">मासिक पंजीकरण ({{ $currentYear }})</div>
                <div class="card-body">
                    @php($maxMonth = max(1, (int) $monthlyRegistrations->max()))
                    <div class="d-flex align-items-end gap-1" style="height: 120px;">
                        @for ($month = 1; $month <= 12; $month++)
                            @php($count = (int) ($monthlyRegistrations[$month] ?? 0))
                            <div class="flex-fill text-center" title="{{ $count }} सदस्य">
                                <div class="bg-orange rounded-top mx-auto" style="width: 100%; height: {{ max(4, round($count / $maxMonth * 100)) }}px; opacity: {{ $count ? 1 : .15 }};"></div>
                                <div class="small text-muted mt-1" style="font-size: .65rem;">{{ $month }}</div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
