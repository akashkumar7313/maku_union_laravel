@extends('layouts.admin')

@section('title', 'मेरी प्रोफ़ाइल')
@section('heading', 'मेरी प्रोफ़ाइल')
@section('subheading', auth()->user()->email)

@section('content')
    <div class="page-heading">
        <div><h1>मेरी प्रोफ़ाइल</h1><p>खाता विवरण एवं पासवर्ड</p></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card-admin">
                <div class="card-header">व्यक्तिगत विवरण</div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" data-submit-once>
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label" for="name">नाम</label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">ईमेल</label>
                            <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="190">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="phone">मोबाइल</label>
                            <input class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" inputmode="numeric" maxlength="15">
                        </div>
                        <div class="small text-muted mb-3">भूमिका: {{ \App\Enums\Role::from($user->role)->label() }}</div>
                        <button class="btn btn-orange" type="submit">विवरण सहेजें</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-admin">
                <div class="card-header">पासवर्ड बदलें</div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.password') }}" method="POST" data-submit-once>
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label" for="current_password">वर्तमान पासवर्ड</label>
                            <input class="form-control" id="current_password" name="current_password" type="password" required autocomplete="current-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">नया पासवर्ड</label>
                            <input class="form-control" id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">नया पासवर्ड (पुष्टि)</label>
                            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                        </div>
                        <button class="btn btn-orange" type="submit">पासवर्ड बदलें</button>
                    </form>
                </div>
            </div>
            <div class="card-admin mt-4">
                <div class="card-body">
                    <dl class="detail-list mb-0">
                        <dt>अंतिम लॉगिन</dt><dd>{{ $user->last_login_at ? hindi_datetime($user->last_login_at) : '—' }}</dd>
                        <dt>खाता बना</dt><dd>{{ hindi_datetime($user->created_at) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
