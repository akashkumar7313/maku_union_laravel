<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next, string $minimum = 'officer'): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'कृपया पहले लॉगिन करें।');
        }

        $user = $request->user();

        if (! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', 'आपका खाता निष्क्रिय है।');
        }

        if ($minimum === 'admin' && ! $user->isAdmin()) {
            abort(403, 'इस कार्य के लिए प्रशासक अनुमति आवश्यक है।');
        }

        return $next($request);
    }
}
