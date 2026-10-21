@extends('layouts.admin')

@section('title', $category->exists ? 'श्रेणी संपादित करें' : 'नई श्रेणी')
@section('heading', $category->exists ? 'श्रेणी संपादित करें' : 'नई श्रेणी')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $category->exists ? 'श्रेणी संपादित करें' : 'नई श्रेणी' }}</h1></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.worker-categories.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ $category->exists ? route('admin.worker-categories.update', $category) : route('admin.worker-categories.store') }}" method="POST" enctype="multipart/form-data" data-submit-once>
        @csrf
        @if ($category->exists) @method('PUT') @endif
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">श्रेणी का नाम <span class="required">*</span></label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="100" data-slug-source>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="slug">स्लग</label>
                            <input class="form-control" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="120" data-slug-target>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="description">विवरण</label>
                            <textarea class="form-control" id="description" name="description" rows="4" maxlength="2000">{{ old('description', $category->description) }}</textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="icon">बूटस्ट्रैप आइकन क्लास</label>
                                <input class="form-control" id="icon" name="icon" value="{{ old('icon', $category->icon) }}" maxlength="60" placeholder="bi-people">
                                <div class="form-text">उदाहरण: bi-hammer, bi-tools, bi-people</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="sort_order">क्रम</label>
                                <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $category->sort_order) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-admin mb-4">
                    <div class="card-header">चित्र</div>
                    <div class="card-body">
                        <img class="img-fluid rounded mb-3 {{ $category->image ? '' : 'd-none' }}" id="imagePreview" src="{{ $category->image ? upload_url($category->image) : '' }}" alt="">
                        <label class="form-label" for="image">श्रेणी चित्र</label>
                        <input class="form-control" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="#imagePreview">
                        <div class="form-text">JPG, PNG या WEBP, अधिकतम 2 MB।</div>
                    </div>
                </div>
                <div class="card-admin mb-4">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $category->is_active ?? true))>
                            <label class="form-check-label" for="is_active">सक्रिय</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary flex-fill" href="{{ route('admin.worker-categories.index') }}">रद्द करें</a>
                    <button class="btn btn-orange flex-fill" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
