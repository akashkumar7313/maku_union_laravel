@extends('layouts.admin')

@section('title', $member->exists ? 'पदाधिकारी संपादित करें' : 'नया पदाधिकारी')
@section('heading', $member->exists ? 'पदाधिकारी संपादित करें' : 'नया पदाधिकारी')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $member->exists ? 'पदाधिकारी संपादित करें' : 'नया पदाधिकारी' }}</h1></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.team-members.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ $member->exists ? route('admin.team-members.update', $member) : route('admin.team-members.store') }}" method="POST" enctype="multipart/form-data" data-submit-once>
        @csrf
        @if ($member->exists) @method('PUT') @endif
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img class="rounded-circle" id="photoPreview" style="width: 100px; height: 100px; object-fit: cover;" src="{{ $member->photo ? upload_url($member->photo) : asset('assets/img/avatar-placeholder.svg') }}" alt="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="name">नाम <span class="required">*</span></label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $member->name) }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="position">पद</label>
                            <input class="form-control" id="position" name="position" value="{{ old('position', $member->position) }}" maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="photo">फ़ोटो</label>
                            <input class="form-control" id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="#photoPreview">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="sort_order">क्रम</label>
                                <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $member->sort_order) }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $member->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">सक्रिय</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.team-members.index') }}">रद्द करें</a>
                    <button class="btn btn-orange" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
