<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>प्रशासक लॉगिन | {{ setting('site_short_name', 'MAKU Union') }}</title>
    <link rel="icon" href="{{ setting_image_url('site_favicon', 'assets/img/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="login-head">
            <img src="{{ setting_image_url('site_logo', 'assets/img/logo.png') }}" alt="MAKU Union">
            <h1>प्रशासन पैनल</h1>
            <p>{{ setting('site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन') }}</p>
        </div>
        <div class="login-body">
            @if (session('success'))
                <div class="alert alert-success small">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger small">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('admin.login.attempt') }}" method="POST" data-submit-once>
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">ईमेल</label>
                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">पासवर्ड</label>
                    <input class="form-control" id="password" name="password" type="password" required autocomplete="current-password">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                    <label class="form-check-label small" for="remember">मुझे याद रखें</label>
                </div>
                <button class="btn btn-admin w-100" type="submit">लॉगिन करें</button>
            </form>
            <p class="text-center small text-muted mt-4 mb-0"><a href="{{ route('home') }}"><i class="bi bi-arrow-left me-1"></i>वेबसाइट पर लौटें</a></p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/admin.js') }}"></script>
</body>
</html>
