@extends('layouts.admin')

@section('title', 'बैनर')
@section('heading', 'होम बैनर')
@section('subheading', 'हीरो सेक्शन प्रबंधन')

@section('content')
    <div class="page-heading">
        <div><h1>बैनर</h1><p>सक्रिय बैनर में से पहला होमपेज पर दिखता है।</p></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @forelse ($banners as $banner)
                <div class="card-admin mb-4">
                    <div class="card-header">
                        <span>{{ $banner->title ?: 'बिना शीर्षक' }}</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-bg-{{ $banner->is_active ? 'success' : 'secondary' }}">{{ $banner->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#banner-{{ $banner->id }}"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" data-submit-once>
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="बैनर हटाएँ?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                @if ($banner->image)
                                    <img class="img-fluid rounded" src="{{ upload_url($banner->image) }}" alt="">
                                @else
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" style="height: 110px;"><i class="bi bi-image text-muted fs-3"></i></div>
                                @endif
                            </div>
                            <div class="col-md-8 small">
                                <div class="fw-semibold">{{ $banner->title }} <span class="text-warning">{{ $banner->highlight }}</span></div>
                                <p class="text-muted mb-1">{{ $banner->subtitle }}</p>
                                <div>बटन: {{ $banner->button_text ?: '—' }} → {{ $banner->button_url ?: '—' }}</div>
                                <div class="text-muted">क्रम: {{ $banner->sort_order }}</div>
                            </div>
                        </div>
                        <div class="collapse mt-3" id="banner-{{ $banner->id }}">
                            <hr>
                            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" data-submit-once>
                                @csrf @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label">शीर्षक</label><input class="form-control" name="title" value="{{ $banner->title }}" maxlength="200"></div>
                                    <div class="col-md-6"><label class="form-label">हाइलाइट</label><input class="form-control" name="highlight" value="{{ $banner->highlight }}" maxlength="200"></div>
                                    <div class="col-12"><label class="form-label">उपशीर्षक</label><textarea class="form-control" name="subtitle" rows="2" maxlength="500">{{ $banner->subtitle }}</textarea></div>
                                    <div class="col-md-6"><label class="form-label">बटन टेक्स्ट</label><input class="form-control" name="button_text" value="{{ $banner->button_text }}" maxlength="100"></div>
                                    <div class="col-md-6"><label class="form-label">बटन URL</label><input class="form-control" name="button_url" value="{{ $banner->button_url }}" maxlength="255"></div>
                                    <div class="col-md-6"><label class="form-label">चित्र</label><input class="form-control" name="image" type="file" accept="image/jpeg,image/png,image/webp"></div>
                                    <div class="col-md-3"><label class="form-label">क्रम</label><input class="form-control" name="sort_order" type="number" min="0" max="999" value="{{ $banner->sort_order }}"></div>
                                    <div class="col-md-3 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="ba{{ $banner->id }}" @checked($banner->is_active)><label class="form-check-label" for="ba{{ $banner->id }}">सक्रिय</label></div></div>
                                </div>
                                <button class="btn btn-admin mt-3" type="submit">अपडेट करें</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state"><i class="bi bi-image"></i><p class="mb-0">कोई बैनर नहीं है।</p></div>
            @endforelse

            @if ($banners->hasPages())
                <div class="mt-2">{{ $banners->links() }}</div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-admin">
                <div class="card-header">नया बैनर</div>
                <div class="card-body">
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" data-submit-once>
                        @csrf
                        <div class="mb-3"><label class="form-label" for="title">शीर्षक</label><input class="form-control" id="title" name="title" maxlength="200"></div>
                        <div class="mb-3"><label class="form-label" for="highlight">हाइलाइट</label><input class="form-control" id="highlight" name="highlight" maxlength="200"></div>
                        <div class="mb-3"><label class="form-label" for="subtitle">उपशीर्षक</label><textarea class="form-control" id="subtitle" name="subtitle" rows="2" maxlength="500"></textarea></div>
                        <div class="mb-3"><label class="form-label" for="button_text">बटन टेक्स्ट</label><input class="form-control" id="button_text" name="button_text" maxlength="100"></div>
                        <div class="mb-3"><label class="form-label" for="button_url">बटन URL</label><input class="form-control" id="button_url" name="button_url" maxlength="255"></div>
                        <div class="mb-3"><label class="form-label" for="image">चित्र</label><input class="form-control" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"></div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label" for="sort_order">क्रम</label><input class="form-control" id="sort_order" name="sort_order" type="number" min="0" max="999" value="0"></div>
                            <div class="col-6 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="newbanner" checked><label class="form-check-label" for="newbanner">सक्रिय</label></div></div>
                        </div>
                        <button class="btn btn-orange w-100" type="submit">बैनर जोड़ें</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
