@extends('layouts.admin')

@section('title', 'शाखाएँ')
@section('heading', 'शाखाएँ')
@section('subheading', 'कार्यालय एवं संपर्क केंद्र')

@section('content')
    <div class="page-heading">
        <div><h1>शाखाएँ</h1><p>{{ number_format($branches->total()) }} शाखाएँ</p></div>
        <a class="btn btn-sm btn-orange" href="{{ route('admin.branches.create') }}"><i class="bi bi-plus-lg me-1"></i> नई शाखा</a>
    </div>

    <div class="row g-4">
        @forelse ($branches as $branch)
            <div class="col-md-6">
                <div class="card-admin h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h2 class="h6 mb-1">{{ $branch->name }}</h2>
                            <span class="badge text-bg-{{ $branch->is_active ? 'success' : 'secondary' }}">{{ $branch->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span>
                        </div>
                        <p class="small text-muted mb-2">{{ $branch->address ?: 'पता नहीं' }}</p>
                        <div class="small mb-3">
                            @if ($branch->phone)<div><i class="bi bi-telephone me-1"></i>{{ phone_display($branch->phone) }}</div>@endif
                            @if ($branch->email)<div><i class="bi bi-envelope me-1"></i>{{ $branch->email }}</div>@endif
                            @if ($branch->map_url)<div class="text-truncate"><i class="bi bi-geo me-1"></i>{{ $branch->map_url }}</div>@endif
                        </div>
                        <div class="d-flex gap-1">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.branches.edit', $branch) }}"><i class="bi bi-pencil"></i> संपादित</a>
                            <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" data-submit-once>
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="शाखा हटाएँ?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="empty-state"><i class="bi bi-geo-alt"></i><p class="mb-0">कोई शाखा दर्ज नहीं है।</p></div></div>
        @endforelse
    </div>

    @if ($branches->hasPages())
        <div class="mt-4">{{ $branches->links() }}</div>
    @endif
@endsection
