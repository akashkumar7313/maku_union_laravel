@extends('layouts.admin')

@section('title', 'सदस्य संपादित करें')
@section('heading', 'सदस्य संपादित करें')
@section('subheading', $member->membership_no)

@section('content')
    <div class="page-heading">
        <div><h1>{{ $member->name }}</h1><p>{{ $member->membership_no }}</p></div>
        <div class="actions">
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.members.show', $member) }}"><i class="bi bi-eye me-1"></i> विवरण</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.members.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
        </div>
    </div>

    <form action="{{ route('admin.members.update', $member) }}" method="POST" enctype="multipart/form-data" data-submit-once>
        @csrf
        @method('PUT')
        @include('admin.members._form', ['member' => $member, 'categories' => $categories, 'isEdit' => true])
        <div class="card-admin mt-4">
            <div class="card-body d-flex flex-wrap justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.members.show', $member) }}">रद्द करें</a>
                <button class="btn btn-orange" type="submit">परिवर्तन सहेजें</button>
            </div>
        </div>
    </form>
@endsection
