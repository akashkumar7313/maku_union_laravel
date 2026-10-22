@extends('layouts.public')

@section('title', 'सदस्यता कार्ड | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'सदस्यता विवरण', 'subtitle' => 'आपका सदस्यता कार्ड एवं भुगतान इतिहास'])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="membership-card">
                        <div class="card-head">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ setting_image_url('site_logo', 'assets/img/logo.png') }}" alt="MAKU Union">
                                <div>
                                    <h2>{{ setting('site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन') }}</h2>
                                    <div class="meta">{{ setting('affiliation_hi', 'असंगठित श्रमिक संगठन') }}</div>
                                </div>
                            </div>
                            <span class="badge text-bg-{{ $member->statusColor() }}">{{ $member->statusLabel() }}</span>
                        </div>
                        <div class="card-body-maku">
                            <div class="d-flex gap-3 align-items-start">
                                <img class="member-photo" src="{{ $member->photoUrl() }}" alt="{{ $member->name }}">
                                <div class="flex-grow-1">
                                    <h3 class="h5 text-white mb-1">{{ $member->name }}</h3>
                                    <div class="meta mb-2">{{ $member->category?->name ?: 'श्रमिक सदस्य' }}</div>
                                    <div class="membership-number mb-2">{{ $member->membership_no }}</div>
                                    <div class="meta"><i class="bi bi-telephone me-1"></i>{{ $member->maskedPhone() }}</div>
                                </div>
                            </div>
                            <div class="row g-2 mt-3">
                                <div class="col-6">
                                    <div class="card-validity">
                                        <div class="meta">वैधता प्रारंभ</div>
                                        <strong>{{ hindi_date($member->valid_from) }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card-validity">
                                        <div class="meta">वैधता समाप्ति</div>
                                        <strong>{{ hindi_date($member->valid_until) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3 no-print">
                        <button class="btn btn-maku btn-sm" type="button" onclick="window.print()"><i class="bi bi-printer me-1"></i> कार्ड प्रिंट करें</button>
                        @if (in_array($member->status, ['active', 'expired'], true))
                            <a class="btn btn-orange btn-sm" href="{{ $renewUrl }}"><i class="bi bi-arrow-repeat me-1"></i> सदस्यता नवीनीकृत करें</a>
                        @endif
                        <a class="btn btn-outline-maku btn-sm" href="{{ route('grievances.create') }}"><i class="bi bi-exclamation-circle me-1"></i> शिकायत दर्ज करें</a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h5 mb-3">सदस्य जानकारी</h2>
                        <dl class="row mb-0 small">
                            <dt class="col-5">पिता / पति का नाम</dt>
                            <dd class="col-7">{{ $member->guardian_name ?: '—' }}</dd>
                            <dt class="col-5">जनपद</dt>
                            <dd class="col-7">{{ $member->district }}</dd>
                            <dt class="col-5">ब्लॉक / तहसील</dt>
                            <dd class="col-7">{{ $member->block ?: '—' }}</dd>
                            <dt class="col-5">पता</dt>
                            <dd class="col-7">{{ $member->address }}</dd>
                            <dt class="col-5">सदस्यता तिथि</dt>
                            <dd class="col-7">{{ hindi_date($member->joined_on) }}</dd>
                        </dl>
                    </div>

                    <div class="card-soft p-4">
                        <h2 class="h5 mb-3">भुगतान इतिहास</h2>
                        @if ($member->payments->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr><th>ऑर्डर</th><th>राशि</th><th>स्थिति</th><th>दिनांक</th><th></th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($member->payments as $item)
                                            <tr>
                                                <td class="small">{{ $item->order_no }}</td>
                                                <td>{{ inr($item->amount) }}</td>
                                                <td><span class="badge text-bg-{{ $item->statusColor() }}">{{ $item->statusLabel() }}</span></td>
                                                <td class="small">{{ hindi_date($item->paid_at ?: $item->created_at) }}</td>
                                                <td class="text-end">
                                                    @if ($item->status === 'paid')
                                                        <a class="btn btn-sm btn-outline-maku" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('membership.receipt', now()->addHour(), ['payment' => $item->order_no]) }}">रसीद</a>
                                                    @elseif ($item->status === 'pending')
                                                        <a class="btn btn-sm btn-orange" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('membership.payment', now()->addHour(), ['payment' => $item->order_no]) }}">भुगतान</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="small text-muted mb-0">अभी कोई भुगतान दर्ज नहीं है।</p>
                        @endif
                    </div>

                    @if ($member->renewals->isNotEmpty())
                        <div class="card-soft p-4 mt-4">
                            <h2 class="h5 mb-3">नवीनीकरण इतिहास</h2>
                            <ul class="list-unstyled small mb-0">
                                @foreach ($member->renewals as $renewal)
                                    <li class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                        <span>{{ hindi_date($renewal->valid_from) }} – {{ hindi_date($renewal->valid_until) }}</span>
                                        <strong>{{ inr($renewal->amount) }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
