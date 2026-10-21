@extends('layouts.admin')

@section('title', 'संपर्क संदेश')
@section('heading', 'संपर्क संदेश')
@section('subheading', 'वेबसाइट से प्राप्त संदेश')

@section('content')
    <div class="page-heading">
        <div><h1>संदेश</h1><p>अपठित: {{ number_format($unreadCount) }}</p></div>
        <a class="btn btn-sm {{ request('unread') ? 'btn-admin' : 'btn-outline-secondary' }}" href="{{ route('admin.contact-messages.index', ['unread' => 1]) }}">केवल अपठित</a>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin table-stack mb-0">
                <thead><tr><th>प्रेषक</th><th>विषय</th><th>संदेश</th><th>स्थिति</th><th>दिनांक</th><th class="text-end">कार्य</th></tr></thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr class="{{ $message->read_at ? '' : 'fw-semibold' }}">
                            <td data-label="प्रेषक">
                                {{ $message->name }}
                                <div class="small text-muted fw-normal">{{ $message->phone ? phone_display($message->phone) : ($message->email ?: '—') }}</div>
                            </td>
                            <td class="small" data-label="विषय">{{ $message->subject ?: '—' }}</td>
                            <td class="small" data-label="संदेश">{{ excerpt_hi($message->message, 70) }}</td>
                            <td data-label="स्थिति"><span class="badge text-bg-{{ $message->read_at ? 'secondary' : 'info' }}">{{ $message->read_at ? 'पढ़ा गया' : 'नया' }}</span></td>
                            <td class="small" data-label="दिनांक">{{ hindi_datetime($message->created_at) }}</td>
                            <td class="text-end actions">
                                <a class="btn btn-outline-secondary" href="{{ route('admin.contact-messages.show', $message) }}"><i class="bi bi-eye"></i></a>
                                <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" class="d-inline" data-submit-once>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" type="submit" data-confirm="संदेश हटाएँ?"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">कोई संदेश नहीं है।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($messages->hasPages())
            <div class="card-body border-top">{{ $messages->links() }}</div>
        @endif
    </div>
@endsection
