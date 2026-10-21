@extends('layouts.admin')

@section('title', 'शिकायत '.$grievance->case_no)
@section('heading', 'शिकायत विवरण')
@section('subheading', $grievance->case_no)

@section('content')
    <div class="page-heading">
        <div>
            <h1>{{ $grievance->subject }}</h1>
            <p>{{ $grievance->case_no }} · {{ hindi_datetime($grievance->created_at) }}</p>
        </div>
        <div class="actions">
            <span class="badge text-bg-{{ $grievance->statusColor() }} fs-6">{{ $grievance->statusLabel() }}</span>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grievances.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-admin mb-4">
                <div class="card-header">शिकायत विवरण</div>
                <div class="card-body">
                    <dl class="row detail-list mb-0">
                        <dt class="col-sm-4">शिकायतकर्ता</dt><dd class="col-sm-8">{{ $grievance->name }}</dd>
                        <dt class="col-sm-4">मोबाइल</dt><dd class="col-sm-8">{{ phone_display($grievance->phone) }}</dd>
                        <dt class="col-sm-4">ईमेल</dt><dd class="col-sm-8">{{ $grievance->email ?: '—' }}</dd>
                        <dt class="col-sm-4">श्रेणी</dt><dd class="col-sm-8">{{ $grievance->category?->name ?: '—' }}</dd>
                        <dt class="col-sm-4">सदस्य</dt>
                        <dd class="col-sm-8">
                            @if ($grievance->member)
                                <a href="{{ route('admin.members.show', $grievance->member) }}">{{ $grievance->member->name }}</a> ({{ $grievance->member->membership_no }})
                            @else
                                —
                            @endif
                        </dd>
                        <dt class="col-sm-4">दर्ज IP</dt><dd class="col-sm-8">{{ $grievance->ip ?: '—' }}</dd>
                        <dt class="col-sm-4">विवरण</dt>
                        <dd class="col-sm-8" style="white-space: pre-line;">{{ $grievance->description }}</dd>
                        @if ($grievance->resolution_note)
                            <dt class="col-sm-4">समाधान टिप्पणी</dt><dd class="col-sm-8" style="white-space: pre-line;">{{ $grievance->resolution_note }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card-admin mb-4">
                <div class="card-header">प्रगति इतिहास</div>
                <div class="card-body">
                    @forelse ($grievance->updates as $update)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <strong>{{ \App\Enums\GrievanceStatus::tryFrom($update->status)?->label() ?? $update->status }}</strong>
                                <span class="small text-muted">{{ hindi_datetime($update->created_at) }} · {{ $update->user?->name ?: 'प्रणाली' }}</span>
                            </div>
                            @if ($update->note)
                                <p class="small mb-1 mt-1" style="white-space: pre-line;">{{ $update->note }}</p>
                            @endif
                            <span class="badge text-bg-{{ $update->is_public ? 'success' : 'secondary' }}">{{ $update->is_public ? 'सार्वजनिक' : 'आंतरिक' }}</span>
                        </div>
                    @empty
                        <p class="small text-muted mb-0">अभी कोई प्रगति प्रविष्टि नहीं है।</p>
                    @endforelse
                </div>
            </div>

            <div class="card-admin">
                <div class="card-header">दस्तावेज़ ({{ $grievance->documents->count() }})</div>
                <div class="card-body">
                    @forelse ($grievance->documents as $document)
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 gap-2">
                            <div class="small">
                                <i class="bi bi-paperclip"></i> {{ $document->original_name }}
                                <span class="text-muted">({{ number_format($document->size / 1024, 1) }} KB)</span>
                            </div>
                            <div class="d-flex gap-1">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.grievances.documents.download', [$grievance, $document]) }}"><i class="bi bi-download"></i></a>
                                <form action="{{ route('admin.grievances.documents.destroy', [$grievance, $document]) }}" method="POST" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="दस्तावेज़ हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="small text-muted mb-3">कोई दस्तावेज़ अपलोड नहीं है।</p>
                    @endforelse
                    <form action="{{ route('admin.grievances.documents.store', $grievance) }}" method="POST" enctype="multipart/form-data" class="mt-3" data-submit-once>
                        @csrf
                        <label class="form-label" for="document">नया दस्तावेज़</label>
                        <div class="input-group">
                            <input class="form-control" id="document" name="document" type="file" required accept="image/jpeg,image/png,application/pdf,.doc,.docx">
                            <button class="btn btn-admin" type="submit">अपलोड</button>
                        </div>
                        <div class="form-text">JPG, PNG, PDF, DOC या DOCX; अधिकतम 4 MB।</div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card-admin">
                <div class="card-header">शिकायत अद्यतन करें</div>
                <div class="card-body">
                    <form action="{{ route('admin.grievances.update', $grievance) }}" method="POST" data-submit-once>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="status">स्थिति</label>
                            <select class="form-select" id="status" name="status" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" @selected($grievance->status === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="priority">प्राथमिकता</label>
                            <select class="form-select" id="priority" name="priority" required>
                                @foreach (['low' => 'कम', 'normal' => 'सामान्य', 'high' => 'उच्च', 'urgent' => 'अत्यावश्यक'] as $value => $label)
                                    <option value="{{ $value }}" @selected($grievance->priority === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="assigned_to">अधिकारी</label>
                            <select class="form-select" id="assigned_to" name="assigned_to">
                                <option value="">कोई नहीं</option>
                                @foreach ($officers as $officer)
                                    <option value="{{ $officer->id }}" @selected((string) $grievance->assigned_to === (string) $officer->id)>{{ $officer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="note">अद्यतन टिप्पणी</label>
                            <textarea class="form-control" id="note" name="note" rows="4" maxlength="2000" placeholder="शिकायत पर की गई कार्रवाई लिखें"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" id="is_public" name="is_public" type="checkbox" value="1">
                            <label class="form-check-label" for="is_public">यह अद्यतन शिकायतकर्ता को दिखाएँ</label>
                        </div>
                        <button class="btn btn-admin w-100" type="submit">अद्यतन सहेजें</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
