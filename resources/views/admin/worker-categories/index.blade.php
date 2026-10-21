@extends('layouts.admin')

@section('title', 'श्रमिक श्रेणियाँ')
@section('heading', 'श्रमिक श्रेणियाँ')
@section('subheading', 'श्रेणी एवं आइकन प्रबंधन')

@section('content')
    <div class="page-heading">
        <div><h1>श्रमिक श्रेणियाँ</h1><p>{{ number_format($categories->total()) }} श्रेणियाँ</p></div>
        <a class="btn btn-sm btn-orange" href="{{ route('admin.worker-categories.create') }}"><i class="bi bi-plus-lg me-1"></i> नई श्रेणी</a>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin mb-0">
                <thead><tr><th>श्रेणी</th><th>स्लग</th><th>सदस्य</th><th>क्रम</th><th>स्थिति</th><th class="text-end">कार्य</th></tr></thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($category->image)
                                        <img class="thumb-sm" src="{{ upload_url($category->image) }}" alt="">
                                    @else
                                        <span class="text-warning fs-4"><i class="bi {{ $category->icon ?: 'bi-people' }}"></i></span>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $category->name }}</div>
                                        <div class="small text-muted">{{ excerpt_hi($category->description, 60) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $category->slug }}</td>
                            <td class="small">{{ number_format($category->members_count) }}</td>
                            <td class="small">{{ $category->sort_order }}</td>
                            <td><span class="badge text-bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span></td>
                            <td class="text-end actions">
                                <a class="btn btn-outline-secondary" href="{{ route('admin.worker-categories.edit', $category) }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.worker-categories.destroy', $category) }}" method="POST" class="d-inline" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit" data-confirm="श्रेणी हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">कोई श्रेणी नहीं है।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($categories->hasPages())
        <div class="mt-4">{{ $categories->links() }}</div>
    @endif
@endsection
