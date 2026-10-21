@extends('layouts.public')

@section('title', 'सदस्यता नवीनीकरण | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'सदस्यता नवीनीकरण', 'subtitle' => 'अपनी सदस्यता एक वर्ष के लिए नवीनीकृत करें'])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card-form">
                        <h2 class="h4 mb-3">नवीनीकरण विवरण</h2>
                        @include('partials.errors')
                        <div class="payment-summary mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">सदस्य का नाम</span><strong>{{ $member->name }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">सदस्यता संख्या</span><strong>{{ $member->membership_no }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">वर्तमान वैधता</span><strong>{{ hindi_date($member->valid_until) }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fs-5">
                                <span class="fw-semibold">नवीनीकरण शुल्क</span>
                                <span class="fw-bold text-success">{{ inr($fee) }}</span>
                            </div>
                        </div>
                        <form action="{{ $action }}" method="POST" data-submit-once>
                            @csrf
                            <div class="alert alert-light border small">
                                भुगतान के बाद सदस्यता की वैधता वर्तमान अवधि की समाप्ति से एक वर्ष के लिए बढ़ा दी जाएगी।
                            </div>
                            <button class="btn btn-orange w-100" type="submit" data-loading-text="भुगतान हेतु तैयार हो रहा है...">
                                भुगतान करें एवं नवीनीकरण करें
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
