@extends('layouts.public')

@section('title', 'गैलरी | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'गैलरी', 'subtitle' => 'संगठन की गतिविधियों और कार्यक्रमों की झलकियाँ'])

    <section class="section">
        <div class="container">
            @if ($galleries->isNotEmpty())
                <div class="row g-4" data-gallery>
                    @foreach ($galleries as $item)
                        <div class="col-md-6 col-lg-4">
                            @if ($item->type === 'video' && youtube_embed($item->video_url))
                                <div class="ratio ratio-16x9 rounded overflow-hidden mb-2">
                                    <iframe src="{{ youtube_embed($item->video_url) }}" title="{{ $item->title }}" loading="lazy" allowfullscreen></iframe>
                                </div>
                            @else
                                <a class="gallery-lightbox-item js-gallery-item d-block" href="{{ upload_url($item->file) }}" data-title="{{ $item->title }}" aria-label="{{ $item->title ?: 'गैलरी छवि' }} खोलें">
                                    <img class="w-100 rounded" style="height: 240px; object-fit: cover;" src="{{ upload_url($item->file) }}" alt="{{ $item->title }}" loading="lazy">
                                </a>
                            @endif
                            @if ($item->title)
                                <p class="small text-muted mt-2 mb-0">{{ $item->title }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if ($galleries->hasPages())
                    <div class="mt-4">{{ $galleries->links() }}</div>
                @endif
            @else
                <div class="empty-state">
                    <i class="bi bi-images"></i>
                    <p class="mb-0">अभी गैलरी में कोई सामग्री उपलब्ध नहीं है।</p>
                </div>
            @endif
        </div>
    </section>
@endsection
