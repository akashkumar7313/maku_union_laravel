@extends('layouts.public')

@section('title', 'खोज परिणाम: '.$query.' | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'खोज परिणाम', 'subtitle' => '"'.$query.'" के लिए परिणाम'])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-soft p-4 mb-4">
                        <h2 class="h5 mb-3">समाचार ({{ $newsItems->count() }})</h2>
                        @forelse ($newsItems as $news)
                            <article class="border-bottom pb-3 mb-3">
                                <h3 class="h6 mb-1"><a href="{{ route('news.show', $news) }}">{{ $news->title }}</a></h3>
                                <p class="small text-muted mb-1">{{ excerpt_hi($news->excerpt ?: $news->body, 140) }}</p>
                                <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i>{{ hindi_date($news->published_at) }}</span>
                            </article>
                        @empty
                            <p class="small text-muted mb-0">कोई समाचार नहीं मिला।</p>
                        @endforelse
                    </div>

                    <div class="card-soft p-4">
                        <h2 class="h5 mb-3">पृष्ठ एवं श्रेणियाँ ({{ $pages->count() + $categories->count() }})</h2>
                        @forelse ($pages as $page)
                            <article class="border-bottom pb-2 mb-2">
                                <a href="{{ route('pages.show', $page) }}"><i class="bi bi-file-text me-2"></i>{{ $page->title }}</a>
                                <p class="small text-muted mb-0 mt-1">{{ excerpt_hi($page->body, 110) }}</p>
                            </article>
                        @empty
                        @endforelse
                        @foreach ($categories as $category)
                            <article class="border-bottom pb-2 mb-2">
                                <a href="{{ route('categories.show', $category) }}"><i class="bi {{ $category->icon ?: 'bi-people' }} me-2"></i>{{ $category->name }}</a>
                                <p class="small text-muted mb-0 mt-1">{{ excerpt_hi($category->description, 110) }}</p>
                            </article>
                        @endforeach
                        @if ($pages->isEmpty() && $categories->isEmpty())
                            <p class="small text-muted mb-0">कोई पृष्ठ या श्रेणी नहीं मिली।</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-soft p-4">
                        <h2 class="h6 mb-3">नई खोज</h2>
                        <form action="{{ route('search') }}" method="GET">
                            <label class="visually-hidden" for="searchAgain">खोज शब्द</label>
                            <input class="form-control mb-2" id="searchAgain" type="search" name="q" value="{{ $query }}" required maxlength="100">
                            <button class="btn btn-maku w-100" type="submit">खोजें</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
