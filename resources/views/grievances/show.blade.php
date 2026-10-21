@extends('layouts.public')

@section('title', 'शिकायत '.$grievance->case_no.' | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'शिकायत विवरण', 'subtitle' => 'शिकायत संख्या: '.$grievance->case_no])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-soft p-4 p-md-5">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h1 class="h4 mb-1">{{ $grievance->subject }}</h1>
                                <div class="small text-muted">शिकायत संख्या: <span class="fw-semibold">{{ $grievance->case_no }}</span></div>
                            </div>
                            <span class="badge text-bg-{{ $grievance->statusColor() }} fs-6">{{ $grievance->statusLabel() }}</span>
                        </div>

                        <div class="row g-3 small mb-3">
                            <div class="col-md-6"><span class="text-muted">शिकायतकर्ता:</span> {{ $grievance->name }}</div>
                            <div class="col-md-6"><span class="text-muted">श्रेणी:</span> {{ $grievance->category?->name ?: '—' }}</div>
                            <div class="col-md-6"><span class="text-muted">दर्ज दिनांक:</span> {{ hindi_datetime($grievance->created_at) }}</div>
                            <div class="col-md-6"><span class="text-muted">अंतिम अद्यतन:</span> {{ hindi_datetime($grievance->updated_at) }}</div>
                        </div>

                        <h2 class="h6 mt-4">समस्या का विवरण</h2>
                        <p class="mb-0" style="white-space: pre-line;">{{ $grievance->description }}</p>

                        @if ($grievance->resolution_note)
                            <div class="alert alert-success mt-4 mb-0">
                                <strong><i class="bi bi-check-circle-fill me-1"></i> समाधान:</strong>
                                <div class="mt-1" style="white-space: pre-line;">{{ $grievance->resolution_note }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h6 mb-3">प्रगति</h2>
                        @if ($grievance->publicUpdates->isNotEmpty())
                            <ul class="timeline mb-0">
                                @foreach ($grievance->publicUpdates as $update)
                                    <li>
                                        <div class="fw-semibold">{{ \App\Enums\GrievanceStatus::tryFrom($update->status)?->label() ?? $update->status }}</div>
                                        <div class="small text-muted">{{ hindi_datetime($update->created_at) }}</div>
                                        @if ($update->note)
                                            <p class="small mb-0 mt-1" style="white-space: pre-line;">{{ $update->note }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="small text-muted mb-0">अभी कोई सार्वजनिक प्रगति अद्यतन उपलब्ध नहीं है। कार्यालय द्वारा शिकायत पर कार्य किया जा रहा है।</p>
                        @endif
                    </div>

                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        यह पेज सुरक्षित लिंक है। बाद में शिकायत संख्या और मोबाइल नंबर से
                        <a href="{{ route('grievances.track') }}">स्थिति देखें</a>।
                    </div>

                    <div class="card-soft p-4">
                        <h2 class="h6 mb-3">सहायता</h2>
                        @if (filled(setting('contact_phone')))
                            <p class="small mb-2"><i class="bi bi-telephone-fill text-warning me-2"></i><a href="{{ phone_href(setting('contact_phone')) }}">{{ phone_display(setting('contact_phone')) }}</a></p>
                        @endif
                        @if (filled(setting('contact_email')))
                            <p class="small mb-0"><i class="bi bi-envelope-fill text-warning me-2"></i><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
