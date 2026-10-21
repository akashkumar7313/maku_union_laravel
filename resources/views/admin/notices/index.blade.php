@extends('layouts.admin')

@section('title', 'सूचनाएँ')
@section('heading', 'सूचनाएँ')
@section('subheading', 'वेबसाइट सूचना पट्टी')

@section('content')
    <div class="page-heading">
        <div><h1>सूचनाएँ</h1><p>कुल {{ number_format($notices->total()) }} सूचनाएँ</p></div>
        <a class="btn btn-sm btn-orange" href="{{ route('admin.notices.create') }}"><i class="bi bi-plus-lg me-1"></i> नई सूचना</a>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin mb-0">
                <thead><tr><th>शीर्षक</th><th>लिंक</th><th>प्रारंभ</th><th>समाप्ति</th><th>स्थिति</th><th class="text-end">कार्य</th></tr></thead>
                <tbody>
                    @forelse ($notices as $notice)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $notice->title }}</div>
                                <div class="small text-muted">{{ excerpt_hi($notice->body, 80) }}</div>
                            </td>
                            <td class="small">{{ $notice->url ?: '—' }}</td>
                            <td class="small">{{ $notice->starts_at ? hindi_datetime($notice->starts_at) : 'तुरंत' }}</td>
                            <td class="small">{{ $notice->ends_at ? hindi_datetime($notice->ends_at) : 'कोई सीमा नहीं' }}</td>
                            <td><span class="badge text-bg-{{ $notice->is_active ? 'success' : 'secondary' }}">{{ $notice->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span></td>
                            <td class="text-end actions">
                                <a class="btn btn-outline-secondary" href="{{ route('admin.notices.edit', $notice) }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" class="d-inline" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit" data-confirm="सूचना हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">कोई सूचना नहीं है।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($notices->hasPages())
            <div class="card-body border-top">{{ $notices->links() }}</div>
        @endif
    </div>
@endsection
