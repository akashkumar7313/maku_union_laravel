@extends('layouts.public')

@section('title', 'शिकायत स्थिति | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'शिकायत स्थिति', 'subtitle' => 'अपनी दर्ज शिकायत की प्रगति देखें'])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card-form">
                        <h2 class="h4 mb-2">शिकायत खोजें</h2>
                        <p class="small text-muted">शिकायत संख्या और उसी मोबाइल नंबर से स्थिति देखें जिससे शिकायत दर्ज हुई थी।</p>
                        @include('partials.errors')
                        <form action="{{ route('grievances.track.submit') }}" method="POST" data-submit-once>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="case_no">शिकायत संख्या <span class="required">*</span></label>
                                <input class="form-control" id="case_no" name="case_no" value="{{ old('case_no') }}" required maxlength="40" placeholder="MAKU-GRV-2026-00001">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="phone">मोबाइल नंबर <span class="required">*</span></label>
                                <input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required inputmode="numeric" maxlength="15">
                            </div>
                            <button class="btn btn-maku w-100" type="submit" data-loading-text="खोजा जा रहा है...">स्थिति देखें</button>
                        </form>
                        <hr class="my-4">
                        <a class="small" href="{{ route('grievances.create') }}">नई शिकायत दर्ज करें <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
