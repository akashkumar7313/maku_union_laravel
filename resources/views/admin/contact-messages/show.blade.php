@extends('layouts.admin')

@section('title', 'संदेश विवरण')
@section('heading', 'संदेश विवरण')
@section('subheading', $message->subject ?: 'संपर्क संदेश')

@section('content')
    <div class="page-heading">
        <div><h1>{{ $message->subject ?: 'संपर्क संदेश' }}</h1><p>{{ hindi_datetime($message->created_at) }}</p></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.contact-messages.index') }}"><i class="bi bi-arrow-left me-1"></i> सूची</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-admin">
                <div class="card-body">
                    <div class="mb-3" style="white-space: pre-line;">{{ $message->message }}</div>
                    <hr>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($message->email)
                            <a class="btn btn-sm btn-admin" href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: 'आपका संदेश')) }}"><i class="bi bi-reply me-1"></i> ईमेल द्वारा उत्तर</a>
                        @endif
                        @if ($message->phone)
                            <a class="btn btn-sm btn-outline-secondary" href="{{ phone_href($message->phone) }}"><i class="bi bi-telephone me-1"></i> कॉल करें</a>
                        @endif
                        <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" data-submit-once>
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="संदेश हटाएँ?"><i class="bi bi-trash me-1"></i> हटाएँ</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-admin">
                <div class="card-header">प्रेषक</div>
                <div class="card-body">
                    <dl class="detail-list mb-0">
                        <dt>नाम</dt><dd>{{ $message->name }}</dd>
                        <dt>ईमेल</dt><dd>{{ $message->email ?: '—' }}</dd>
                        <dt>मोबाइल</dt><dd>{{ $message->phone ? phone_display($message->phone) : '—' }}</dd>
                        <dt>IP</dt><dd>{{ $message->ip ?: '—' }}</dd>
                        <dt>पढ़ा गया</dt><dd>{{ $message->read_at ? hindi_datetime($message->read_at) : 'नहीं' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
