@extends('layouts.admin')

@section('title', $branch->exists ? 'शाखा संपादित करें' : 'नई शाखा')
@section('heading', $branch->exists ? 'शाखा संपादित करें' : 'नई शाखा')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $branch->exists ? 'शाखा संपादित करें' : 'नई शाखा' }}</h1></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.branches.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ $branch->exists ? route('admin.branches.update', $branch) : route('admin.branches.store') }}" method="POST" data-submit-once>
        @csrf
        @if ($branch->exists) @method('PUT') @endif
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">शाखा का नाम <span class="required">*</span></label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $branch->name) }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="address">पता</label>
                            <textarea class="form-control" id="address" name="address" rows="2" maxlength="500">{{ old('address', $branch->address) }}</textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="phone">मोबाइल</label>
                                <input class="form-control" id="phone" name="phone" value="{{ old('phone', $branch->phone) }}" inputmode="numeric" maxlength="15">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">ईमेल</label>
                                <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $branch->email) }}" maxlength="190">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label" for="map_url">मैप लिंक</label>
                            <input class="form-control" id="map_url" name="map_url" value="{{ old('map_url', $branch->map_url) }}" maxlength="255">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="sort_order">क्रम</label>
                                <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $branch->sort_order) }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $branch->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">सक्रिय</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.branches.index') }}">रद्द करें</a>
                    <button class="btn btn-orange" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
