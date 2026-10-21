@extends('layouts.admin')

@section('title', $news->exists ? 'समाचार संपादित करें' : 'नया समाचार')
@section('heading', $news->exists ? 'समाचार संपादित करें' : 'नया समाचार')
@section('subheading', $news->exists ? $news->title : 'नई प्रविष्टि')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $news->exists ? 'समाचार संपादित करें' : 'नया समाचार' }}</h1></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.news.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ $news->exists ? route('admin.news.update', $news) : route('admin.news.store') }}" method="POST" enctype="multipart/form-data" data-submit-once>
        @csrf
        @if ($news->exists) @method('PUT') @endif
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="title">शीर्षक <span class="required">*</span></label>
                            <input class="form-control" id="title" name="title" value="{{ old('title', $news->title) }}" required maxlength="200" data-slug-source>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="slug">स्लग</label>
                            <input class="form-control" id="slug" name="slug" value="{{ old('slug', $news->slug) }}" maxlength="200" data-slug-target>
                            <div class="form-text">खाली छोड़ने पर शीर्षक से बनेगा।</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="excerpt">संक्षिप्त परिचय</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="2" maxlength="1000">{{ old('excerpt', $news->excerpt) }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="body">विस्तृत विवरण <span class="required">*</span></label>
                            <textarea class="form-control" id="body" name="body" rows="16" required>{{ old('body', $news->body) }}</textarea>
                            <div class="form-text">सामान्य टेक्स्ट एवं सुरक्षित HTML टैग समर्थित हैं।</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-admin mb-4">
                    <div class="card-header">प्रकाशन</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="status">स्थिति</label>
                            <select class="form-select" id="status" name="status" required>
                                @foreach (['draft' => 'ड्राफ्ट', 'published' => 'प्रकाशित', 'archived' => 'संग्रहीत'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $news->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="news_category_id">श्रेणी</label>
                            <select class="form-select" id="news_category_id" name="news_category_id">
                                <option value="">कोई नहीं</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('news_category_id', $news->news_category_id) === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="published_at">प्रकाशन तिथि</label>
                            <input class="form-control" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $news->published_at?->format('Y-m-d\TH:i')) }}">
                            <div class="form-text">प्रकाशित करते समय खाली छोड़ने पर अभी की तिथि लगेगी।</div>
                        </div>
                    </div>
                </div>
                <div class="card-admin mb-4">
                    <div class="card-header">चित्र</div>
                    <div class="card-body">
                        @if ($news->image)
                            <img class="img-fluid rounded mb-3" id="imagePreview" src="{{ upload_url($news->image) }}" alt="">
                        @else
                            <img class="img-fluid rounded mb-3 d-none" id="imagePreview" alt="">
                        @endif
                        <label class="form-label" for="image">नया चित्र</label>
                        <input class="form-control" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="#imagePreview">
                        <div class="form-text">JPG, PNG या WEBP, अधिकतम 4 MB।</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary flex-fill" href="{{ route('admin.news.index') }}">रद्द करें</a>
                    <button class="btn btn-orange flex-fill" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
