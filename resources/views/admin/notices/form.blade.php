@extends('layouts.admin')

@section('title', $notice->exists ? 'सूचना संपादित करें' : 'नई सूचना')
@section('heading', $notice->exists ? 'सूचना संपादित करें' : 'नई सूचना')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $notice->exists ? 'सूचना संपादित करें' : 'नई सूचना' }}</h1></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.notices.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ $notice->exists ? route('admin.notices.update', $notice) : route('admin.notices.store') }}" method="POST" data-submit-once>
        @csrf
        @if ($notice->exists) @method('PUT') @endif
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="title">शीर्षक <span class="required">*</span></label>
                            <input class="form-control" id="title" name="title" value="{{ old('title', $notice->title) }}" required maxlength="200">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="body">विवरण</label>
                            <textarea class="form-control" id="body" name="body" rows="3" maxlength="2000">{{ old('body', $notice->body) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="url">लिंक</label>
                            <input class="form-control" id="url" name="url" value="{{ old('url', $notice->url) }}" maxlength="255" placeholder="/membership/register">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="starts_at">प्रारंभ तिथि</label>
                                <input class="form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', $notice->starts_at?->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="ends_at">समाप्ति तिथि</label>
                                <input class="form-control" id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at', $notice->ends_at?->format('Y-m-d\TH:i')) }}">
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $notice->is_active ?? true))>
                            <label class="form-check-label" for="is_active">सक्रिय</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.notices.index') }}">रद्द करें</a>
                    <button class="btn btn-orange" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
