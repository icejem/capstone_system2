<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginVerificationMail;
use App\Models\LoginVerificationToken;
use App\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class LoginVerificationController extends Controller
{
    use ThrottlesLogins;

    protected $maxAttempts = 5;
    protected $decayMinutes = 15;

    /**
     * Handle login request - generate verification token and send email
     */
    public function handleLogin(Request $request)
    {
        $this->validateLogin($request);

        // Throttle login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            Log::warning('Login throttled: Too many attempts', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return $this->sendLockoutResponse($request);
        }

        // Attempt to authenticate
        $user = $this->getUser($request);

        if (!$user) {
            $this->incrementLoginAttempts($request);
            Log::warning('Login failed: Invalid credentials', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        // Generate verification token
        $token = LoginVerificationToken::generateToken(
            $user,
            $request->ip(),
            $request->userAgent()
        );

        // Send verification email
        Mail::to($user->email)->send(new LoginVerificationMail(
            $user,
            $token,
            $request->ip(),
            $request->userAgent()
        ));

        // Log login attempt
        Log::info('Login verification email sent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'token_id' => $token->id,
        ]);

        // Clear throttle
        $this->clearLoginAttempts($request);

        // Redirect to verification pending page
        return redirect()->route('auth.login-verification-pending')->with('email', $user->email);
    }

    /**
     * Verify login token and authenticate user
     */
    public function verifyLogin(Request $request, string $token)
    {
        $verificationToken = LoginVerificationToken::findByPlainToken($token);

        if (!$verificationToken) {
            Log::warning('Login verification failed: Invalid or expired token', [
                'ip' => $request->ip(),
                'token' => substr($token, 0, 10) . '...',
            ]);

            return redirect()->route('login')
                ->with('error', 'Verification link is invalid or expired. Please login again.');
        }

        if ($verificationToken->isExpired()) {
            Log::warning('Login verification failed: Token expired', [
                'user_id' => $verificationToken->user_id,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Verification link has expired. Please login again.');
        }

        if ($verificationToken->used) {
            Log::warning('Login verification failed: Token already used', [
                'user_id' => $verificationToken->user_id,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('login')
                ->with('error', 'This verification link has already been used. Please login again.');
        }

        // Mark token as verified
        $verificationToken->markAsVerified();

        $user = $verificationToken->user;

        // Log successful verification
        Log::info('Login verification successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'token_id' => $verificationToken->id,
        ]);

        // Authenticate user
        Auth::login($user, remember: false);

        // Update last login
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome back! You have been successfully verified and logged in.');
    }

    /**
     * Show login verification pending page
     */
    public function showPending(Request $request)
    {
        $email = $request->query('email') ?? session('email');

        if (!$email) {
            return redirect()->route('login');
        }

        return view('auth.login-verification-pending', ['email' => $email]);
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            Log::warning('Resend verification: User not found', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return back()->with('error', 'Email address not found.');
        }

        // Check if there's a recent valid token
        $recentToken = LoginVerificationToken::where('user_id', $user->id)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($recentToken && $recentToken->created_at->diffInSeconds(now()) < 60) {
            return back()->with('error', 'Please wait 60 seconds before requesting another verification email.');
        }

        // Generate new token
        $token = LoginVerificationToken::generateToken(
            $user,
            $request->ip(),
            $request->userAgent()
        );

        // Send email
        Mail::to($user->email)->send(new LoginVerificationMail(
            $user,
            $token,
            $request->ip(),
            $request->userAgent()
        ));

        Log::info('Verification email resent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Verification email sent! Please check your inbox.');
    }

    /**
     * Get user from email
     */
    protected function getUser(Request $request): ?User
    {
        return User::where('email', $request->input('email'))->first();
    }

    /**
     * Validate login request
     */
    protected function validateLogin(Request $request): void
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     */
    public function username(): string
    {
        return 'email';
    }
}
