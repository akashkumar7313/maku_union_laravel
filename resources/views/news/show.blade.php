@extends('layouts.public')

@section('title', $news->title.' | '.setting('site_name_hi', config('app.name')))
@section('meta_description', excerpt_hi($news->excerpt ?: $news->body, 155))

@section('content')
    @include('partials.page-hero', ['title' => $news->category?->name ?: 'समाचार', 'subtitle' => $news->title])

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="card-soft p-4 p-md-5">
                        <h1 class="h2 mb-3">{{ $news->title }}</h1>
                        <div class="d-flex flex-wrap gap-3 text-muted small mb-4">
                            <span><i class="bi bi-calendar3 me-1"></i>{{ hindi_date($news->published_at) }}</span>
                            @if ($news->author)
                                <span><i class="bi bi-person me-1"></i>{{ $news->author->name }}</span>
                            @endif
                            <span><i class="bi bi-eye me-1"></i>{{ number_format($news->views) }} बार देखा गया</span>
                        </div>
                        @if ($news->image)
                            <img class="img-fluid rounded mb-4" src="{{ upload_url($news->image) }}" alt="{{ $news->title }}">
                        @endif
                        @if ($news->excerpt)
                            <p class="lead">{{ $news->excerpt }}</p>
                        @endif
                        <div class="page-content">
                            {!! safe_html($news->body) !!}
                        </div>
                        <hr class="my-4">
                        <a class="btn btn-outline-maku btn-sm" href="{{ route('news.index') }}"><i class="bi bi-arrow-left me-1"></i> सभी समाचार</a>
                    </article>
                </div>
                <div class="col-lg-4">
                    @if ($related->isNotEmpty())
                        <div class="card-soft p-4">
                            <h2 class="h6 mb-3">संबंधित समाचार</h2>
                            @foreach ($related as $item)
                                <article class="border-bottom pb-2 mb-2">
                                    <a class="small fw-semibold" href="{{ route('news.show', $item) }}">{{ $item->title }}</a>
                                    <div class="text-muted" style="font-size: .75rem;">{{ hindi_date($item->published_at) }}</div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
