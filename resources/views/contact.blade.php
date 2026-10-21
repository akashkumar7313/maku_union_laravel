@extends('layouts.public')

@section('title', 'संपर्क करें | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'संपर्क करें', 'subtitle' => 'हमसे संपर्क करें या अपना संदेश भेजें'])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card-form">
                        <h2 class="h4 mb-3">हमें संदेश भेजें</h2>
                        @include('partials.errors')
                        <form action="{{ route('contact.store') }}" method="POST" data-submit-once>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">आपका नाम <span class="required">*</span></label>
                                    <input class="form-control" id="name" name="name" value="{{ old('name') }}" required maxlength="150">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="phone">मोबाइल नंबर</label>
                                    <input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" inputmode="numeric" maxlength="15">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">ईमेल</label>
                                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" maxlength="190">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="subject">विषय</label>
                                    <input class="form-control" id="subject" name="subject" value="{{ old('subject') }}" maxlength="200">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="message">संदेश <span class="required">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required minlength="10" maxlength="3000">{{ old('message') }}</textarea>
                                </div>
                                <div class="d-none" aria-hidden="true">
                                    <label for="website">वेबसाइट</label>
                                    <input id="website" name="website" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-maku px-4" type="submit" data-loading-text="भेजा जा रहा है...">संदेश भेजें</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h5 mb-3">संपर्क जानकारी</h2>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-3"><i class="bi bi-geo-alt-fill text-warning me-2"></i>{{ setting('contact_address', 'मिर्जापुर, उत्तर प्रदेश') }}</li>
                            @if (filled(setting('contact_phone')))
                                <li class="mb-3"><i class="bi bi-telephone-fill text-warning me-2"></i><a href="{{ phone_href(setting('contact_phone')) }}">{{ phone_display(setting('contact_phone')) }}</a></li>
                            @endif
                            @if (filled(setting('contact_phone_alt')))
                                <li class="mb-3"><i class="bi bi-telephone-fill text-warning me-2"></i><a href="{{ phone_href(setting('contact_phone_alt')) }}">{{ phone_display(setting('contact_phone_alt')) }}</a></li>
                            @endif
                            @if (filled(setting('contact_email')))
                                <li class="mb-3"><i class="bi bi-envelope-fill text-warning me-2"></i><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></li>
                            @endif
                            @if (filled(setting('contact_hours')))
                                <li class="mb-0"><i class="bi bi-clock-fill text-warning me-2"></i>{{ setting('contact_hours') }}</li>
                            @endif
                        </ul>
                    </div>
                    @if (filled(setting('map_embed')))
                        <div class="card-soft overflow-hidden">
                            {!! setting('map_embed') !!}
                        </div>
                    @elseif (filled(setting('map_link')))
                        <div class="card-soft p-4">
                            <h2 class="h6 mb-2">कार्यालय का पता</h2>
                            <p class="small text-muted mb-3">{{ setting('contact_address') }}</p>
                            <a class="btn btn-outline-maku btn-sm" href="{{ setting('map_link') }}" target="_blank" rel="noopener"><i class="bi bi-geo-alt me-1"></i> मैप पर देखें</a>
                        </div>
                    @endif
                </div>
            </div>

            @if ($branches->isNotEmpty())
                <div class="section-heading mt-5">
                    <span class="eyebrow">हमारी शाखाएँ</span>
                    <h2>क्षेत्रीय संपर्क केंद्र</h2>
                </div>
                <div class="row g-4">
                    @foreach ($branches as $branch)
                        <div class="col-md-6 col-lg-4">
                            <div class="card-soft p-4 h-100">
                                <h3 class="h6">{{ $branch->name }}</h3>
                                <p class="small text-muted mb-2">{{ $branch->address }}</p>
                                @if ($branch->phone)<p class="small mb-1"><i class="bi bi-telephone me-2"></i><a href="{{ phone_href($branch->phone) }}">{{ phone_display($branch->phone) }}</a></p>@endif
                                @if ($branch->email)<p class="small mb-0"><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a></p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($teamMembers->isNotEmpty())
                <div class="section-heading mt-5">
                    <span class="eyebrow">पदाधिकारी</span>
                    <h2>संगठन की टीम</h2>
                </div>
                <div class="row g-4">
                    @foreach ($teamMembers as $member)
                        <div class="col-6 col-md-4 col-lg-2 text-center">
                            <img class="rounded-circle mb-2" src="{{ upload_url($member->photo) }}" alt="{{ $member->name }}" width="100" height="100" style="object-fit: cover;" loading="lazy">
                            <h3 class="h6 mb-1">{{ $member->name }}</h3>
                            <p class="small text-muted mb-0">{{ $member->position }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
