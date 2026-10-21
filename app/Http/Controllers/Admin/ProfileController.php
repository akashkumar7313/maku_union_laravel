<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request, AuditLogger $audit)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'digits_between:10,15'],
        ], [
            'name.required' => 'नाम आवश्यक है।',
            'email.required' => 'ईमेल आवश्यक है।',
            'email.unique' => 'यह ईमेल पहले से पंजीकृत है।',
        ]);

        $data['phone'] = $data['phone'] ? clean_digits($data['phone']) : null;
        $old = $user->only(['name', 'email', 'phone']);
        $user->update($data);

        $audit->log('admin.profile_updated', $user, $old, $data);

        return back()->with('success', 'प्रोफ़ाइल सफलतापूर्वक अपडेट हो गई।');
    }

    public function password(Request $request, AuditLogger $audit)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'वर्तमान पासवर्ड आवश्यक है।',
            'password.required' => 'नया पासवर्ड आवश्यक है।',
            'password.min' => 'नया पासवर्ड कम से कम 8 अक्षरों का होना चाहिए।',
            'password.confirmed' => 'नया पासवर्ड और पुष्टि पासवर्ड मेल नहीं खाते।',
            'password.different' => 'नया पासवर्ड वर्तमान पासवर्ड से भिन्न होना चाहिए।',
        ]);

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'वर्तमान पासवर्ड सही नहीं है।']);
        }

        $request->user()->update(['password' => $data['password']]);
        $audit->log('admin.password_changed', $request->user());

        return back()->with('success', 'पासवर्ड सफलतापूर्वक बदल दिया गया।');
    }
}
