<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $metaTitle = trim($__env->yieldContent('title')) ?: setting('meta_title', setting('site_name_hi', config('app.name')));
        $metaDescription = trim($__env->yieldContent('meta_description')) ?: setting('meta_description', setting('about_short', setting('tagline_hi', 'असंगठित श्रमिकों के अधिकारों के लिए समर्पित संगठन')));
        $metaImage = setting_image_url('og_image', 'assets/img/hero.svg');
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ setting('meta_keywords', setting('site_keywords', 'MAKU Union, मिर्जापुर, असंगठित कामगार, श्रमिक यूनियन')) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ setting('site_short_name', 'MAKU Union') }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    <meta name="theme-color" content="#122a4a">
    @if (filled(setting('search_console_id')))
        <meta name="google-site-verification" content="{{ setting('search_console_id') }}">
    @endif
    <link rel="icon" href="{{ setting_image_url('site_favicon', 'assets/img/logo.png') }}">
    @if (filled(setting('google_analytics_id')))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode(setting('google_analytics_id')) }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json(setting('google_analytics_id')));
        </script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<a class="skip-link" href="#main-content">मुख्य सामग्री पर जाएँ</a>

<div class="topbar">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex flex-wrap align-items-center">
            @if (filled(setting('contact_phone')))
                <a class="topbar-item" href="{{ phone_href(setting('contact_phone')) }}"><i class="bi bi-telephone-fill"></i> {{ phone_display(setting('contact_phone')) }}</a>
            @endif
            @if (filled(setting('contact_email')))
                <a class="topbar-item" href="mailto:{{ setting('contact_email') }}"><i class="bi bi-envelope-fill"></i> {{ setting('contact_email') }}</a>
            @endif
            <span class="topbar-item topbar-address"><i class="bi bi-geo-alt-fill"></i> {{ setting('contact_address', 'मिर्जापुर, उत्तर प्रदेश') }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="topbar-item d-none d-md-inline"><i class="bi bi-patch-check-fill"></i> {{ setting('affiliation_hi', 'संबद्ध: भारतीय मजदूर संघ (BMS)') }}</span>
            <div class="social-links d-flex align-items-center">
                @if (filled(setting('facebook_url')))
                    <a href="{{ setting('facebook_url') }}" target="_blank" rel="noopener" aria-label="फेसबुक"><i class="bi bi-facebook"></i></a>
                @endif
                @if (filled(setting('youtube_url')))
                    <a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" aria-label="यूट्यूब"><i class="bi bi-youtube"></i></a>
                @endif
                @if (filled(setting('whatsapp_number')))
                    <a href="{{ whatsapp_url(setting('whatsapp_number')) }}" target="_blank" rel="noopener" aria-label="व्हाट्सएप"><i class="bi bi-whatsapp"></i></a>
                @endif
                @if (filled(setting('twitter_url')))
                    <a href="{{ setting('twitter_url') }}" target="_blank" rel="noopener" aria-label="एक्स"><i class="bi bi-twitter-x"></i></a>
                @endif
                @if (filled(setting('instagram_url')))
                    <a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" aria-label="इंस्टाग्राम"><i class="bi bi-instagram"></i></a>
                @endif
                @if (filled(setting('telegram_url')))
                    <a href="{{ setting('telegram_url') }}" target="_blank" rel="noopener" aria-label="टेलीग्राम"><i class="bi bi-telegram"></i></a>
                @endif
            </div>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <a class="brand" href="{{ route('home') }}">
                <img src="{{ setting_image_url('site_logo', 'assets/img/logo.png') }}" alt="{{ setting('site_name_hi', 'MAKU') }} लोगो">
                <span>
                    <span class="brand-title d-block">{{ setting('site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन') }}</span>
                    <span class="brand-subtitle d-block">{{ setting('tagline_hi', 'असंगठित एवं निर्माण श्रमिकों के अधिकारों के लिए समर्पित') }}</span>
                    @if (filled(setting('affiliation_hi')))
                        <span class="brand-affiliation d-block"><i class="bi bi-patch-check-fill"></i> {{ setting('affiliation_hi') }}</span>
                    @endif
                </span>
            </a>
            <form class="header-search w-100" action="{{ route('search') }}" method="GET" role="search">
                <div class="input-group">
                    <label class="visually-hidden" for="siteSearch">वेबसाइट पर खोजें</label>
                    <input class="form-control" id="siteSearch" type="search" name="q" value="{{ request('q') }}" placeholder="समाचार, जानकारी या श्रेणी खोजें..." maxlength="100" required>
                    <button class="btn" type="submit" aria-label="खोजें"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg navbar-maku" aria-label="मुख्य नेविगेशन">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="मेनू खोलें">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav w-100">
                @foreach ($menuItems as $item)
                    @php
                        $isActive = $item->route_name
                            ? request()->routeIs($item->route_name)
                            : ($item->url && request()->is(ltrim(parse_url($item->url, PHP_URL_PATH) ?: '/', '/')));
                    @endphp
                    <li class="nav-item {{ $item->children->isNotEmpty() ? 'dropdown' : '' }}">
                        <a class="nav-link {{ $isActive ? 'active' : '' }}"
                           href="{{ $item->href() }}"
                           @if ($item->opens_new_tab) target="_blank" rel="noopener" @endif
                           @if ($item->children->isNotEmpty()) role="button" data-bs-toggle="dropdown" aria-expanded="false" @endif>
                            {{ $item->label }}@if ($item->children->isNotEmpty()) <i class="bi bi-chevron-down small"></i>@endif
                        </a>
                        @if ($item->children->isNotEmpty())
                            <ul class="dropdown-menu">
                                @foreach ($item->children as $child)
                                    <li><a class="dropdown-item" href="{{ $child->href() }}" @if ($child->opens_new_tab) target="_blank" rel="noopener" @endif>{{ $child->label }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>

@include('partials.notice-slider')

<main id="main-content">
    @include('partials.flash')
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <img src="{{ setting_image_url('site_logo', 'assets/img/logo.png') }}" alt="{{ setting('site_name_hi', 'MAKU') }}">
                    <div>
                        <strong>{{ setting('site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन') }}</strong>
                        <span class="d-block small text-white-50">{{ setting('site_short_name', 'MAKU Union') }}</span>
                    </div>
                </div>
                <p class="small">{{ setting('about_short') ? excerpt_hi(setting('about_short'), 210) : setting('footer_note') }}</p>
                <div class="footer-social mt-3">
                    @if (filled(setting('facebook_url')))<a href="{{ setting('facebook_url') }}" target="_blank" rel="noopener" aria-label="फेसबुक"><i class="bi bi-facebook"></i></a>@endif
                    @if (filled(setting('youtube_url')))<a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" aria-label="यूट्यूब"><i class="bi bi-youtube"></i></a>@endif
                    @if (filled(setting('whatsapp_number')))<a href="{{ whatsapp_url(setting('whatsapp_number')) }}" target="_blank" rel="noopener" aria-label="व्हाट्सएप"><i class="bi bi-whatsapp"></i></a>@endif
                    @if (filled(setting('instagram_url')))<a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" aria-label="इंस्टाग्राम"><i class="bi bi-instagram"></i></a>@endif
                    @if (filled(setting('telegram_url')))<a href="{{ setting('telegram_url') }}" target="_blank" rel="noopener" aria-label="टेलीग्राम"><i class="bi bi-telegram"></i></a>@endif
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h4>त्वरित लिंक</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">मुख्य पृष्ठ</a></li>
                    <li><a href="{{ route('pages.show', 'about-us') }}">हमारे बारे में</a></li>
                    <li><a href="{{ route('membership.register') }}">सदस्य बनें</a></li>
                    <li><a href="{{ route('membership.lookup') }}">सदस्यता खोजें</a></li>
                    <li><a href="{{ route('grievances.create') }}">श्रमिक सहायता</a></li>
                    <li><a href="{{ route('news.index') }}">समाचार</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h4>श्रमिक श्रेणियाँ</h4>
                <ul class="footer-links">
                    @foreach ($navCategories->take(6) as $category)
                        <li><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-3">
                <h4>संपर्क</h4>
                <ul class="footer-contact">
                    <li><i class="bi bi-geo-alt-fill"></i> {{ setting('contact_address', 'मिर्जापुर, उत्तर प्रदेश') }}</li>
                    @if (filled(setting('contact_phone')))
                        <li><i class="bi bi-telephone-fill"></i> <a href="{{ phone_href(setting('contact_phone')) }}">{{ phone_display(setting('contact_phone')) }}</a></li>
                    @endif
                    @if (filled(setting('contact_email')))
                        <li><i class="bi bi-envelope-fill"></i> <a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></li>
                    @endif
                    @if (filled(setting('contact_hours')))
                        <li><i class="bi bi-clock-fill"></i> {{ setting('contact_hours') }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-wrap justify-content-between gap-2">
            <span>
                @if (filled(setting('copyright_text')))
                    {{ setting('copyright_text') }}
                @else
                    © <span data-current-year>{{ now()->year }}</span> {{ setting('site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन') }}. सर्वाधिकार सुरक्षित।
                @endif
                @if (filled(setting('developer_credit')))
                    <span class="text-white-50"> · {{ setting('developer_credit') }}</span>
                @endif
            </span>
            <span class="d-flex flex-wrap gap-3">
                @foreach ($footerPages as $footerPage)
                    <a href="{{ route('pages.show', $footerPage) }}">{{ $footerPage->title }}</a>
                @endforeach
                <a href="{{ route('gallery') }}">गैलरी</a>
            </span>
        </div>
    </div>
</footer>

@include('partials.gallery-lightbox')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/app.js') }}?v={{ filemtime(public_path('assets/js/app.js')) }}"></script>
@stack('scripts')
</body>
</html>
