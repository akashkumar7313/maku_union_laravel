@extends('layouts.admin')

@section('title', 'उपयोगकर्ता')
@section('heading', 'प्रशासनिक उपयोगकर्ता')
@section('subheading', 'पैनल पहुँच एवं भूमिकाएँ')

@section('content')
    <div class="page-heading">
        <div><h1>उपयोगकर्ता</h1><p>कुल {{ number_format($users->total()) }}</p></div>
        <a class="btn btn-sm btn-orange" href="{{ route('admin.users.create') }}"><i class="bi bi-plus-lg me-1"></i> नया उपयोगकर्ता</a>
    </div>

    <div class="card-admin">
        <div class="table-responsive">
            <table class="table table-admin table-stack mb-0">
                <thead><tr><th>नाम</th><th>ईमेल</th><th>मोबाइल</th><th>भूमिका</th><th>स्थिति</th><th>अंतिम लॉगिन</th><th class="text-end">कार्य</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td data-label="नाम">
                                <div class="fw-semibold">{{ $user->name }} @if ($user->id === auth()->id())<span class="badge text-bg-light border">आप</span>@endif</div>
                            </td>
                            <td class="small" data-label="ईमेल">{{ $user->email }}</td>
                            <td class="small" data-label="मोबाइल">{{ $user->phone ? phone_display($user->phone) : '—' }}</td>
                            <td data-label="भूमिका"><span class="badge text-bg-{{ $user->isAdmin() ? 'dark' : 'primary' }}">{{ \App\Enums\Role::from($user->role)->label() }}</span></td>
                            <td data-label="स्थिति"><span class="badge text-bg-{{ $user->isActive() ? 'success' : 'secondary' }}">{{ $user->isActive() ? 'सक्रिय' : 'निष्क्रिय' }}</span></td>
                            <td class="small" data-label="अंतिम लॉगिन">{{ $user->last_login_at ? hindi_datetime($user->last_login_at) : 'कभी नहीं' }}</td>
                            <td class="text-end actions">
                                <a class="btn btn-outline-secondary" href="{{ route('admin.users.edit', $user) }}"><i class="bi bi-pencil"></i></a>
                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" data-submit-once>
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger" type="submit" data-confirm="{{ $user->name }} का खाता हटाएँ?"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">कोई उपयोगकर्ता नहीं।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="card-body border-top">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
