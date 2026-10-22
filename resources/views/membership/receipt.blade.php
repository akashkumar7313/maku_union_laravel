@extends('layouts.public')

@section('title', 'भुगतान रसीद | '.setting('site_name_hi', config('app.name')))

@section('content')
    @php
        $gatewayLabels = [
            'cash' => 'नकद',
            'online' => 'ऑनलाइन',
            'upi' => 'UPI / QR',
            'bank' => 'बैंक खाता',
            'cashfree' => 'Cashfree',
            'sandbox' => 'परीक्षण भुगतान',
            'manual' => 'बैंक / UPI',
        ];
        $mediumLabels = ['upi' => 'UPI / QR', 'bank' => 'बैंक खाता'];
        $medium = data_get($payment->payload, 'payment_medium');
    @endphp

    @include('partials.page-hero', ['title' => 'भुगतान रसीद', 'subtitle' => 'सदस्यता शुल्क भुगतान की पुष्टि'])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-soft p-4 p-md-5">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 border-bottom pb-3 mb-3">
                            <div class="d-flex gap-3 align-items-center">
                                <img src="{{ setting_image_url('site_logo', 'assets/img/logo.png') }}" alt="MAKU Union" width="64" height="64">
                                <div>
                                    <h2 class="h5 mb-1">{{ setting('site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन') }}</h2>
                                    <div class="small text-muted">{{ setting('contact_address', 'मिर्जापुर, उत्तर प्रदेश') }}</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge text-bg-{{ $payment->statusColor() }}">{{ $payment->statusLabel() }}</span>
                                <div class="small text-muted mt-1">रसीद: {{ $payment->receipt_no ?: '—' }}</div>
                            </div>
                        </div>

                        <div class="row g-3 small">
                            <div class="col-md-6">
                                <div class="text-muted">सदस्य का नाम</div>
                                <div class="fw-semibold">{{ $member->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">सदस्यता संख्या</div>
                                <div class="fw-semibold">{{ $member->membership_no }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">ऑर्डर संख्या</div>
                                <div class="fw-semibold">{{ $payment->order_no }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">भुगतान दिनांक</div>
                                <div class="fw-semibold">{{ hindi_datetime($payment->paid_at) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">भुगतान प्रकार</div>
                                <div class="fw-semibold">{{ $payment->type === 'renewal' ? 'सदस्यता नवीनीकरण' : 'नई सदस्यता' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">भुगतान माध्यम / संदर्भ</div>
                                <div class="fw-semibold">
                                    {{ $gatewayLabels[$payment->gateway] ?? $payment->gateway }}
                                    @if ($medium)
                                        · {{ $mediumLabels[$medium] ?? $medium }}
                                    @endif
                                    {{ $payment->gateway_payment_id ? ' · '.$payment->gateway_payment_id : '' }}
                                    {{ $payment->utr ? ' · UTR '.$payment->utr : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="payment-summary mt-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">सदस्यता शुल्क</span>
                                <span>{{ inr($payment->amount) }}</span>
                            </div>
                            @if ($payment->hasOnlineFee())
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">ऑनलाइन सुविधा शुल्क</span>
                                    <span>{{ inr($payment->fee_amount) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">GST</span>
                                    <span>{{ inr($payment->tax_amount) }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                <span class="fw-semibold">भुगतान राशि</span>
                                <span class="fs-4 fw-bold text-success">{{ inr($payment->totalAmount()) }}</span>
                            </div>
                        </div>

                        @if ($payment->receipt_path)
                            <div class="mt-3">
                                <a class="btn btn-sm btn-outline-maku" href="{{ upload_url($payment->receipt_path) }}" target="_blank" rel="noopener">
                                    <i class="bi bi-receipt me-1"></i> अपलोड की गई भुगतान रसीद देखें
                                </a>
                            </div>
                        @endif

                        @if ($payment->status !== 'paid')
                            <div class="alert alert-warning mt-3 mb-0">
                                यह भुगतान अभी पूर्ण नहीं हुआ है। स्थिति: {{ $payment->statusLabel() }}।
                                @if ($payment->gateway === 'cash')
                                    नकद भुगतान अनुरोध दर्ज है; प्रशासक की स्वीकृति के बाद सदस्यता सक्रिय होगी।
                                @elseif ($payment->receipt_path)
                                    आपकी रसीद अपलोड है; प्रशासक सत्यापन के बाद सदस्यता सक्रिय होगी।
                                @elseif ($payment->utr)
                                    UTR दर्ज है; कार्यालय सत्यापन के बाद सदस्यता सक्रिय होगी।
                                @endif
                            </div>
                        @endif

                        <div class="d-flex flex-wrap gap-2 mt-4 no-print">
                            <button class="btn btn-maku btn-sm" type="button" onclick="window.print()"><i class="bi bi-printer me-1"></i> रसीद प्रिंट करें</button>
                            <a class="btn btn-outline-maku btn-sm" href="{{ route('membership.lookup') }}">सदस्यता देखें</a>
                            <a class="btn btn-outline-maku btn-sm" href="{{ route('home') }}">मुख्य पृष्ठ</a>
                        </div>
                        <p class="small text-muted mt-3 mb-0">यह कंप्यूटर जनित रसीद है। किसी भी प्रश्न के लिए ऑर्डर संख्या के साथ कार्यालय से संपर्क करें।</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
