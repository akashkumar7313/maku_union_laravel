@extends('layouts.public')

@section('title', 'सदस्यता पंजीकरण | '.setting('site_name_hi', config('app.name')))
@section('meta_description', 'MAKU Union सदस्यता के लिए ऑनलाइन पंजीकरण करें।')

@section('content')
    @include('partials.page-hero', ['title' => 'सदस्यता पंजीकरण', 'subtitle' => 'असंगठित श्रमिक संगठन से जुड़ें'])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-form">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h2 class="h4 mb-0">सदस्यता फॉर्म</h2>
                            <span class="badge text-bg-success fs-6">शुल्क: {{ inr($fee) }} / {{ setting('membership_duration_text', 'एक वर्ष') }}</span>
                        </div>
                        <p class="small text-muted">तारांकित (<span class="required">*</span>) फ़ील्ड अनिवार्य हैं। भुगतान के बाद सदस्यता सक्रिय होगी।</p>

                        @include('partials.errors')

                        <form action="{{ route('membership.store') }}" method="POST" enctype="multipart/form-data" data-submit-once>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">पूरा नाम <span class="required">*</span></label>
                                    <input class="form-control" id="name" name="name" value="{{ old('name') }}" required maxlength="150" autocomplete="name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="guardian_name">पिता / पति का नाम</label>
                                    <input class="form-control" id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" maxlength="150">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="phone">मोबाइल नंबर <span class="required">*</span></label>
                                    <input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required inputmode="numeric" maxlength="15" autocomplete="tel">
                                    <div class="form-hint">10 अंकों का मोबाइल नंबर डालें।</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">ईमेल (वैकल्पिक)</label>
                                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" maxlength="190" autocomplete="email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="gender">लिंग</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">चुनें</option>
                                        <option value="male" @selected(old('gender') === 'male')>पुरुष</option>
                                        <option value="female" @selected(old('gender') === 'female')>महिला</option>
                                        <option value="other" @selected(old('gender') === 'other')>अन्य</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="dob">जन्म तिथि</label>
                                    <input class="form-control" id="dob" name="dob" type="date" value="{{ old('dob') }}" max="{{ now()->subDay()->format('Y-m-d') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="address">पूरा पता <span class="required">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="2" required minlength="5" maxlength="500" autocomplete="street-address">{{ old('address') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="district">जनपद <span class="required">*</span></label>
                                    <input class="form-control" id="district" name="district" value="{{ old('district', 'मिर्जापुर') }}" required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="block">ब्लॉक / तहसील</label>
                                    <input class="form-control" id="block" name="block" value="{{ old('block') }}" maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="worker_category_id">श्रमिक श्रेणी <span class="required">*</span></label>
                                    <select class="form-select" id="worker_category_id" name="worker_category_id" required>
                                        <option value="">श्रेणी चुनें</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected((string) old('worker_category_id') === (string) $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="aadhaar_number">आधार संख्या (12 अंक) <span class="required">*</span></label>
                                    <input class="form-control" id="aadhaar_number" name="aadhaar_number" value="{{ old('aadhaar_number') }}" inputmode="numeric" maxlength="12" autocomplete="off" required>
                                    <div class="form-hint">पूरा 12 अंकों का आधार नंबर भरें।</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="photo">फ़ोटो</label>
                                    <input class="form-control" id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp">
                                    <div class="form-hint">JPG, PNG या WEBP, अधिकतम 2 MB।</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="id_document">पहचान दस्तावेज़</label>
                                    <input class="form-control" id="id_document" name="id_document" type="file" accept="image/jpeg,image/png,application/pdf">
                                    <div class="form-hint">JPG, PNG या PDF, अधिकतम 4 MB।</div>
                                </div>
                                <div class="col-12 d-none" aria-hidden="true">
                                    <label for="website">वेबसाइट</label>
                                    <input id="website" name="website" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" id="terms" name="terms" type="checkbox" value="1" required @checked(old('terms'))>
                                        <label class="form-check-label small" for="terms">
                                            मैं घोषणा करता/करती हूँ कि दी गई जानकारी सत्य है और संगठन के
                                            <a href="{{ route('pages.show', 'terms') }}" target="_blank">नियम एवं शर्तें</a> तथा
                                            <a href="{{ route('pages.show', 'privacy-policy') }}" target="_blank">गोपनीयता नीति</a> स्वीकार करता/करती हूँ। <span class="required">*</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-orange px-4" type="submit" data-loading-text="पंजीकरण हो रहा है...">पंजीकरण करें एवं भुगतान पर जाएँ</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h6 mb-3">सदस्यता से लाभ</h2>
                        <ul class="about-points mb-0">
                            <li><i class="bi bi-check-circle-fill"></i><span>संगठन की सदस्यता एवं पहचान पत्र।</span></li>
                            <li><i class="bi bi-check-circle-fill"></i><span>श्रमिक अधिकारों एवं योजनाओं की जानकारी।</span></li>
                            <li><i class="bi bi-check-circle-fill"></i><span>शिकायत दर्ज करने एवं स्थिति देखने की सुविधा।</span></li>
                            <li><i class="bi bi-check-circle-fill"></i><span>संगठन के कार्यक्रमों में भागीदारी।</span></li>
                        </ul>
                    </div>
                    <div class="card-soft p-4">
                        <h2 class="h6 mb-2">सहायता चाहिए?</h2>
                        <p class="small text-muted mb-3">पंजीकरण में समस्या होने पर कार्यालय से संपर्क करें।</p>
                        @if (filled(setting('contact_phone')))
                            <p class="small mb-2"><i class="bi bi-telephone-fill text-warning me-2"></i>{{ phone_display(setting('contact_phone')) }}</p>
                        @endif
                        @if (filled(setting('contact_email')))
                            <p class="small mb-3"><i class="bi bi-envelope-fill text-warning me-2"></i>{{ setting('contact_email') }}</p>
                        @endif
                        <hr>
                        <p class="small text-muted mb-2">पहले पंजीकरण कर लिया है और संख्या भूल गए?</p>
                        <a class="small" href="{{ route('membership.forgot') }}"><i class="bi bi-search me-1"></i> पंजीकरण संख्या खोजें</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
