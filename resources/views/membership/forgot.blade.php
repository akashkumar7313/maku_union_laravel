@extends('layouts.public')

@section('title', 'पंजीकरण संख्या खोजें | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'पंजीकरण संख्या खोजें', 'subtitle' => 'आधार और पंजीकृत मोबाइल नंबर से सदस्यता संख्या पाएँ'])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card-form">
                        <h2 class="h4 mb-2">संख्या भूल गए?</h2>
                        <p class="small text-muted">पंजीकरण के समय दिया गया आधार नंबर और मोबाइल नंबर भरें। हम आपकी सदस्यता संख्या और भुगतान स्थिति दिखाएँगे।</p>

                        @include('partials.errors')

                        <form action="{{ route('membership.forgot.submit') }}" method="POST" data-submit-once>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="aadhaar_number">आधार संख्या (12 अंक) <span class="required">*</span></label>
                                <input class="form-control" id="aadhaar_number" name="aadhaar_number" value="{{ old('aadhaar_number') }}" required inputmode="numeric" maxlength="12" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="phone">पंजीकृत मोबाइल नंबर <span class="required">*</span></label>
                                <input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required inputmode="numeric" maxlength="15" autocomplete="tel">
                            </div>
                            <button class="btn btn-maku w-100" type="submit" data-loading-text="खोजा जा रहा है...">संख्या खोजें</button>
                        </form>

                        <hr class="my-4">
                        <p class="small text-muted mb-0">संख्या याद है? <a href="{{ route('membership.lookup') }}">सदस्यता विवरण देखें</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
