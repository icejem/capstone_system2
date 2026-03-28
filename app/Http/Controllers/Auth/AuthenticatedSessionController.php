<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Track the session
        $user = Auth::user();
        if ($user) {
            UserSessionService::createSession($user);
        }

        $userType = (string) (Auth::user()?->user_type ?? 'student');
        $target = match ($userType) {
            'admin' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default => route('student.dashboard'),
        };

        // Avoid redirecting to API endpoints that were stored as intended URLs.
        $request->session()->forget('url.intended');

        return redirect()->to($target);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            if ($user) {
                UserSessionService::endSession($user);
            }
        } catch (\Throwable $e) {
            // Ignore tracking cleanup failures so logout can still complete.
        }

        try {
            Auth::guard('web')->logout();
        } catch (\Throwable $e) {
            // Ignore guard cleanup failures and continue redirecting home.
        }

        try {
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        } catch (\Throwable $e) {
            // Ignore session store issues so users are not stranded on /logout.
        }

        $response = redirect()->to('/');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
