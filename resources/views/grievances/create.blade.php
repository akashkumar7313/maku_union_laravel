@extends('layouts.public')

@section('title', 'श्रमिक सहायता | '.setting('site_name_hi', config('app.name')))
@section('meta_description', 'MAKU Union श्रमिक सहायता केंद्र में अपनी समस्या या शिकायत दर्ज करें।')

@section('content')
    @include('partials.page-hero', ['title' => 'श्रमिक सहायता', 'subtitle' => 'अपनी समस्या दर्ज करें और समाधान की स्थिति देखें'])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-form">
                        <h2 class="h4 mb-2">शिकायत दर्ज करें</h2>
                        <p class="small text-muted">अपनी समस्या का विवरण दें। शिकायत संख्या और मोबाइल नंबर से आप बाद में स्थिति देख सकते हैं।</p>
                        @if (filled(setting('grievance_note')))
                            <div class="alert alert-light border small">{{ setting('grievance_note') }}</div>
                        @endif
                        @include('partials.errors')
                        <form action="{{ route('grievances.store') }}" method="POST" enctype="multipart/form-data" data-submit-once>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">आपका नाम <span class="required">*</span></label>
                                    <input class="form-control" id="name" name="name" value="{{ old('name') }}" required maxlength="150">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="phone">मोबाइल नंबर <span class="required">*</span></label>
                                    <input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required inputmode="numeric" maxlength="15">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">ईमेल (वैकल्पिक)</label>
                                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" maxlength="190">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="worker_category_id">श्रमिक श्रेणी</label>
                                    <select class="form-select" id="worker_category_id" name="worker_category_id">
                                        <option value="">चुनें</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected((string) old('worker_category_id') === (string) $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="subject">शिकायत का विषय <span class="required">*</span></label>
                                    <input class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required minlength="5" maxlength="200" placeholder="जैसे: मजदूरी का भुगतान नहीं मिला">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="description">समस्या का विवरण <span class="required">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="6" required minlength="20" maxlength="5000" placeholder="घटना का स्थान, दिनांक, संबंधित व्यक्ति/ठेकेदार और अन्य आवश्यक जानकारी लिखें।">{{ old('description') }}</textarea>
                                    <div class="form-hint">कम से कम 20 अक्षर।</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="documents">दस्तावेज़ (वैकल्पिक)</label>
                                    <input class="form-control" id="documents" name="documents[]" type="file" multiple accept="image/jpeg,image/png,application/pdf,.doc,.docx">
                                    <div class="form-hint">अधिकतम 5 फ़ाइलें; प्रत्येक 4 MB तक। JPG, PNG, PDF, DOC या DOCX।</div>
                                </div>
                                <div class="col-12 d-none" aria-hidden="true">
                                    <label for="website">वेबसाइट</label>
                                    <input id="website" name="website" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" id="consent" name="consent" type="checkbox" value="1" required @checked(old('consent'))>
                                        <label class="form-check-label small" for="consent">
                                            मैं पुष्टि करता/करती हूँ कि दी गई जानकारी सत्य है और संगठन को इसका उपयोग शिकायत निवारण हेतु अधिकृत करता/करती हूँ। <span class="required">*</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-orange px-4" type="submit" data-loading-text="दर्ज हो रहा है...">शिकायत दर्ज करें</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h6 mb-3">स्थिति देखें</h2>
                        <p class="small text-muted">पहले से दर्ज शिकायत की स्थिति देखने के लिए शिकायत संख्या और मोबाइल नंबर दर्ज करें।</p>
                        <a class="btn btn-maku w-100" href="{{ route('grievances.track') }}">शिकायत स्थिति देखें</a>
                    </div>
                    <div class="card-soft p-4">
                        <h2 class="h6 mb-3">ध्यान दें</h2>
                        <ul class="small text-muted ps-3 mb-0">
                            <li class="mb-2">शिकायत दर्ज होने पर आपको एक शिकायत संख्या मिलेगी। उसे सुरक्षित रखें।</li>
                            <li class="mb-2">शिकायत की जानकारी केवल अधिकृत पदाधिकारियों को दी जाती है।</li>
                            <li>आपात स्थिति में स्थानीय प्रशासन या श्रम विभाग से भी संपर्क करें।</li>
                        </ul>
                        @if (filled(setting('grievance_helpline')))
                            <hr>
                            <p class="small mb-1"><i class="bi bi-telephone-fill text-warning me-2"></i>हेल्पलाइन: <a href="{{ phone_href(setting('grievance_helpline')) }}">{{ phone_display(setting('grievance_helpline')) }}</a></p>
                        @endif
                        @if (filled(setting('grievance_email')))
                            <p class="small mb-0"><i class="bi bi-envelope-fill text-warning me-2"></i><a href="mailto:{{ setting('grievance_email') }}">{{ setting('grievance_email') }}</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
