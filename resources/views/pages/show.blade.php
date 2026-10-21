@extends('layouts.public')

@section('title', $page->title.' | '.setting('site_name_hi', config('app.name')))
@section('meta_description', excerpt_hi($page->subtitle ?: $page->body, 155))

@section('content')
    @include('partials.page-hero', ['title' => $page->title, 'subtitle' => $page->subtitle])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-soft p-4 p-md-5">
                        @if ($page->image)
                            <img class="img-fluid rounded mb-4" src="{{ upload_url($page->image) }}" alt="{{ $page->title }}">
                        @endif
                        <div class="page-content">
                            {!! safe_html($page->body) !!}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h5 mb-3">त्वरित लिंक</h2>
                        <div class="d-grid gap-2">
                            <a class="btn btn-outline-maku btn-sm" href="{{ route('membership.register') }}"><i class="bi bi-person-plus me-1"></i> सदस्य बनें</a>
                            <a class="btn btn-outline-maku btn-sm" href="{{ route('grievances.create') }}"><i class="bi bi-exclamation-circle me-1"></i> शिकायत दर्ज करें</a>
                            <a class="btn btn-outline-maku btn-sm" href="{{ route('membership.lookup') }}"><i class="bi bi-search me-1"></i> सदस्यता खोजें</a>
                            <a class="btn btn-outline-maku btn-sm" href="{{ route('contact') }}"><i class="bi bi-telephone me-1"></i> संपर्क करें</a>
                        </div>
                    </div>
                    <div class="card-soft p-4">
                        <h2 class="h5 mb-3">संपर्क जानकारी</h2>
                        <p class="small mb-2"><i class="bi bi-geo-alt-fill text-warning me-2"></i>{{ setting('contact_address', 'मिर्जापुर, उत्तर प्रदेश') }}</p>
                        @if (filled(setting('contact_phone')))
                            <p class="small mb-2"><i class="bi bi-telephone-fill text-warning me-2"></i><a href="{{ phone_href(setting('contact_phone')) }}">{{ phone_display(setting('contact_phone')) }}</a></p>
                        @endif
                        @if (filled(setting('contact_email')))
                            <p class="small mb-0"><i class="bi bi-envelope-fill text-warning me-2"></i><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></p>
                        @endif
                    </div>
                </div>
            </div>

            @if ($page->slug === 'about-us' && $teamMembers->isNotEmpty())
                <div class="section-heading text-center mt-5">
                    <span class="eyebrow">हमारी टीम</span>
                    <h2>संगठन के पदाधिकारी</h2>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ($teamMembers as $member)
                        <div class="col-6 col-md-4 col-lg-2 text-center">
                            <img class="rounded-circle mb-3" src="{{ upload_url($member->photo) }}" alt="{{ $member->name }}" width="120" height="120" style="object-fit: cover;" loading="lazy">
                            <h3 class="h6 mb-1">{{ $member->name }}</h3>
                            <p class="small text-muted mb-0">{{ $member->position }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($page->slug === 'organization' && $branches->isNotEmpty())
                <div class="section-heading mt-5">
                    <span class="eyebrow">हमारी शाखाएँ</span>
                    <h2>कार्यालय एवं संपर्क केंद्र</h2>
                </div>
                <div class="row g-4">
                    @foreach ($branches as $branch)
                        <div class="col-md-6">
                            <div class="card-soft p-4 h-100">
                                <h3 class="h6">{{ $branch->name }}</h3>
                                <p class="small text-muted mb-2">{{ $branch->address }}</p>
                                @if ($branch->phone)
                                    <p class="small mb-1"><i class="bi bi-telephone me-2"></i><a href="{{ phone_href($branch->phone) }}">{{ phone_display($branch->phone) }}</a></p>
                                @endif
                                @if ($branch->email)
                                    <p class="small mb-0"><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a></p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($page->slug === 'about-us' && $galleries->isNotEmpty())
                <div class="section-heading mt-5">
                    <span class="eyebrow">गैलरी</span>
                    <h2>हमारी गतिविधियाँ</h2>
                </div>
                <div class="row g-3" data-gallery>
                    @foreach ($galleries as $item)
                        <div class="col-6 col-md-3">
                            <a class="gallery-lightbox-item js-gallery-item d-block" href="{{ upload_url($item->file) }}" data-title="{{ $item->title }}" aria-label="{{ $item->title ?: 'गैलरी छवि' }} खोलें">
                                <img class="w-100 rounded" style="height: 160px; object-fit: cover;" src="{{ upload_url($item->file) }}" alt="{{ $item->title }}" loading="lazy">
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
