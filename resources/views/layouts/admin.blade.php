<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'प्रशासन पैनल') | {{ setting('site_short_name', 'MAKU Union') }}</title>
    <link rel="icon" href="{{ setting_image_url('site_favicon', 'assets/img/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
@php
    $navUnread = \App\Models\ContactMessage::query()->whereNull('read_at')->count();
    $navGrievances = \App\Models\Grievance::query()->whereIn('status', ['pending', 'in_progress'])->count();
    $navPendingMembers = \App\Models\Member::query()->where('status', 'pending')->count();
    $navPendingPayments = \App\Models\MembershipPayment::query()->where('status', 'pending')->count();
@endphp

<aside class="admin-sidebar" id="adminSidebar">
    <a class="admin-brand" href="{{ route('admin.dashboard') }}">
        <img src="{{ setting_image_url('site_logo', 'assets/img/logo.png') }}" alt="MAKU Union">
        <span>
            <strong>{{ setting('site_short_name', 'MAKU Union') }} प्रशासन</strong>
            <span>{{ setting('site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन') }}</span>
        </span>
    </a>
    <nav class="admin-nav" aria-label="प्रशासन नेविगेशन">
        <div class="nav-section">मुख्य</div>
        <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> डैशबोर्ड</a>

        <div class="nav-section">सदस्यता</div>
        <a class="{{ request()->routeIs('admin.members.*') ? 'active' : '' }}" href="{{ route('admin.members.index') }}">
            <i class="bi bi-people"></i> सदस्य
            @if ($navPendingMembers)<span class="badge text-bg-warning">{{ $navPendingMembers }}</span>@endif
        </a>
        <a class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
            <i class="bi bi-credit-card"></i> भुगतान
            @if ($navPendingPayments)<span class="badge text-bg-warning">{{ $navPendingPayments }}</span>@endif
        </a>

        <div class="nav-section">श्रमिक सहायता</div>
        <a class="{{ request()->routeIs('admin.grievances.*') ? 'active' : '' }}" href="{{ route('admin.grievances.index') }}">
            <i class="bi bi-exclamation-circle"></i> शिकायतें
            @if ($navGrievances)<span class="badge text-bg-danger">{{ $navGrievances }}</span>@endif
        </a>

        <div class="nav-section">सामग्री</div>
        <a class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}"><i class="bi bi-newspaper"></i> समाचार</a>
        <a class="{{ request()->routeIs('admin.news-categories.*') ? 'active' : '' }}" href="{{ route('admin.news-categories.index') }}"><i class="bi bi-tags"></i> समाचार श्रेणियाँ</a>
        <a class="{{ request()->routeIs('admin.worker-categories.*') ? 'active' : '' }}" href="{{ route('admin.worker-categories.index') }}"><i class="bi bi-diagram-3"></i> श्रमिक श्रेणियाँ</a>
        <a class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}"><i class="bi bi-file-text"></i> पृष्ठ</a>
        <a class="{{ request()->routeIs('admin.menus.*') ? 'active' : '' }}" href="{{ route('admin.menus.index') }}"><i class="bi bi-list-nested"></i> मेन्यू</a>
        <a class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}"><i class="bi bi-image"></i> बैनर</a>
        <a class="{{ request()->routeIs('admin.action-cards.*') ? 'active' : '' }}" href="{{ route('admin.action-cards.index') }}"><i class="bi bi-grid-1x2"></i> एक्शन कार्ड</a>
        <a class="{{ request()->routeIs('admin.mission-cards.*') ? 'active' : '' }}" href="{{ route('admin.mission-cards.index') }}"><i class="bi bi-bullseye"></i> मिशन कार्ड</a>
        <a class="{{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" href="{{ route('admin.gallery.index') }}"><i class="bi bi-images"></i> गैलरी</a>
        <a class="{{ request()->routeIs('admin.notices.*') ? 'active' : '' }}" href="{{ route('admin.notices.index') }}"><i class="bi bi-megaphone"></i> सूचनाएँ</a>
        <a class="{{ request()->routeIs('admin.team-members.*') ? 'active' : '' }}" href="{{ route('admin.team-members.index') }}"><i class="bi bi-person-badge"></i> पदाधिकारी</a>
        <a class="{{ request()->routeIs('admin.branches.*') ? 'active' : '' }}" href="{{ route('admin.branches.index') }}"><i class="bi bi-geo-alt"></i> शाखाएँ</a>

        <div class="nav-section">संचार एवं रिपोर्ट</div>
        <a class="{{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}" href="{{ route('admin.contact-messages.index') }}">
            <i class="bi bi-envelope"></i> संपर्क संदेश
            @if ($navUnread)<span class="badge text-bg-info">{{ $navUnread }}</span>@endif
        </a>
        <a class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart"></i> रिपोर्ट</a>

        <div class="nav-section">प्रणाली</div>
        <a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}"><i class="bi bi-gear"></i> सेटिंग्स</a>
        <a class="{{ request()->routeIs('admin.translations.*') ? 'active' : '' }}" href="{{ route('admin.translations.index') }}"><i class="bi bi-translate"></i> अनुवाद</a>
        <a class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.edit') }}"><i class="bi bi-person-circle"></i> मेरी प्रोफ़ाइल</a>
        @if (auth()->user()?->isAdmin())
            <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-person-gear"></i> उपयोगकर्ता</a>
            <a class="{{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}"><i class="bi bi-journal-text"></i> ऑडिट लॉग</a>
        @endif
    </nav>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="admin-main">
    <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary sidebar-toggle" id="sidebarToggle" type="button" aria-label="मेनू खोलें">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <p class="page-title mb-0">@yield('heading', 'डैशबोर्ड')<small>@yield('subheading', 'MAKU Union प्रशासन पैनल')</small></p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex" href="{{ route('home') }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right me-1"></i> वेबसाइट</a>
            <div class="admin-user">
                <span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <div class="d-none d-sm-block">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">{{ auth()->user()->isAdmin() ? 'प्रशासक' : 'अधिकारी' }}</div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit" title="लॉगआउट"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </header>

    <div class="admin-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss>
                <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="बंद करें"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" data-auto-dismiss>
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="बंद करें"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <p class="fw-bold mb-1">कृपया निम्न त्रुटियाँ ठीक करें:</p>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="admin-footer d-flex flex-wrap justify-content-between gap-2">
        <span>© <span data-current-year>{{ now()->year }}</span> {{ setting('site_name_hi', 'MAKU') }}</span>
        <span>MAKU Union प्रशासन पैनल</span>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/admin.js') }}?v={{ filemtime(public_path('assets/js/admin.js')) }}"></script>
@stack('scripts')
</body>
</html>
