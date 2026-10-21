@extends('layouts.admin')

@section('title', 'गैलरी')
@section('heading', 'गैलरी')
@section('subheading', 'चित्र एवं वीडियो')

@section('content')
    <div class="page-heading">
        <div><h1>गैलरी</h1><p>{{ number_format($items->total()) }} आइटम</p></div>
        <a class="btn btn-sm btn-orange" href="{{ route('admin.gallery.create') }}"><i class="bi bi-plus-lg me-1"></i> नया आइटम</a>
    </div>

    <div class="row g-4">
        @forelse ($items as $item)
            <div class="col-md-4 col-xl-3">
                <div class="card-admin h-100">
                    @if ($item->type === 'video')
                        <div class="ratio ratio-16x9 rounded-top overflow-hidden bg-dark">
                            @if (youtube_embed($item->video_url))
                                <iframe src="{{ youtube_embed($item->video_url) }}" title="{{ $item->title }}" loading="lazy" allowfullscreen></iframe>
                            @else
                                <div class="d-flex align-items-center justify-content-center text-white"><i class="bi bi-camera-video"></i></div>
                            @endif
                        </div>
                    @elseif ($item->file)
                        <img class="w-100 rounded-top" style="height: 150px; object-fit: cover;" src="{{ upload_url($item->file) }}" alt="{{ $item->title }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-top" style="height: 150px;"><i class="bi bi-image text-muted fs-3"></i></div>
                    @endif
                    <div class="card-body">
                        <div class="fw-semibold">{{ $item->title ?: 'बिना शीर्षक' }}</div>
                        <div class="small text-muted">{{ $item->type === 'video' ? 'वीडियो' : 'चित्र' }} · क्रम {{ $item->sort_order }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="badge text-bg-{{ $item->is_active ? 'success' : 'secondary' }}">{{ $item->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span>
                            <div class="d-flex gap-1">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.gallery.edit', $item) }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="आइटम हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="empty-state"><i class="bi bi-images"></i><p class="mb-0">गैलरी खाली है।</p></div></div>
        @endforelse
    </div>

    @if ($items->hasPages())
        <div class="mt-4">{{ $items->links() }}</div>
    @endif
@endsection
