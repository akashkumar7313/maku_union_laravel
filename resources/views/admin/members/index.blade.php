@extends('layouts.admin')

@section('title', 'सदस्य')
@section('heading', 'सदस्य प्रबंधन')
@section('subheading', 'सदस्यता रिकॉर्ड खोजें एवं प्रबंधित करें')

@section('content')
    <div class="page-heading">
        <div><h1>सदस्य सूची</h1><p>कुल {{ number_format($members->total()) }} रिकॉर्ड</p></div>
        <div class="actions">
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.members.export', request()->query()) }}"><i class="bi bi-download me-1"></i> CSV निर्यात</a>
            <a class="btn btn-sm btn-orange" href="{{ route('admin.members.create') }}"><i class="bi bi-person-plus me-1"></i> नया सदस्य</a>
        </div>
    </div>

    <div class="card-admin mb-4">
        <div class="card-body">
            <form class="filter-bar" method="GET" action="{{ route('admin.members.index') }}">
                <div>
                    <label class="form-label" for="q">खोज</label>
                    <input class="form-control" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="नाम, सदस्यता संख्या, मोबाइल">
                </div>
                <div>
                    <label class="form-label" for="status">स्थिति</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">सभी</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="category">श्रेणी</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">सभी</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-admin" type="submit"><i class="bi bi-search me-1"></i> खोजें</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.members.index') }}">रीसेट</a>
            </form>
        </div>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin table-stack mb-0">
                <thead>
                    <tr><th>सदस्य</th><th>सदस्यता संख्या</th><th>श्रेणी</th><th>मोबाइल</th><th>वैधता</th><th>स्थिति</th><th class="text-end">कार्य</th></tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td data-label="सदस्य">
                                <div class="d-flex align-items-center gap-2">
                                    <img class="avatar-sm" src="{{ $member->photoUrl() }}" alt="{{ $member->name }}">
                                    <div>
                                        <a class="fw-semibold" href="{{ route('admin.members.show', $member) }}">{{ $member->name }}</a>
                                        <div class="small text-muted">{{ $member->guardian_name ?: '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="small" data-label="सदस्यता संख्या">{{ $member->membership_no }}</td>
                            <td class="small" data-label="श्रेणी">{{ $member->category?->name ?: '—' }}</td>
                            <td class="small" data-label="मोबाइल">{{ phone_display($member->phone) }}</td>
                            <td class="small" data-label="वैधता">{{ hindi_date($member->valid_until) }}</td>
                            <td data-label="स्थिति"><span class="badge text-bg-{{ $member->statusColor() }}">{{ $member->statusLabel() }}</span></td>
                            <td class="text-end actions">
                                <a class="btn btn-outline-secondary" href="{{ route('admin.members.show', $member) }}"><i class="bi bi-eye"></i></a>
                                <a class="btn btn-outline-secondary" href="{{ route('admin.members.edit', $member) }}"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">कोई सदस्य नहीं मिला।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($members->hasPages())
            <div class="card-body border-top">{{ $members->links() }}</div>
        @endif
    </div>
@endsection
