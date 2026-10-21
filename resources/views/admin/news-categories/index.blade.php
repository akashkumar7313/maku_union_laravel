@extends('layouts.admin')

@section('title', 'समाचार श्रेणियाँ')
@section('heading', 'समाचार श्रेणियाँ')
@section('subheading', 'श्रेणी प्रबंधन')

@section('content')
    <div class="page-heading">
        <div><h1>समाचार श्रेणियाँ</h1><p>{{ number_format($categories->total()) }} श्रेणियाँ</p></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-admin">
                <div class="table-responsive">
                    <table class="table table-admin mb-0">
                        <thead><tr><th>नाम</th><th>स्लग</th><th>समाचार</th><th>स्थिति</th><th class="text-end">कार्य</th></tr></thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>
                                        <form action="{{ route('admin.news-categories.update', $category) }}" method="POST" class="d-flex gap-2 align-items-center" data-submit-once>
                                            @csrf @method('PUT')
                                            <input class="form-control form-control-sm" name="name" value="{{ $category->name }}" required maxlength="100">
                                            <input type="hidden" name="slug" value="{{ $category->slug }}">
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="cat{{ $category->id }}" @checked($category->is_active)>
                                                <label class="form-check-label small" for="cat{{ $category->id }}">सक्रिय</label>
                                            </div>
                                            <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-check2"></i></button>
                                        </form>
                                    </td>
                                    <td class="small text-muted">{{ $category->slug }}</td>
                                    <td class="small">{{ $category->news_items_count }}</td>
                                    <td><span class="badge text-bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span></td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.news-categories.destroy', $category) }}" method="POST" data-submit-once>
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="श्रेणी हटाएँ? जुड़े समाचार श्रेणी-रहित हो जाएँगे।"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">कोई श्रेणी नहीं है।</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card-admin">
                <div class="card-header">नई श्रेणी</div>
                <div class="card-body">
                    <form action="{{ route('admin.news-categories.store') }}" method="POST" data-submit-once>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="name">नाम <span class="required">*</span></label>
                            <input class="form-control" id="name" name="name" value="{{ old('name') }}" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="slug">स्लग</label>
                            <input class="form-control" id="slug" name="slug" value="{{ old('slug') }}" maxlength="120">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" checked>
                            <label class="form-check-label" for="is_active">सक्रिय</label>
                        </div>
                        <button class="btn btn-orange w-100" type="submit">श्रेणी जोड़ें</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($categories->hasPages())
        <div class="mt-4">{{ $categories->links() }}</div>
    @endif
@endsection
