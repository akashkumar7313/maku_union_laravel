@extends('layouts.admin')

@section('title', 'पृष्ठ संपादित करें')
@section('heading', 'पृष्ठ संपादित करें')
@section('subheading', $page->title)

@section('content')
    <div class="page-heading">
        <div><h1>{{ $page->title }}</h1><p>स्लग: {{ $page->slug }}</p></div>
        <div class="actions">
            <a class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener" href="{{ route('pages.show', $page) }}"><i class="bi bi-box-arrow-up-right me-1"></i> देखें</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.pages.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
        </div>
    </div>

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" enctype="multipart/form-data" data-submit-once>
        @csrf @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="title">शीर्षक <span class="required">*</span></label>
                            <input class="form-control" id="title" name="title" value="{{ old('title', $page->title) }}" required maxlength="200">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="subtitle">उपशीर्षक</label>
                            <input class="form-control" id="subtitle" name="subtitle" value="{{ old('subtitle', $page->subtitle) }}" maxlength="300">
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="body">पृष्ठ सामग्री</label>
                            <textarea class="form-control" id="body" name="body" rows="18">{{ old('body', $page->body) }}</textarea>
                            <div class="form-text">सुरक्षित HTML समर्थित है। सामग्री केवल विश्वसनीय प्रशासकों द्वारा संपादित करें।</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-admin mb-4">
                    <div class="card-header">प्रकाशन</div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $page->is_active))>
                            <label class="form-check-label" for="is_active">पृष्ठ सक्रिय</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" id="show_in_footer" name="show_in_footer" type="checkbox" value="1" @checked(old('show_in_footer', $page->show_in_footer))>
                            <label class="form-check-label" for="show_in_footer">फुटर में दिखाएँ</label>
                        </div>
                    </div>
                </div>
                <div class="card-admin mb-4">
                    <div class="card-header">चित्र</div>
                    <div class="card-body">
                        @if ($page->image)
                            <img class="img-fluid rounded mb-3" id="imagePreview" src="{{ upload_url($page->image) }}" alt="">
                        @else
                            <img class="img-fluid rounded mb-3 d-none" id="imagePreview" alt="">
                        @endif
                        <label class="form-label" for="image">पृष्ठ चित्र</label>
                        <input class="form-control" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="#imagePreview">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary flex-fill" href="{{ route('admin.pages.index') }}">रद्द करें</a>
                    <button class="btn btn-orange flex-fill" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
