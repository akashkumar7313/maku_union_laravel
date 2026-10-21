@extends('layouts.admin')

@section('title', $item->exists ? 'गैलरी संपादित करें' : 'नया गैलरी आइटम')
@section('heading', $item->exists ? 'गैलरी संपादित करें' : 'नया गैलरी आइटम')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $item->exists ? 'गैलरी संपादित करें' : 'नया गैलरी आइटम' }}</h1></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.gallery.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ $item->exists ? route('admin.gallery.update', $item) : route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" data-submit-once>
        @csrf
        @if ($item->exists) @method('PUT') @endif
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="title">शीर्षक</label>
                            <input class="form-control" id="title" name="title" value="{{ old('title', $item->title) }}" maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="type">प्रकार</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="image" @selected(old('type', $item->type) === 'image')>चित्र</option>
                                <option value="video" @selected(old('type', $item->type) === 'video')>वीडियो</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="file">चित्र</label>
                            <input class="form-control" id="file" name="file" type="file" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG या WEBP, अधिकतम 4 MB।</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="video_url">वीडियो URL (YouTube)</label>
                            <input class="form-control" id="video_url" name="video_url" value="{{ old('video_url', $item->video_url) }}" maxlength="255" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="sort_order">क्रम</label>
                                <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $item->sort_order) }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $item->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">सक्रिय</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.gallery.index') }}">रद्द करें</a>
                    <button class="btn btn-orange" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
