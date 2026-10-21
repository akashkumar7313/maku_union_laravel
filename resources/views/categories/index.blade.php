@extends('layouts.public')

@section('title', 'श्रमिक श्रेणियाँ | '.setting('site_name_hi', config('app.name')))
@section('meta_description', 'MAKU Union द्वारा समर्थित विभिन्न असंगठित एवं निर्माण श्रमिक श्रेणियाँ।')

@section('content')
    @include('partials.page-hero', ['title' => 'श्रमिक श्रेणियाँ', 'subtitle' => 'असंगठित एवं निर्माण क्षेत्र के श्रमिक वर्ग'])

    <section class="section">
        <div class="container">
            <div class="category-grid">
                @forelse ($categories as $category)
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
                            <p>{{ excerpt_hi($category->description, 130) }}</p>
                            <a class="link-arrow" href="{{ route('categories.show', $category) }}">विवरण देखें <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </article>
                @empty
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <i class="bi bi-people"></i>
                        <p class="mb-0">अभी कोई श्रमिक श्रेणी उपलब्ध नहीं है।</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
