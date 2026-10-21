@extends('layouts.public')

@section('title', 'समाचार | '.setting('site_name_hi', config('app.name')))
@section('meta_description', 'MAKU Union के ताज़ा समाचार, गतिविधियाँ और सूचनाएँ।')

@section('content')
    @include('partials.page-hero', ['title' => 'समाचार एवं गतिविधियाँ', 'subtitle' => 'संगठन से जुड़ी ताज़ा जानकारी'])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a class="btn btn-sm {{ ! $activeCategory ? 'btn-maku' : 'btn-outline-maku' }}" href="{{ route('news.index') }}">सभी</a>
                        @foreach ($categories as $category)
                            <a class="btn btn-sm {{ $activeCategory?->id === $category->id ? 'btn-maku' : 'btn-outline-maku' }}"
                               href="{{ route('news.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                        @endforeach
                    </div>

                    @if ($newsItems->isNotEmpty())
                        <div class="row g-4">
                            @foreach ($newsItems as $news)
                                <div class="col-md-6">
                                    <article class="news-card">
                                        <div class="news-media">
                                            @if ($news->image)
                                                <img src="{{ upload_url($news->image) }}" alt="{{ $news->title }}" loading="lazy">
                                            @else
                                                <i class="bi bi-newspaper"></i>
                                            @endif
                                        </div>
                                        <div class="news-body">
                                            <span class="news-date"><i class="bi bi-calendar3"></i> {{ hindi_date($news->published_at) }}</span>
                                            <h3><a href="{{ route('news.show', $news) }}">{{ $news->title }}</a></h3>
                                            <p>{{ $news->excerpt ?: excerpt_hi($news->body, 120) }}</p>
                                            <a class="link-arrow" href="{{ route('news.show', $news) }}">पूरा पढ़ें <i class="bi bi-arrow-right"></i></a>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">{{ $newsItems->links() }}</div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-newspaper"></i>
                            <p class="mb-0">इस श्रेणी में अभी कोई समाचार उपलब्ध नहीं है।</p>
                        </div>
                    @endif
                </div>
                <div class="col-lg-4">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h6 mb-3">समाचार खोजें</h2>
                        <form action="{{ route('news.index') }}" method="GET">
                            <div class="input-group">
                                <label class="visually-hidden" for="newsSearch">खोज शब्द</label>
                                <input class="form-control" id="newsSearch" type="search" name="q" value="{{ $search }}" maxlength="100" placeholder="खोजें...">
                                <button class="btn btn-maku" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="card-soft p-4">
                        <h2 class="h6 mb-3">श्रेणियाँ</h2>
                        <div class="d-grid gap-2">
                            @foreach ($categories as $category)
                                <a class="small d-flex justify-content-between" href="{{ route('news.index', ['category' => $category->slug]) }}">
                                    <span>{{ $category->name }}</span><i class="bi bi-chevron-left"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
