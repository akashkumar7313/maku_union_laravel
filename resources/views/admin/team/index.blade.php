@extends('layouts.admin')

@section('title', 'पदाधिकारी')
@section('heading', 'पदाधिकारी')
@section('subheading', 'संगठन की टीम')

@section('content')
    <div class="page-heading">
        <div><h1>पदाधिकारी</h1><p>{{ number_format($members->total()) }} प्रविष्टियाँ</p></div>
        <a class="btn btn-sm btn-orange" href="{{ route('admin.team-members.create') }}"><i class="bi bi-plus-lg me-1"></i> नया पदाधिकारी</a>
    </div>

    <div class="row g-4">
        @forelse ($members as $member)
            <div class="col-md-4 col-xl-3">
                <div class="card-admin h-100 text-center">
                    <div class="card-body">
                        <img class="rounded-circle mb-3" style="width: 96px; height: 96px; object-fit: cover;" src="{{ upload_url($member->photo) }}" alt="{{ $member->name }}">
                        <h2 class="h6 mb-1">{{ $member->name }}</h2>
                        <p class="small text-muted mb-2">{{ $member->position ?: '—' }}</p>
                        <span class="badge text-bg-{{ $member->is_active ? 'success' : 'secondary' }}">{{ $member->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span>
                        <div class="d-flex justify-content-center gap-1 mt-3">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.team-members.edit', $member) }}"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.team-members.destroy', $member) }}" method="POST" data-submit-once>
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="पदाधिकारी हटाएँ?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="empty-state"><i class="bi bi-person-badge"></i><p class="mb-0">कोई पदाधिकारी दर्ज नहीं है।</p></div></div>
        @endforelse
    </div>

    @if ($members->hasPages())
        <div class="mt-4">{{ $members->links() }}</div>
    @endif
@endsection
