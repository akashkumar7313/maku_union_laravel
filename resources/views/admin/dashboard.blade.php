@extends('layouts.admin')

@section('title', 'डैशबोर्ड')
@section('heading', 'डैशबोर्ड')
@section('subheading', 'संगठन की समग्र स्थिति')

@section('content')
    <div class="page-heading">
        <div>
            <h1>नमस्ते, {{ auth()->user()->name }}</h1>
            <p>आज {{ hindi_date(now()) }} — संगठन की ताज़ा स्थिति नीचे देखें।</p>
        </div>
        <div class="actions">
            <a class="btn btn-sm btn-orange" href="{{ route('admin.members.create') }}"><i class="bi bi-person-plus me-1"></i> नया सदस्य</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart me-1"></i> रिपोर्ट</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card-admin">
            <span class="icon blue"><i class="bi bi-people"></i></span>
            <div><div class="value">{{ number_format($stats['registered']) }}</div><div class="label">पंजीकृत सदस्य</div></div>
        </div>
        <div class="stat-card-admin">
            <span class="icon green"><i class="bi bi-person-check"></i></span>
            <div><div class="value">{{ number_format($stats['active']) }}</div><div class="label">सक्रिय सदस्य</div></div>
        </div>
        <div class="stat-card-admin">
            <span class="icon orange"><i class="bi bi-exclamation-circle"></i></span>
            <div><div class="value">{{ number_format($stats['grievances']) }}</div><div class="label">कुल शिकायतें</div></div>
        </div>
        <div class="stat-card-admin">
            <span class="icon green"><i class="bi bi-check2-circle"></i></span>
            <div><div class="value">{{ number_format($stats['resolved']) }}</div><div class="label">निस्तारित शिकायतें</div></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-6 col-xl-3">
            <a class="stat-card-admin h-100 text-reset" href="{{ route('admin.members.index', ['status' => 'pending']) }}">
                <span class="icon orange"><i class="bi bi-hourglass-split"></i></span>
                <div><div class="value">{{ number_format($pendingMembers) }}</div><div class="label">लंबित सदस्य</div></div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="stat-card-admin h-100 text-reset" href="{{ route('admin.payments.index', ['status' => 'pending']) }}">
                <span class="icon orange"><i class="bi bi-credit-card"></i></span>
                <div><div class="value">{{ number_format($pendingPayments) }}</div><div class="label">लंबित भुगतान</div></div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="stat-card-admin h-100 text-reset" href="{{ route('admin.grievances.index') }}">
                <span class="icon red"><i class="bi bi-tools"></i></span>
                <div><div class="value">{{ number_format($openGrievances) }}</div><div class="label">खुली शिकायतें</div></div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a class="stat-card-admin h-100 text-reset" href="{{ route('admin.contact-messages.index', ['unread' => 1]) }}">
                <span class="icon navy"><i class="bi bi-envelope"></i></span>
                <div><div class="value">{{ number_format($unreadMessages) }}</div><div class="label">अपठित संदेश</div></div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-admin h-100">
                <div class="card-header">
                    <span>हाल में जुड़े सदस्य</span>
                    <a class="small" href="{{ route('admin.members.index') }}">सभी देखें</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-admin">
                        <thead><tr><th>सदस्य</th><th class="d-none d-sm-table-cell">सदस्यता संख्या</th><th class="d-none d-md-table-cell">श्रेणी</th><th>स्थिति</th></tr></thead>
                        <tbody>
                            @forelse ($recentMembers as $member)
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="{{ route('admin.members.show', $member) }}">{{ $member->name }}</a>
                                        <div class="small text-muted">{{ phone_display($member->phone) }}</div>
                                    </td>
                                    <td class="small d-none d-sm-table-cell">{{ $member->membership_no }}</td>
                                    <td class="small d-none d-md-table-cell">{{ $member->category?->name ?: '—' }}</td>
                                    <td><span class="badge text-bg-{{ $member->statusColor() }}">{{ $member->statusLabel() }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">अभी कोई सदस्य पंजीकृत नहीं है।</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card-admin h-100">
                <div class="card-header">
                    <span>हाल की शिकायतें</span>
                    <a class="small" href="{{ route('admin.grievances.index') }}">सभी देखें</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-admin">
                        <thead><tr><th>शिकायत</th><th>स्थिति</th></tr></thead>
                        <tbody>
                            @forelse ($recentGrievances as $grievance)
                                <tr>
                                    <td>
                                        <a class="fw-semibold" href="{{ route('admin.grievances.show', $grievance) }}">{{ excerpt_hi($grievance->subject, 46) }}</a>
                                        <div class="small text-muted">{{ $grievance->case_no }} · {{ hindi_date($grievance->created_at) }}</div>
                                    </td>
                                    <td><span class="badge text-bg-{{ $grievance->statusColor() }}">{{ $grievance->statusLabel() }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-4">अभी कोई शिकायत दर्ज नहीं है।</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card-admin mt-4">
        <div class="card-header">
            <span>वार्षिक आँकड़े</span>
            <span class="small text-muted">पंजीकरण, सक्रियता एवं शिकायत निवारण</span>
        </div>
        <div class="table-responsive">
            <table class="table table-admin mb-0">
                <thead>
                    <tr><th>वर्ष</th><th>पंजीकृत</th><th class="d-none d-sm-table-cell">सक्रिय</th><th>कुल शिकायतें</th><th class="d-none d-sm-table-cell">निस्तारित</th><th>लंबित</th></tr>
                </thead>
                <tbody>
                    @foreach ($yearly as $year => $row)
                        <tr>
                            <td class="fw-semibold">{{ $year === 'combined' ? 'सर्वकालिक' : $year }}</td>
                            <td>{{ number_format($row['registered']) }}</td>
                            <td class="d-none d-sm-table-cell">{{ number_format($row['active']) }}</td>
                            <td>{{ number_format($row['grievances']) }}</td>
                            <td class="d-none d-sm-table-cell">{{ number_format($row['resolved']) }}</td>
                            <td>{{ number_format($row['pending']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
