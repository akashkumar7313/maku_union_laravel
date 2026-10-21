@extends('layouts.admin')

@section('title', $member->name)
@section('heading', 'सदस्य विवरण')
@section('subheading', $member->membership_no)

@section('content')
    <div class="page-heading">
        <div>
            <h1>{{ $member->name }}</h1>
            <p>{{ $member->membership_no }} · {{ $member->category?->name ?: 'श्रेणी नहीं' }}</p>
        </div>
        <div class="actions">
            <a class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener" href="{{ $cardUrl }}"><i class="bi bi-person-vcard me-1"></i> डिजिटल कार्ड</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.members.edit', $member) }}"><i class="bi bi-pencil me-1"></i> संपादित करें</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.members.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-admin mb-4">
                <div class="card-header">व्यक्तिगत विवरण</div>
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <img class="avatar-sm" style="width: 84px; height: 84px;" src="{{ $member->photoUrl() }}" alt="{{ $member->name }}">
                        <div>
                            <h2 class="h5 mb-1">{{ $member->name }}</h2>
                            <p class="small text-muted mb-1">{{ $member->guardian_name ? 'पिता/पति: '.$member->guardian_name : '—' }}</p>
                            <span class="badge text-bg-{{ $member->statusColor() }}">{{ $member->statusLabel() }}</span>
                        </div>
                    </div>
                    <dl class="row detail-list mb-0">
                        <dt class="col-sm-4">मोबाइल</dt><dd class="col-sm-8">{{ phone_display($member->phone) }}</dd>
                        <dt class="col-sm-4">ईमेल</dt><dd class="col-sm-8">{{ $member->email ?: '—' }}</dd>
                        <dt class="col-sm-4">पता</dt><dd class="col-sm-8">{{ $member->address }}, {{ $member->block ? $member->block.', ' : '' }}{{ $member->district }}</dd>
                        <dt class="col-sm-4">लिंग / जन्म तिथि</dt><dd class="col-sm-8">{{ ['male' => 'पुरुष', 'female' => 'महिला', 'other' => 'अन्य'][$member->gender] ?? '—' }} · {{ hindi_date($member->dob) }}</dd>
                        <dt class="col-sm-4">सदस्यता तिथि</dt><dd class="col-sm-8">{{ hindi_date($member->joined_on) }}</dd>
                        <dt class="col-sm-4">वैधता</dt><dd class="col-sm-8">{{ hindi_date($member->valid_from) }} – {{ hindi_date($member->valid_until) }}</dd>
                        <dt class="col-sm-4">आधार संख्या</dt><dd class="col-sm-8">{{ $member->maskedAadhaar() }}</dd>
                        <dt class="col-sm-4">पंजीकरणकर्ता</dt><dd class="col-sm-8">{{ $member->creator?->name ?: 'स्वयं / ऑनलाइन' }}</dd>
                        @if ($member->notes)
                            <dt class="col-sm-4">टिप्पणी</dt><dd class="col-sm-8" style="white-space: pre-line;">{{ $member->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card-admin mb-4">
                <div class="card-header">भुगतान</div>
                <div class="table-responsive">
                    <table class="table table-admin mb-0">
                        <thead><tr><th>ऑर्डर</th><th>प्रकार</th><th>राशि</th><th>स्थिति</th><th>तिथि</th></tr></thead>
                        <tbody>
                            @forelse ($member->payments as $payment)
                                <tr>
                                    <td class="small">{{ $payment->order_no }}<div class="text-muted">{{ $payment->receipt_no }}</div></td>
                                    <td class="small">{{ $payment->type === 'renewal' ? 'नवीनीकरण' : 'नया' }}</td>
                                    <td>{{ inr($payment->totalAmount()) }}</td>
                                    <td><span class="badge text-bg-{{ $payment->statusColor() }}">{{ $payment->statusLabel() }}</span></td>
                                    <td class="small">{{ hindi_date($payment->paid_at ?: $payment->created_at) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">कोई भुगतान दर्ज नहीं है।</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($member->grievances->isNotEmpty())
                <div class="card-admin">
                    <div class="card-header">शिकायतें</div>
                    <div class="table-responsive">
                        <table class="table table-admin mb-0">
                            <thead><tr><th>शिकायत</th><th>विषय</th><th>स्थिति</th></tr></thead>
                            <tbody>
                                @foreach ($member->grievances as $grievance)
                                    <tr>
                                        <td class="small"><a href="{{ route('admin.grievances.show', $grievance) }}">{{ $grievance->case_no }}</a></td>
                                        <td class="small">{{ $grievance->subject }}</td>
                                        <td><span class="badge text-bg-{{ $grievance->statusColor() }}">{{ $grievance->statusLabel() }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card-admin mb-4">
                <div class="card-header">स्थिति बदलें</div>
                <div class="card-body">
                    <form action="{{ route('admin.members.status', $member) }}" method="POST" data-submit-once>
                        @csrf
                        <label class="form-label" for="status">नई स्थिति</label>
                        <select class="form-select mb-2" id="status" name="status" required>
                            @foreach (\App\Enums\MemberStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($member->status === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <label class="form-label" for="note">टिप्पणी</label>
                        <textarea class="form-control mb-3" id="note" name="note" rows="2" maxlength="1000" placeholder="कारण / टिप्पणी"></textarea>
                        <button class="btn btn-admin w-100" type="submit">स्थिति अपडेट करें</button>
                    </form>
                </div>
            </div>

            <div class="card-admin mb-4">
                <div class="card-header">भुगतान प्रविष्टि</div>
                <div class="card-body">
                    <form action="{{ route('admin.members.payment', $member) }}" method="POST" data-submit-once>
                        @csrf
                        <label class="form-label" for="type">प्रकार</label>
                        <select class="form-select mb-2" id="type" name="type" required>
                            <option value="renewal">नवीनीकरण</option>
                            <option value="new">नया भुगतान</option>
                        </select>
                        <div class="form-check mb-3">
                            <input class="form-check-input" id="mark_paid" name="mark_paid" type="checkbox" value="1">
                            <label class="form-check-label" for="mark_paid">कार्यालय में प्राप्त — तुरंत सफल चिह्नित करें</label>
                        </div>
                        <button class="btn btn-orange w-100" type="submit">भुगतान प्रविष्टि बनाएँ</button>
                    </form>
                </div>
            </div>

            <div class="card-admin">
                <div class="card-header">नवीनीकरण इतिहास</div>
                <div class="card-body">
                    @forelse ($member->renewals as $renewal)
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2 small">
                            <span>{{ hindi_date($renewal->valid_from) }} – {{ hindi_date($renewal->valid_until) }}</span>
                            <strong>{{ inr($renewal->amount) }}</strong>
                        </div>
                    @empty
                        <p class="small text-muted mb-0">अभी कोई नवीनीकरण नहीं हुआ।</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
