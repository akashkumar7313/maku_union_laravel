@extends('layouts.public')

@section('title', $category->name.' | '.setting('site_name_hi', config('app.name')))
@section('meta_description', excerpt_hi($category->description, 155))

@section('content')
    @include('partials.page-hero', ['title' => $category->name, 'subtitle' => 'श्रमिक श्रेणी एवं सहायता जानकारी'])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-soft p-4 p-md-5">
                        @if ($category->image)
                            <img class="img-fluid rounded mb-4" src="{{ upload_url($category->image) }}" alt="{{ $category->name }}">
                        @else
                            <div class="category-media rounded mb-4"><i class="bi {{ $category->icon ?: 'bi-people' }}"></i></div>
                        @endif
                        <p>{{ $category->description ?: 'इस श्रेणी से जुड़ी जानकारी शीघ्र उपलब्ध कराई जाएगी।' }}</p>
                        <div class="alert alert-light border mt-4 mb-0">
                            <i class="bi bi-info-circle me-2 text-primary"></i>
                            इस श्रेणी से जुड़े सक्रिय सदस्य: <strong>{{ number_format($memberCount) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h5 mb-2">सहायता चाहिए?</h2>
                        <p class="small text-muted">अपनी समस्या दर्ज करें या सदस्यता के लिए पंजीकरण करें।</p>
                        <div class="d-grid gap-2">
                            <a class="btn btn-orange btn-sm" href="{{ route('grievances.create') }}">शिकायत दर्ज करें</a>
                            <a class="btn btn-outline-maku btn-sm" href="{{ route('membership.register') }}">सदस्य बनें</a>
                        </div>
                    </div>
                    @if ($otherCategories->isNotEmpty())
                        <div class="card-soft p-4">
                            <h2 class="h6 mb-3">अन्य श्रेणियाँ</h2>
                            <div class="d-grid gap-2">
                                @foreach ($otherCategories as $other)
                                    <a class="d-flex align-items-center gap-2 small" href="{{ route('categories.show', $other) }}">
                                        <i class="bi {{ $other->icon ?: 'bi-people' }} text-warning"></i> {{ $other->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
