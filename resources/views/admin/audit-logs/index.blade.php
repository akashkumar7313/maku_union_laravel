@extends('layouts.admin')

@section('title', 'ऑडिट लॉग')
@section('heading', 'ऑडिट लॉग')
@section('subheading', 'प्रशासनिक गतिविधियों का रिकॉर्ड')

@section('content')
    <div class="page-heading">
        <div><h1>ऑडिट लॉग</h1><p>कुल {{ number_format($logs->total()) }} प्रविष्टियाँ · प्रत्येक महत्वपूर्ण गतिविधि स्वतः दर्ज होती है</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.audit-logs.export', $filters) }}"><i class="bi bi-download me-1"></i> CSV निर्यात</a>
    </div>

    <div class="card-admin mb-4">
        <div class="card-body">
            <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.audit-logs.index') }}">
                <div class="col-md-3">
                    <label class="form-label" for="action">गतिविधि</label>
                    <select class="form-select" id="action" name="action">
                        <option value="">सभी गतिविधियाँ</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ \App\Models\AuditLog::labelFor($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="user">उपयोगकर्ता</label>
                    <select class="form-select" id="user" name="user">
                        <option value="">सभी</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) ($filters['user'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="from">से</label>
                    <input class="form-control" id="from" name="from" type="date" value="{{ $filters['from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="to">तक</label>
                    <input class="form-control" id="to" name="to" type="date" value="{{ $filters['to'] ?? '' }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-admin" type="submit">फ़िल्टर</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.audit-logs.index') }}">रीसेट</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin mb-0">
                <thead><tr><th>समय</th><th>उपयोगकर्ता</th><th>गतिविधि</th><th>लक्ष्य</th><th>विवरण</th></tr></thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="small text-nowrap">{{ hindi_datetime($log->created_at) }}</td>
                            <td class="small">{{ $log->user?->name ?? 'सिस्टम' }}</td>
                            <td>
                                <span class="badge text-bg-light border">{{ $log->actionLabel() }}</span>
                                <div><code class="small text-muted">{{ $log->action }}</code></div>
                            </td>
                            <td class="small">{{ $log->targetLabel() ?? '—' }}</td>
                            <td class="small">
                                @if ($log->old_values || $log->new_values)
                                    <details>
                                        <summary class="text-muted">बदलाव देखें</summary>
                                        <div class="mt-1">
                                            @foreach (collect($log->old_values ?? [])->keys()->merge(collect($log->new_values ?? [])->keys())->unique() as $field)
                                                <div class="text-break"><span class="text-muted">{{ $field }}:</span>
                                                    <span class="text-danger">{{ data_get($log->old_values, $field, '—') }}</span>
                                                    <i class="bi bi-arrow-right mx-1"></i>
                                                    <span class="text-success">{{ data_get($log->new_values, $field, '—') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">कोई लॉग नहीं मिला।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="card-body border-top">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection
