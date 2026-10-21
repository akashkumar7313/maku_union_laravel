@extends('layouts.admin')

@section('title', 'एक्शन कार्ड')
@section('heading', 'एक्शन कार्ड')
@section('subheading', 'होमपेज के मुख्य कार्य बटन')

@section('content')
    <div class="page-heading">
        <div><h1>एक्शन कार्ड</h1><p>होमपेज पर तीन प्रमुख कार्ड दिखाए जाते हैं।</p></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @foreach ($cards as $card)
                <div class="card-admin mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="stat-card-admin p-0" style="width: 48px; height: 48px; border-radius: 12px;">
                                    <span class="icon {{ $card->color }}"><i class="bi {{ $card->icon ?: 'bi-grid' }}"></i></span>
                                </span>
                                <div>
                                    <div class="fw-semibold">{{ $card->title }}</div>
                                    <div class="small text-muted">{{ $card->description }}</div>
                                    <div class="small text-muted">{{ $card->url }} · क्रम {{ $card->sort_order }} · <span class="badge text-bg-{{ $card->is_active ? 'success' : 'secondary' }}">{{ $card->is_active ? 'सक्रिय' : 'निष्क्रिय' }}</span></div>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#card-{{ $card->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.action-cards.destroy', $card) }}" method="POST" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="कार्ड हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="collapse mt-3" id="card-{{ $card->id }}">
                            <hr>
                            <form action="{{ route('admin.action-cards.update', $card) }}" method="POST" data-submit-once>
                                @csrf @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label">शीर्षक</label><input class="form-control" name="title" value="{{ $card->title }}" required maxlength="120"></div>
                                    <div class="col-md-6"><label class="form-label">विवरण</label><input class="form-control" name="description" value="{{ $card->description }}" maxlength="300"></div>
                                    <div class="col-md-4"><label class="form-label">रंग</label>
                                        <select class="form-select" name="color">
                                            @foreach (['green' => 'हरा', 'blue' => 'नीला', 'orange' => 'नारंगी', 'navy' => 'गहरा नीला'] as $value => $label)
                                                <option value="{{ $value }}" @selected($card->color === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4"><label class="form-label">आइकन</label><input class="form-control" name="icon" value="{{ $card->icon }}" maxlength="60"></div>
                                    <div class="col-md-4"><label class="form-label">क्रम</label><input class="form-control" name="sort_order" type="number" min="0" max="999" value="{{ $card->sort_order }}"></div>
                                    <div class="col-md-8"><label class="form-label">URL</label><input class="form-control" name="url" value="{{ $card->url }}" required maxlength="255"></div>
                                    <div class="col-md-4 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="ac{{ $card->id }}" @checked($card->is_active)><label class="form-check-label" for="ac{{ $card->id }}">सक्रिय</label></div></div>
                                </div>
                                <button class="btn btn-admin mt-3" type="submit">अपडेट करें</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-lg-4">
            <div class="card-admin">
                <div class="card-header">नया कार्ड</div>
                <div class="card-body">
                    <form action="{{ route('admin.action-cards.store') }}" method="POST" data-submit-once>
                        @csrf
                        <div class="mb-3"><label class="form-label" for="title">शीर्षक</label><input class="form-control" id="title" name="title" required maxlength="120"></div>
                        <div class="mb-3"><label class="form-label" for="description">विवरण</label><input class="form-control" id="description" name="description" maxlength="300"></div>
                        <div class="mb-3"><label class="form-label" for="color">रंग</label>
                            <select class="form-select" id="color" name="color">
                                <option value="green">हरा</option><option value="blue">नीला</option><option value="orange">नारंगी</option><option value="navy">गहरा नीला</option>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label" for="icon">आइकन</label><input class="form-control" id="icon" name="icon" maxlength="60" placeholder="bi-person-plus"></div>
                        <div class="mb-3"><label class="form-label" for="url">URL</label><input class="form-control" id="url" name="url" required maxlength="255" placeholder="/membership/register"></div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label" for="sort_order">क्रम</label><input class="form-control" id="sort_order" name="sort_order" type="number" min="0" max="999" value="0"></div>
                            <div class="col-6 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="newcard" checked><label class="form-check-label" for="newcard">सक्रिय</label></div></div>
                        </div>
                        <button class="btn btn-orange w-100" type="submit">कार्ड जोड़ें</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
