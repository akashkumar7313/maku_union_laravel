@extends('layouts.admin')

@section('title', $user->exists ? 'उपयोगकर्ता संपादित करें' : 'नया उपयोगकर्ता')
@section('heading', $user->exists ? 'उपयोगकर्ता संपादित करें' : 'नया उपयोगकर्ता')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $user->exists ? 'उपयोगकर्ता संपादित करें' : 'नया उपयोगकर्ता' }}</h1></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" data-submit-once>
        @csrf
        @if ($user->exists) @method('PUT') @endif
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="name">नाम <span class="required">*</span></label>
                                <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="150">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="phone">मोबाइल</label>
                                <input class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" inputmode="numeric" maxlength="15">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="email">ईमेल <span class="required">*</span></label>
                                <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="190">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="role">भूमिका <span class="required">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->value }}" @selected(old('role', $user->role) === $role->value)>{{ $role->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $user->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">सक्रिय खाता</label>
                                </div>
                            </div>
                            <div class="col-12"><hr class="my-1"></div>
                            <div class="col-md-6">
                                <label class="form-label" for="password">पासवर्ड {{ $user->exists ? '' : '*' }}</label>
                                <input class="form-control" id="password" name="password" type="password" @required(! $user->exists) autocomplete="new-password">
                                <div class="form-text">{{ $user->exists ? 'खाली छोड़ने पर पासवर्ड नहीं बदलेगा।' : 'कम से कम 8 अक्षर।' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="password_confirmation">पासवर्ड की पुष्टि</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">रद्द करें</a>
                    <button class="btn btn-orange" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
