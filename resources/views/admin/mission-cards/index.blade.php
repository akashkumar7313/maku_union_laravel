@extends('layouts.admin')

@section('title', 'मिशन कार्ड')
@section('heading', 'मिशन कार्ड')
@section('subheading', 'संगठन का उद्देश्य एवं मिशन')

@section('content')
    <div class="page-heading">
        <div><h1>मिशन कार्ड</h1><p>होमपेज के "हमारा संकल्प" सेक्शन में दिखते हैं।</p></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @foreach ($cards as $card)
                <div class="card-admin mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-warning fs-3"><i class="bi {{ $card->icon ?: 'bi-bullseye' }}"></i></span>
                                <div>
                                    <div class="fw-semibold">{{ $card->title }}</div>
                                    <div class="small text-muted">{{ $card->description }}</div>
                                    <div class="small text-muted">क्रम {{ $card->sort_order }} · <span class="badge text-bg-{{ $card->is_active ? 'success' : 'secondary' }}">{{ $card->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span></div>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#mission-{{ $card->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.mission-cards.destroy', $card) }}" method="POST" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="मिशन कार्ड हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="collapse mt-3" id="mission-{{ $card->id }}">
                            <hr>
                            <form action="{{ route('admin.mission-cards.update', $card) }}" method="POST" data-submit-once>
                                @csrf @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label">शीर्षक</label><input class="form-control" name="title" value="{{ $card->title }}" required maxlength="120"></div>
                                    <div class="col-md-3"><label class="form-label">आइकन</label><input class="form-control" name="icon" value="{{ $card->icon }}" maxlength="60"></div>
                                    <div class="col-md-3"><label class="form-label">क्रम</label><input class="form-control" name="sort_order" type="number" min="0" max="999" value="{{ $card->sort_order }}"></div>
                                    <div class="col-12"><label class="form-label">विवरण</label><textarea class="form-control" name="description" rows="2" maxlength="500">{{ $card->description }}</textarea></div>
                                    <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="mc{{ $card->id }}" @checked($card->is_active)><label class="form-check-label" for="mc{{ $card->id }}">सक्रिय</label></div></div>
                                </div>
                                <button class="btn btn-admin mt-3" type="submit">अपडेट करें</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-lg-4">
            <div class="card-admin">
                <div class="card-header">नया मिशन कार्ड</div>
                <div class="card-body">
                    <form action="{{ route('admin.mission-cards.store') }}" method="POST" data-submit-once>
                        @csrf
                        <div class="mb-3"><label class="form-label" for="title">शीर्षक</label><input class="form-control" id="title" name="title" required maxlength="120"></div>
                        <div class="mb-3"><label class="form-label" for="description">विवरण</label><textarea class="form-control" id="description" name="description" rows="3" maxlength="500"></textarea></div>
                        <div class="mb-3"><label class="form-label" for="icon">आइकन</label><input class="form-control" id="icon" name="icon" maxlength="60" placeholder="bi-bullseye"></div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label" for="sort_order">क्रम</label><input class="form-control" id="sort_order" name="sort_order" type="number" min="0" max="999" value="0"></div>
                            <div class="col-6 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="newmission" checked><label class="form-check-label" for="newmission">सक्रिय</label></div></div>
                        </div>
                        <button class="btn btn-orange w-100" type="submit">कार्ड जोड़ें</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
