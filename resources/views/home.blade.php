@extends('layouts.public')

@section('title', setting('site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन').' | असंगठित श्रमिकों के अधिकारों के लिए')

@section('content')
    @php($heroBanners = $banners->isNotEmpty() ? $banners : collect([$banner]))
    <section class="hero hero-slider" data-hero-slider>
        <div class="hero-slides">
            @foreach ($heroBanners as $slide)
                <div class="hero-slide {{ $loop->first ? 'active' : '' }}" style="background-image: url('{{ upload_url($slide?->image, 'assets/img/hero.svg') }}');">
                    <div class="container">
                        <div class="hero-content">
                            <h1>
                                {{ $slide?->title ?: 'मजदूर का अधिकार' }}
                                @if (filled($slide?->highlight))
                                    <span class="highlight">{{ $slide->highlight }}</span>
                                @else
                                    <span class="highlight">हमारा संकल्प</span>
                                @endif
                            </h1>
                            <p>{{ $slide?->subtitle ?: setting('about_short', 'असंगठित एवं निर्माण श्रमिकों के अधिकारों, सम्मान और सामाजिक सुरक्षा के लिए समर्पित संगठन।') }}</p>
                            <div class="hero-actions d-flex flex-wrap gap-2 align-items-center">
                                <a class="btn btn-hero" href="{{ $slide?->button_url ? url($slide->button_url) : route('membership.register') }}">
                                    <i class="bi bi-person-plus-fill me-1"></i> {{ $slide?->button_text ?: 'सदस्य बनें' }}
                                </a>
                                <a class="btn btn-outline-light btn-hero-outline rounded-pill" href="{{ route('grievances.create') }}">श्रमिक सहायता</a>
                            </div>
                            <div class="hero-meta">
                                <span><i class="bi bi-shield-check me-1"></i> श्रमिक अधिकार</span>
                                <span><i class="bi bi-people me-1"></i> सामूहिक आवाज़</span>
                                <span><i class="bi bi-geo-alt me-1"></i> मिर्जापुर, उत्तर प्रदेश</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="hero-controls">
            <div class="container d-flex justify-content-end align-items-center gap-2">
                <button class="hero-arrow" type="button" data-hero-prev aria-label="पिछला बैनर"><i class="bi bi-chevron-left"></i></button>
                <div class="hero-dots" data-hero-dots aria-label="बैनर चयन"></div>
                <button class="hero-arrow" type="button" data-hero-next aria-label="अगला बैनर"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="action-grid">
            @forelse ($actionCards as $card)
                <a class="action-card {{ $card->color }}" href="{{ str_starts_with($card->url, 'http') ? $card->url : url($card->url) }}">
                    <span class="icon"><i class="bi {{ $card->icon ?: 'bi-arrow-right-circle' }}"></i></span>
                    <div class="action-text">
                        <h3>{{ $card->title }}</h3>
                        <p>{{ $card->description }}</p>
                    </div>
                </a>
            @empty
                <a class="action-card green" href="{{ route('membership.register') }}">
                    <span class="icon"><i class="bi bi-person-plus"></i></span>
                    <div class="action-text"><h3>सदस्य बनें</h3><p>सदस्यता के लिए पंजीकरण करें</p></div>
                </a>
                <a class="action-card blue" href="{{ route('membership.lookup') }}">
                    <span class="icon"><i class="bi bi-arrow-repeat"></i></span>
                    <div class="action-text"><h3>सदस्यता खोजें</h3><p>अपनी सदस्यता देखें</p></div>
                </a>
                <a class="action-card orange" href="{{ route('grievances.create') }}">
                    <span class="icon"><i class="bi bi-exclamation-circle"></i></span>
                    <div class="action-text"><h3>शिकायत दर्ज करें</h3><p>अपनी समस्या दर्ज करें</p></div>
                </a>
            @endforelse
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6">
                    <div class="section-heading mb-3">
                        <span class="eyebrow">हमारे बारे में</span>
                        <h2>श्रमिकों के सम्मान और अधिकारों के लिए</h2>
                    </div>
                    <p>{{ setting('about_short', 'मिर्जापुर असंगठित कामगार यूनियन (MAKU) असंगठित एवं निर्माण श्रमिकों के हितों की रक्षा और उनके अधिकारों के प्रति जागरूकता के लिए कार्य करती है।') }}</p>
                    <ul class="about-points">
                        <li><i class="bi bi-check-circle-fill"></i><span>{{ setting('about_objective', 'श्रमिकों को उनके श्रम का सम्मान, उचित मजदूरी और सामाजिक सुरक्षा का अधिकार।') }}</span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>{{ setting('about_mission', 'श्रमिकों को संगठित करना और उनकी समस्याओं को प्रशासन तक पहुँचाना।') }}</span></li>
                        <li><i class="bi bi-check-circle-fill"></i><span>{{ setting('about_vision', 'हर असंगठित श्रमिक सुरक्षित, सम्मानित और अपने अधिकारों के प्रति सजग हो।') }}</span></li>
                    </ul>
                    <a href="{{ route('pages.show', 'about-us') }}" class="btn btn-maku rounded-pill px-4">और जानें <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="col-lg-6">
                    <div class="about-media">
                        <img src="{{ upload_url($banner?->image, 'assets/img/hero.svg') }}" alt="श्रमिकों के अधिकारों के लिए संगठन" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-band">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number">{{ number_format($statistics['registered']) }}</div>
                    <div class="label">कुल पंजीकृत सदस्य</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ number_format($statistics['active']) }}</div>
                    <div class="label">सक्रिय सदस्य</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ number_format($statistics['grievances']) }}</div>
                    <div class="label">दर्ज शिकायतें</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ number_format($statistics['resolved']) }}</div>
                    <div class="label">निस्तारित शिकायतें</div>
                </div>
            </div>
            <p class="stat-footnote mb-0">ये आँकड़े संगठन के वास्तविक सदस्यता एवं शिकायत रिकॉर्ड से लिए गए हैं।</p>
        </div>
    </section>

    <section class="section section-alt">
        <div class="container">
            <div class="section-heading text-center">
                <span class="eyebrow">श्रमिक श्रेणियाँ</span>
                <h2>हम किन श्रमिकों के लिए कार्य करते हैं</h2>
                <p>असंगठित एवं निर्माण क्षेत्र के विभिन्न श्रमिक वर्गों के अधिकारों के लिए संगठन सक्रिय है।</p>
            </div>
            <div class="category-grid">
                @foreach ($categories as $category)
                    <article class="category-card">
                        <div class="category-media">
                            @if ($category->image)
                                <img src="{{ upload_url($category->image) }}" alt="{{ $category->name }}" loading="lazy">
                            @else
                                <i class="bi {{ $category->icon ?: 'bi-people' }}"></i>
                            @endif
                        </div>
                        <div class="category-body">
                            <h3>{{ $category->name }}</h3>
                            <p>{{ excerpt_hi($category->description, 110) }}</p>
                            <a class="link-arrow" href="{{ route('categories.show', $category) }}">और पढ़ें <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-maku rounded-pill px-4">सभी श्रेणियाँ देखें</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading text-center">
                <span class="eyebrow">हमारा संकल्प</span>
                <h2>संगठन का उद्देश्य एवं मिशन</h2>
            </div>
            <div class="row g-4">
                @foreach ($missionCards as $mission)
                    <div class="col-md-4">
                        <div class="mission-card">
                            <span class="icon"><i class="bi {{ $mission->icon ?: 'bi-bullseye' }}"></i></span>
                            <h3>{{ $mission->title }}</h3>
                            <p>{{ $mission->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section-alt">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-2">
                <div class="section-heading mb-4">
                    <span class="eyebrow">समाचार एवं गतिविधियाँ</span>
                    <h2>ताज़ा समाचार</h2>
                </div>
                <a href="{{ route('news.index') }}" class="btn btn-outline-maku rounded-pill px-3 mb-4">सभी समाचार</a>
            </div>
            @if ($newsItems->isNotEmpty())
                <div class="row g-4">
                    @foreach ($newsItems as $news)
                        <div class="col-md-6 col-lg-4">
                            <article class="news-card">
                                <div class="news-media">
                                    @if ($news->image)
                                        <img src="{{ upload_url($news->image) }}" alt="{{ $news->title }}" loading="lazy">
                                    @else
                                        <i class="bi bi-newspaper"></i>
                                    @endif
                                </div>
                                <div class="news-body">
                                    @if ($news->category)
                                        <span class="badge text-bg-light align-self-start mb-2">{{ $news->category->name }}</span>
                                    @endif
                                    <span class="news-date"><i class="bi bi-calendar3"></i> {{ hindi_date($news->published_at) }}</span>
                                    <h3><a href="{{ route('news.show', $news) }}">{{ $news->title }}</a></h3>
                                    <p>{{ $news->excerpt ?: excerpt_hi($news->body, 110) }}</p>
                                    <a class="link-arrow" href="{{ route('news.show', $news) }}">पूरा पढ़ें <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-newspaper"></i>
                    <p class="mb-0">अभी कोई समाचार प्रकाशित नहीं हुआ है।</p>
                </div>
            @endif
        </div>
    </section>

    @if ($teamMembers->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="section-heading text-center">
                    <span class="eyebrow">हमारी टीम</span>
                    <h2>संगठन के पदाधिकारी</h2>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ($teamMembers as $member)
                        <div class="col-6 col-md-4 col-lg-2">
                            <div class="text-center">
                                <img class="rounded-circle mb-3" src="{{ upload_url($member->photo) }}" alt="{{ $member->name }}" width="120" height="120" style="object-fit: cover;" loading="lazy">
                                <h3 class="h6 mb-1">{{ $member->name }}</h3>
                                <p class="small text-muted mb-0">{{ $member->position }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (setting('gallery_home_enabled', '1') === '1' && $galleries->isNotEmpty())
        <section class="section section-alt">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-2">
                    <div class="section-heading mb-4">
                        <span class="eyebrow">गैलरी</span>
                        <h2>कार्यक्रमों की झलकियाँ</h2>
                    </div>
                    <a href="{{ route('gallery') }}" class="btn btn-outline-maku rounded-pill px-3 mb-4">पूरी गैलरी</a>
                </div>
                <div class="row g-3" data-gallery>
                    @foreach ($galleries as $item)
                        <div class="col-6 col-lg-3">
                            @if ($item->type === 'video' && youtube_embed($item->video_url))
                                <div class="ratio ratio-4x3 rounded overflow-hidden">
                                    <iframe src="{{ youtube_embed($item->video_url) }}" title="{{ $item->title }}" loading="lazy" allowfullscreen></iframe>
                                </div>
                            @else
                                <a class="gallery-lightbox-item js-gallery-item d-block" href="{{ upload_url($item->file) }}" data-title="{{ $item->title }}" aria-label="{{ $item->title ?: 'गैलरी छवि' }} खोलें">
                                    <img class="w-100 rounded" style="height: 190px; object-fit: cover;" src="{{ upload_url($item->file) }}" alt="{{ $item->title }}" loading="lazy">
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-8">
                    <div class="section-heading mb-2">
                        <span class="eyebrow">श्रमिक सहायता</span>
                        <h2>अपनी समस्या या शिकायत दर्ज करें</h2>
                        <p>श्रमिक सहायता केंद्र के माध्यम से अपनी शिकायत दर्ज करें और उसकी स्थिति ऑनलाइन देखें। सदस्यता संख्या और मोबाइल नंबर से स्थिति जाँची जा सकती है।</p>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('grievances.create') }}" class="btn btn-orange rounded-pill px-4 me-2 mb-2">शिकायत दर्ज करें</a>
                    <a href="{{ route('grievances.track') }}" class="btn btn-outline-maku rounded-pill px-4 mb-2">स्थिति देखें</a>
                </div>
            </div>
        </div>
    </section>
@endsection
