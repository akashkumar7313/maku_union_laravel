@extends('layouts.admin')

@section('title', 'शिकायतें')
@section('heading', 'शिकायत प्रबंधन')
@section('subheading', 'श्रमिकों की शिकायतें एवं निवारण')

@section('content')
    <div class="page-heading">
        <div><h1>शिकायतें</h1><p>कुल {{ number_format($grievances->total()) }} रिकॉर्ड</p></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grievances.export', request()->query()) }}"><i class="bi bi-download me-1"></i> CSV निर्यात</a>
    </div>

    <div class="card-admin mb-4">
        <div class="card-body">
            <form class="filter-bar" method="GET" action="{{ route('admin.grievances.index') }}">
                <div>
                    <label class="form-label" for="q">खोज</label>
                    <input class="form-control" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="शिकायत संख्या, नाम, मोबाइल, विषय">
                </div>
                <div>
                    <label class="form-label" for="status">स्थिति</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">सभी</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="priority">प्राथमिकता</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">सभी</option>
                        @foreach (['low' => 'कम', 'normal' => 'सामान्य', 'high' => 'उच्च', 'urgent' => 'अत्यावश्यक'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-admin" type="submit">खोजें</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.grievances.index') }}">रीसेट</a>
            </form>
        </div>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin table-stack mb-0">
                <thead>
                    <tr><th>शिकायत संख्या</th><th>शिकायतकर्ता</th><th>विषय</th><th>प्राथमिकता</th><th>स्थिति</th><th>दिनांक</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($grievances as $grievance)
                        <tr>
                            <td class="small" data-label="शिकायत संख्या"><a class="fw-semibold" href="{{ route('admin.grievances.show', $grievance) }}">{{ $grievance->case_no }}</a></td>
                            <td class="small" data-label="शिकायतकर्ता">
                                {{ $grievance->name }}
                                <div class="text-muted">{{ phone_display($grievance->phone) }}</div>
                            </td>
                            <td class="small" data-label="विषय">{{ excerpt_hi($grievance->subject, 50) }}</td>
                            <td data-label="प्राथमिकता">
                                @php $pc = ['low' => 'secondary', 'normal' => 'primary', 'high' => 'warning', 'urgent' => 'danger'][$grievance->priority] ?? 'secondary'; @endphp
                                <span class="badge text-bg-{{ $pc }}">{{ ['low' => 'कम', 'normal' => 'सामान्य', 'high' => 'उच्च', 'urgent' => 'अत्यावश्यक'][$grievance->priority] ?? $grievance->priority }}</span>
                            </td>
                            <td data-label="स्थिति"><span class="badge text-bg-{{ $grievance->statusColor() }}">{{ $grievance->statusLabel() }}</span></td>
                            <td class="small" data-label="दिनांक">{{ hindi_date($grievance->created_at) }}</td>
                            <td class="text-end actions"><a class="btn btn-outline-secondary" href="{{ route('admin.grievances.show', $grievance) }}"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">कोई शिकायत नहीं मिली।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($grievances->hasPages())
            <div class="card-body border-top">{{ $grievances->links() }}</div>
        @endif
    </div>
@endsection
