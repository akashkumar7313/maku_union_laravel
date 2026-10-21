@extends('layouts.admin')

@section('title', 'पृष्ठ')
@section('heading', 'पृष्ठ प्रबंधन')
@section('subheading', 'वेबसाइट के स्थिर पृष्ठ')

@section('content')
    <div class="page-heading">
        <div><h1>पृष्ठ</h1><p>{{ number_format($pages->total()) }} पृष्ठ</p></div>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin mb-0">
                <thead><tr><th>शीर्षक</th><th>स्लग</th><th>स्थिति</th><th>फुटर</th><th>अंतिम अद्यतन</th><th class="text-end">कार्य</th></tr></thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $page->title }}</div>
                                <div class="small text-muted">{{ excerpt_hi($page->subtitle, 70) }}</div>
                            </td>
                            <td class="small">{{ $page->slug }}</td>
                            <td><span class="badge text-bg-{{ $page->is_active ? 'success' : 'secondary' }}">{{ $page->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span></td>
                            <td><span class="badge text-bg-{{ $page->show_in_footer ? 'info' : 'light' }}">{{ $page->show_in_footer ? 'हाँ' : 'नहीं' }}</span></td>
                            <td class="small">{{ hindi_datetime($page->updated_at) }}</td>
                            <td class="text-end actions">
                                <a class="btn btn-outline-secondary" target="_blank" rel="noopener" href="{{ route('pages.show', $page) }}"><i class="bi bi-box-arrow-up-right"></i></a>
                                <a class="btn btn-outline-secondary" href="{{ route('admin.pages.edit', $page) }}"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($pages->hasPages())
        <div class="mt-4">{{ $pages->links() }}</div>
    @endif
@endsection
