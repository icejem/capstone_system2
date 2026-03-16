<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Throwable;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            // Attempt to send reset link via configured mail transport.
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput($request->only('email', 'auth_form'))
                ->withErrors([
                    'email' => 'Unable to send reset email. Please verify MAIL_USERNAME and Gmail App Password in your .env.',
                ]);
        }

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                        ->with('auth_form', 'forgot')
                    : back()->withInput($request->only('email', 'auth_form'))
                        ->withErrors(['email' => __($status)]);
    }
}
