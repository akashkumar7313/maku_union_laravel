<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User(['role' => Role::Officer->value, 'is_active' => true]),
            'roles' => Role::cases(),
        ]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'digits_between:10,15'],
            'role' => ['required', Rule::enum(Role::class)],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'नाम आवश्यक है।',
            'email.required' => 'ईमेल आवश्यक है।',
            'email.unique' => 'यह ईमेल पहले से पंजीकृत है।',
            'password.required' => 'पासवर्ड आवश्यक है।',
            'password.min' => 'पासवर्ड कम से कम 8 अक्षरों का होना चाहिए।',
            'password.confirmed' => 'पासवर्ड और पुष्टि पासवर्ड मेल नहीं खाते।',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['phone'] = $data['phone'] ? clean_digits($data['phone']) : null;

        $user = User::create($data);
        $audit->log('admin_user.created', $user, [], ['email' => $user->email, 'role' => $user->role]);

        return redirect()->route('admin.users.index')->with('success', 'प्रशासनिक उपयोगकर्ता जोड़ा गया।');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => Role::cases(),
        ]);
    }

    public function update(Request $request, User $user, AuditLogger $audit)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'digits_between:10,15'],
            'role' => ['required', Rule::enum(Role::class)],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'नाम आवश्यक है।',
            'email.unique' => 'यह ईमेल पहले से पंजीकृत है।',
            'password.min' => 'पासवर्ड कम से कम 8 अक्षरों का होना चाहिए।',
        ]);

        $data['is_active'] = $request->boolean('is_active') || $user->id === $request->user()->id;
        $data['phone'] = $data['phone'] ? clean_digits($data['phone']) : null;

        if ($user->id === $request->user()->id && $data['role'] !== $user->role) {
            return back()->withErrors(['role' => 'अपनी स्वयं की भूमिका नहीं बदली जा सकती।'])->withInput();
        }

        if ($this->wouldRemoveLastAdmin($user, $data['role'], $data['is_active'])) {
            return back()->withErrors(['role' => 'अंतिम सक्रिय प्रशासक की भूमिका या स्थिति नहीं बदली जा सकती।'])->withInput();
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $old = $user->only(['name', 'email', 'role', 'is_active']);
        $user->update($data);
        $audit->log('admin_user.updated', $user, $old, $user->only(['name', 'email', 'role', 'is_active']));

        return redirect()->route('admin.users.index')->with('success', 'उपयोगकर्ता विवरण अपडेट हो गया।');
    }

    public function destroy(Request $request, User $user, AuditLogger $audit)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'आप अपना स्वयं का खाता नहीं हटा सकते।');
        }

        if ($this->wouldRemoveLastAdmin($user, Role::Officer->value, false)) {
            return back()->with('error', 'अंतिम सक्रिय प्रशासक खाता नहीं हटाया जा सकता।');
        }

        $audit->log('admin_user.deleted', $user, ['email' => $user->email], []);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'उपयोगकर्ता हटा दिया गया।');
    }

    private function wouldRemoveLastAdmin(User $user, string $newRole, bool $newActive): bool
    {
        if ($user->role !== Role::Admin->value) {
            return false;
        }

        if ($newRole === Role::Admin->value && $newActive) {
            return false;
        }

        return User::query()
            ->where('role', Role::Admin->value)
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->count() === 0;
    }
}
