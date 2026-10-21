@extends('layouts.admin')

@section('title', $item->exists ? 'मेन्यू संपादित करें' : 'नया मेन्यू आइटम')
@section('heading', $item->exists ? 'मेन्यू संपादित करें' : 'नया मेन्यू आइटम')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $item->exists ? 'मेन्यू संपादित करें' : 'नया मेन्यू आइटम' }}</h1></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.menus.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ $item->exists ? route('admin.menus.update', $item) : route('admin.menus.store') }}" method="POST" data-submit-once>
        @csrf
        @if ($item->exists) @method('PUT') @endif
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-admin">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="label">लेबल <span class="required">*</span></label>
                            <input class="form-control" id="label" name="label" value="{{ old('label', $item->label) }}" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="route_name">लारावेल रूट नाम</label>
                            <input class="form-control" id="route_name" name="route_name" value="{{ old('route_name', $item->route_name) }}" maxlength="100" placeholder="membership.register">
                            <div class="form-text">उदाहरण: home, membership.register, grievances.create, news.index, contact</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="url">या कस्टम URL</label>
                            <input class="form-control" id="url" name="url" value="{{ old('url', $item->url) }}" maxlength="255" placeholder="/page/about-us">
                            <div class="form-text">रूट नाम न होने पर यह URL उपयोग होगा।</div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="parent_id">अभिभावक मेन्यू</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">कोई नहीं (मुख्य स्तर)</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" @selected((string) old('parent_id', $item->parent_id) === (string) $parent->id)>{{ $parent->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="sort_order">क्रम</label>
                                <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" max="999" value="{{ old('sort_order', $item->sort_order) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-admin mb-4">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $item->is_active ?? true))>
                            <label class="form-check-label" for="is_active">सक्रिय</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" id="opens_new_tab" name="opens_new_tab" type="checkbox" value="1" @checked(old('opens_new_tab', $item->opens_new_tab))>
                            <label class="form-check-label" for="opens_new_tab">नई टैब में खोलें</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary flex-fill" href="{{ route('admin.menus.index') }}">रद्द करें</a>
                    <button class="btn btn-orange flex-fill" type="submit">सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
