@extends('layouts.admin')

@section('title', 'भुगतान')
@section('heading', 'भुगतान प्रबंधन')
@section('subheading', 'ऑनलाइन एवं नकद भुगतान सत्यापन')

@section('content')
    <div class="page-heading">
        <div><h1>भुगतान सूची</h1><p>कुल प्राप्त: <strong>{{ inr($totalPaid) }}</strong> · लंबित: {{ number_format($pendingCount) }}</p></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.payments.export', request()->query()) }}"><i class="bi bi-download me-1"></i> CSV निर्यात</a>
    </div>

    <div class="card-admin mb-4">
        <div class="card-body">
            <form class="filter-bar" method="GET" action="{{ route('admin.payments.index') }}">
                <div>
                    <label class="form-label" for="q">खोज</label>
                    <input class="form-control" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="ऑर्डर, रसीद, UTR, सदस्य">
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
                    <label class="form-label" for="type">प्रकार</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">सभी</option>
                        <option value="new" @selected(($filters['type'] ?? '') === 'new')>नया</option>
                        <option value="renewal" @selected(($filters['type'] ?? '') === 'renewal')>नवीनीकरण</option>
                    </select>
                </div>
                <button class="btn btn-admin" type="submit">खोजें</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.payments.index') }}">रीसेट</a>
            </form>
        </div>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin table-stack mb-0">
                <thead>
                    <tr><th>ऑर्डर / रसीद</th><th>सदस्य</th><th>प्रकार</th><th>राशि</th><th>गेटवे / UTR</th><th>स्थिति</th><th>तिथि</th><th class="text-end">कार्य</th></tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="small" data-label="ऑर्डर / रसीद">
                                {{ $payment->order_no }}
                                <div class="text-muted">{{ $payment->receipt_no ?: '—' }}</div>
                            </td>
                            <td class="small" data-label="सदस्य">
                                @if ($payment->member)
                                    <a href="{{ route('admin.members.show', $payment->member) }}">{{ $payment->member->name }}</a>
                                    <div class="text-muted">{{ $payment->member->membership_no }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small" data-label="प्रकार">{{ $payment->type === 'renewal' ? 'नवीनीकरण' : 'नया' }}</td>
                            <td class="fw-semibold" data-label="राशि">{{ inr($payment->totalAmount()) }}@if ($payment->hasOnlineFee())<div class="small text-muted">शुल्क {{ inr($payment->fee_amount) }} + GST {{ inr($payment->tax_amount) }}</div>@endif</td>
                            <td class="small" data-label="गेटवे / UTR">
                                {{ ['cash' => 'नकद', 'online' => 'ऑनलाइन', 'upi' => 'UPI / QR', 'bank' => 'बैंक खाता', 'manual' => 'बैंक / UPI'][$payment->gateway] ?? $payment->gateway }}{{ $payment->utr ? ' · '.$payment->utr : '' }}
                                @if ($payment->receipt_path)
                                    <div><a href="{{ upload_url($payment->receipt_path) }}" target="_blank" rel="noopener"><i class="bi bi-receipt me-1"></i>रसीद देखें</a></div>
                                @endif
                            </td>
                            <td data-label="स्थिति"><span class="badge text-bg-{{ $payment->statusColor() }}">{{ $payment->statusLabel() }}</span></td>
                            <td class="small" data-label="तिथि">{{ hindi_date($payment->paid_at ?: $payment->created_at) }}</td>
                            <td class="text-end actions">
                                @if ($payment->status === 'pending')
                                    <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="d-inline" data-submit-once>
                                        @csrf
                                        <button class="btn btn-sm btn-success" type="submit" data-confirm="यह भुगतान सत्यापित कर सदस्यता सक्रिय कर दी जाएगी। आगे बढ़ें?">सत्यापित करें</button>
                                    </form>
                                    <form action="{{ route('admin.payments.refund', $payment) }}" method="POST" class="d-inline" data-submit-once>
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="इस भुगतान को वापसी के रूप में चिह्नित करें?">वापसी</button>
                                    </form>
                                @elseif ($payment->status === 'paid')
                                    <form action="{{ route('admin.payments.refund', $payment) }}" method="POST" class="d-inline" data-submit-once>
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="इस भुगतान को वापसी के रूप में चिह्नित करें?">वापसी चिह्नित करें</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">कोई भुगतान नहीं मिला।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($payments->hasPages())
            <div class="card-body border-top">{{ $payments->links() }}</div>
        @endif
    </div>
@endsection
