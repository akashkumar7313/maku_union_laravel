@extends('layouts.public')

@section('title', 'सदस्यता खोजें | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'सदस्यता खोजें', 'subtitle' => 'अपनी सदस्यता देखें और नवीनीकरण करें'])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card-form">
                        <h2 class="h4 mb-2">सदस्यता विवरण देखें</h2>
                        <p class="small text-muted">सदस्यता संख्या और पंजीकृत मोबाइल नंबर दर्ज करें।</p>
                        @include('partials.errors')
                        <form action="{{ route('membership.lookup.submit') }}" method="POST" data-submit-once>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="membership_no">सदस्यता संख्या <span class="required">*</span></label>
                                <input class="form-control" id="membership_no" name="membership_no" value="{{ old('membership_no') }}" required maxlength="30" placeholder="MAKU-2026-00001">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="phone">मोबाइल नंबर <span class="required">*</span></label>
                                <input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required inputmode="numeric" maxlength="15">
                            </div>
                            <button class="btn btn-maku w-100" type="submit" data-loading-text="खोजा जा रहा है...">सदस्यता देखें</button>
                        </form>
                        <hr class="my-4">
                        <p class="small text-muted mb-2">सदस्यता संख्या भूल गए हैं?</p>
                        <a class="small d-block mb-2" href="{{ route('membership.forgot') }}"><i class="bi bi-search me-1"></i> पंजीकरण संख्या खोजें</a>
                        <a class="small" href="{{ route('membership.register') }}">नया पंजीकरण करें <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
