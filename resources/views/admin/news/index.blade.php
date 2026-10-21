@extends('layouts.admin')

@section('title', 'समाचार')
@section('heading', 'समाचार')
@section('subheading', 'समाचार एवं गतिविधियाँ')

@section('content')
    <div class="page-heading">
        <div><h1>समाचार सूची</h1><p>कुल {{ number_format($newsItems->total()) }} रिकॉर्ड</p></div>
        <a class="btn btn-sm btn-orange" href="{{ route('admin.news.create') }}"><i class="bi bi-plus-lg me-1"></i> नया समाचार</a>
    </div>

    <div class="card-admin mb-4">
        <div class="card-body">
            <form class="filter-bar" method="GET" action="{{ route('admin.news.index') }}">
                <div>
                    <label class="form-label" for="q">खोज</label>
                    <input class="form-control" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="शीर्षक">
                </div>
                <div>
                    <label class="form-label" for="status">स्थिति</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">सभी</option>
                        @foreach (['draft' => 'ड्राफ्ट', 'published' => 'प्रकाशित', 'archived' => 'संग्रहीत'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-admin" type="submit">खोजें</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.news.index') }}">रीसेट</a>
            </form>
        </div>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin table-stack mb-0">
                <thead><tr><th>शीर्षक</th><th>श्रेणी</th><th>स्थिति</th><th>प्रकाशन</th><th>लेखक</th><th class="text-end">कार्य</th></tr></thead>
                <tbody>
                    @forelse ($newsItems as $news)
                        <tr>
                            <td data-label="शीर्षक">
                                <div class="d-flex align-items-center gap-2">
                                    @if ($news->image)
                                        <img class="thumb-sm" src="{{ upload_url($news->image) }}" alt="">
                                    @endif
                                    <div>
                                        <a class="fw-semibold" href="{{ route('admin.news.edit', $news) }}">{{ excerpt_hi($news->title, 60) }}</a>
                                        <div class="small text-muted">{{ $news->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="small" data-label="श्रेणी">{{ $news->category?->name ?: '—' }}</td>
                            <td data-label="स्थिति">
                                @php $sc = ['draft' => 'warning', 'published' => 'success', 'archived' => 'secondary'][$news->status] ?? 'secondary'; @endphp
                                <span class="badge text-bg-{{ $sc }}">{{ ['draft' => 'ड्राफ्ट', 'published' => 'प्रकाशित', 'archived' => 'संग्रहीत'][$news->status] ?? $news->status }}</span>
                            </td>
                            <td class="small" data-label="प्रकाशन">{{ hindi_date($news->published_at) }}</td>
                            <td class="small" data-label="लेखक">{{ $news->author?->name ?: '—' }}</td>
                            <td class="text-end actions">
                                @if ($news->status === 'published')
                                    <a class="btn btn-outline-secondary" target="_blank" rel="noopener" href="{{ route('news.show', $news) }}"><i class="bi bi-box-arrow-up-right"></i></a>
                                @endif
                                <a class="btn btn-outline-secondary" href="{{ route('admin.news.edit', $news) }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.news.destroy', $news) }}" method="POST" class="d-inline" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit" data-confirm="समाचार हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">कोई समाचार नहीं मिला।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($newsItems->hasPages())
            <div class="card-body border-top">{{ $newsItems->links() }}</div>
        @endif
    </div>
@endsection
