<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserAccountIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasActiveAccount()) {
            return $next($request);
        }

        $message = $user->normalizedAccountStatus() === 'suspended'
            ? 'Access denied. Your account is suspended. Please contact the administrator.'
            : 'Access denied. Your account is deactivated. Please contact the administrator.';

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->flash('status', $message);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('login'),
            ], 423);
        }

        return redirect()->route('login')->with('status', $message);
    }
}
