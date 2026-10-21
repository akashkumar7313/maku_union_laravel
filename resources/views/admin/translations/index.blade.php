@extends('layouts.admin')

@section('title', 'अनुवाद')
@section('heading', 'अनुवाद')
@section('subheading', 'मशीन अनुवाद एवं समीक्षा')

@section('content')
    <div class="page-heading">
        <div><h1>अनुवाद</h1><p>{{ number_format($translations->total()) }} प्रविष्टियाँ</p></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.translations.index', ['reviewed' => 0]) }}">केवल समीक्षा हेतु</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card-admin">
                <div class="label">स्थिति</div>
                <div class="value" style="font-size: 1.1rem;">{{ $status['enabled'] ? 'सक्षम' : 'अक्षम' }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-admin">
                <div class="label">कुंजी कॉन्फ़िगर</div>
                <div class="value" style="font-size: 1.1rem;">{{ $status['configured'] ? 'हाँ' : 'नहीं' }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-admin">
                <div class="label">शेष कोटा</div>
                <div class="value">{{ number_format($status['remaining']) }} <small class="text-muted fs-6">/ {{ number_format($status['monthly_limit']) }}</small></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card-admin">
                <div class="label">समीक्षा लंबित</div>
                <div class="value">{{ number_format($status['pending_review']) }}</div>
            </div>
        </div>
    </div>

    <div class="card-admin mb-4">
        <div class="card-body">
            <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.translations.index') }}">
                <div class="col-md-5">
                    <label class="form-label" for="q">खोजें</label>
                    <input class="form-control" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="कुंजी या अनुवाद">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="reviewed">स्थिति</label>
                    <select class="form-select" id="reviewed" name="reviewed">
                        <option value="">सभी</option>
                        <option value="0" @selected(($filters['reviewed'] ?? '') === '0')>समीक्षा लंबित</option>
                        <option value="1" @selected(($filters['reviewed'] ?? '') === '1')>समीक्षित</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-admin" type="submit">फ़िल्टर</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.translations.index') }}">रीसेट</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin mb-0">
                <thead><tr><th style="width: 22%;">कुंजी</th><th>अनुवाद</th><th style="width: 12%;">स्रोत</th><th style="width: 10%;">समीक्षा</th><th class="text-end">कार्य</th></tr></thead>
                <tbody>
                    @forelse ($translations as $translation)
                        <tr>
                            <td class="small"><code class="text-break">{{ \Illuminate\Support\Str::limit($translation->key, 60) }}</code></td>
                            <td>
                                <form id="translation-{{ $translation->id }}" action="{{ route('admin.translations.update', $translation) }}" method="POST" data-submit-once>
                                    @csrf @method('PUT')
                                    <textarea class="form-control form-control-sm" name="value" rows="2" required maxlength="5000">{{ $translation->value }}</textarea>
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" id="reviewed-{{ $translation->id }}" name="reviewed" type="checkbox" value="1" @checked($translation->reviewed)>
                                        <label class="form-check-label small" for="reviewed-{{ $translation->id }}">समीक्षित</label>
                                    </div>
                                </form>
                            </td>
                            <td class="small">{{ $translation->source ?: '—' }}</td>
                            <td><span class="badge text-bg-{{ $translation->reviewed ? 'success' : 'warning' }}">{{ $translation->reviewed ? 'समीक्षित' : 'लंबित' }}</span></td>
                            <td class="text-end actions">
                                <button class="btn btn-outline-secondary" type="submit" form="translation-{{ $translation->id }}"><i class="bi bi-check2"></i> सहेजें</button>
                                @unless ($translation->reviewed)
                                    <form action="{{ route('admin.translations.review', $translation) }}" method="POST" class="d-inline" data-submit-once>
                                        @csrf
                                        <button class="btn btn-outline-success" type="submit" title="समीक्षित चिह्नित करें"><i class="bi bi-patch-check"></i></button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">कोई अनुवाद नहीं मिला।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($translations->hasPages())
            <div class="card-body border-top">{{ $translations->links() }}</div>
        @endif
    </div>
@endsection
