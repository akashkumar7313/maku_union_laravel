@extends('layouts.admin')

@section('title', 'नया सदस्य')
@section('heading', 'नया सदस्य')
@section('subheading', 'सदस्यता पंजीकरण')

@section('content')
    <div class="page-heading">
        <div><h1>नया सदस्य जोड़ें</h1><p>कार्यालय द्वारा सदस्य पंजीकरण</p></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.members.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data" data-submit-once>
        @csrf
        @include('admin.members._form', ['member' => null, 'categories' => $categories, 'isEdit' => false])
        <div class="card-admin mt-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="form-check">
                    <input class="form-check-input" id="mark_active" name="mark_active" type="checkbox" value="1" @checked(old('mark_active'))>
                    <label class="form-check-label" for="mark_active">सदस्यता शुल्क जमा मानकर तुरंत सक्रिय करें</label>
                    <div class="form-text">चयन करने पर वर्तमान सदस्यता शुल्क की प्रविष्टि बनेगी और सदस्य सक्रिय हो जाएगा।</div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.members.index') }}">रद्द करें</a>
                    <button class="btn btn-orange" type="submit">सदस्य सहेजें</button>
                </div>
            </div>
        </div>
    </form>
@endsection
