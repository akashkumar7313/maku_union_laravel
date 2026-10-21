@extends('layouts.admin')

@section('title', 'मेन्यू')
@section('heading', 'मेन्यू प्रबंधन')
@section('subheading', 'नेविगेशन लिंक')

@section('content')
    <div class="page-heading">
        <div><h1>मेन्यू आइटम</h1><p>{{ number_format($items->count()) }} लिंक</p></div>
        <a class="btn btn-sm btn-orange" href="{{ route('admin.menus.create') }}"><i class="bi bi-plus-lg me-1"></i> नया आइटम</a>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin mb-0">
                <thead><tr><th>लेबल</th><th>लिंक</th><th>अभिभावक</th><th>क्रम</th><th>स्थिति</th><th class="text-end">कार्य</th></tr></thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->parent ? '— ' : '' }}{{ $item->label }}</td>
                            <td class="small">{{ $item->route_name ?: ($item->url ?: '—') }}</td>
                            <td class="small">{{ $item->parent?->label ?: '—' }}</td>
                            <td class="small">{{ $item->sort_order }}</td>
                            <td><span class="badge text-bg-{{ $item->is_active ? 'success' : 'secondary' }}">{{ $item->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span></td>
                            <td class="text-end actions">
                                <a class="btn btn-outline-secondary" href="{{ route('admin.menus.edit', $item) }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.menus.destroy', $item) }}" method="POST" class="d-inline" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit" data-confirm="मेन्यू आइटम हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">कोई मेन्यू आइटम नहीं है।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
