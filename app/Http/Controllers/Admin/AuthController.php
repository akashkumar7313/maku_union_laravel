<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request, AuditLogger $audit)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'ईमेल आवश्यक है।',
            'email.email' => 'मान्य ईमेल भरें।',
            'password.required' => 'पासवर्ड आवश्यक है।',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            $audit->log('auth.login_failed', null, [], ['email' => $credentials['email'], 'ip' => $request->ip()]);

            throw ValidationException::withMessages([
                'email' => 'ईमेल या पासवर्ड सही नहीं है।',
            ]);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'आपका खाता निष्क्रिय है। कृपया प्रशासक से संपर्क करें।',
            ]);
        }

        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();
        $audit->log('auth.login', $user, [], ['email' => $user->email]);

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'स्वागत है, '.$user->name.'।');
    }

    public function logout(Request $request, AuditLogger $audit)
    {
        if ($request->user()) {
            $audit->log('auth.logout', $request->user(), [], ['email' => $request->user()->email]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'आप सुरक्षित रूप से लॉगआउट हो गए हैं।');
    }
}
