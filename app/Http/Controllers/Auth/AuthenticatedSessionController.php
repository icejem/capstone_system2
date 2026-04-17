<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginVerification;
use App\Models\User;
use App\Services\LoginVerificationService;
use App\Services\UserSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
    public function store(LoginRequest $request, LoginVerificationService $loginVerificationService): RedirectResponse
    {
        $user = $request->authenticate();

        $request->session()->regenerate();

        $verification = $loginVerificationService->create(
            $user,
            $request,
            $request->boolean('remember'),
        );

        $request->session()->put('login_verification_id', $verification->id);
        $request->session()->put('login_verification_email', $user->email);

        return redirect()
            ->route('login.verification.notice')
            ->with('status', 'We sent a verification link to your email. Please confirm to continue.');
    }

    public function notice(Request $request, LoginVerificationService $loginVerificationService): View|RedirectResponse
    {
        $verification = $this->pendingVerificationFromSession($request);

        if (! $verification) {
            return redirect()->route('login')
                ->with('status', 'Please sign in first to request a login verification link.')
                ->with('auth_form', 'login');
        }

        if ($verification->isConsumed()) {
            $this->clearPendingVerificationSession($request);

            return redirect()->route('login')
                ->with('status', 'This login verification was already used. Please sign in again if needed.')
                ->with('auth_form', 'login');
        }

        if ($verification->isInvalidated()) {
            $this->clearPendingVerificationSession($request);

            return redirect()->route('login')
                ->with('status', 'A newer login verification link was issued. Please sign in again if needed.')
                ->with('auth_form', 'login');
        }

        if ($verification->isDenied()) {
            $this->clearPendingVerificationSession($request);

            return redirect()->route('login')
                ->with('status', 'This login request was denied. Please sign in again if needed.')
                ->with('auth_form', 'login');
        }

        return view('auth.verify-login', [
            'email' => (string) $request->session()->get('login_verification_email', $verification->email),
            'expiresAt' => $verification->expires_at,
            'resendAvailableAt' => $loginVerificationService->resendAvailableAt($verification),
            'canResend' => $loginVerificationService->canResend($verification),
            'deviceLabel' => $verification->device_label,
        ]);
    }

    public function resend(Request $request, LoginVerificationService $loginVerificationService): RedirectResponse
    {
        $verification = $this->pendingVerificationFromSession($request);

        if (! $verification) {
            return redirect()->route('login')
                ->with('status', 'Please sign in again to request a new verification link.')
                ->with('auth_form', 'login');
        }

        if (! $loginVerificationService->canResend($verification)) {
            throw ValidationException::withMessages([
                'email' => 'Please wait a moment before requesting another verification email.',
            ]);
        }

        $newVerification = $loginVerificationService->resend($verification, $request);
        $newVerification->forceFill([
            'last_resent_at' => now(),
        ])->save();

        $request->session()->put('login_verification_id', $newVerification->id);
        $request->session()->put('login_verification_email', $newVerification->email);

        return redirect()
            ->route('login.verification.notice')
            ->with('status', 'A fresh verification link has been sent to your email.');
    }

    public function verify(
        Request $request,
        LoginVerification $verification,
        string $payload,
        LoginVerificationService $loginVerificationService,
    ): RedirectResponse {
        $user = $loginVerificationService->verify($verification, $payload, $request);

        if (! $user || ! $user->hasActiveAccount()) {
            $this->clearPendingVerificationSession($request);

            return redirect()->route('login')->withErrors([
                'email' => 'This verification link is invalid, expired, or already used. Please log in again.',
            ])->withInput([
                'email' => $verification->email,
                'auth_form' => 'login',
            ]);
        }

        return redirect()
            ->route('login.verification.notice')
            ->with('status', 'Login approved. Return to the original browser to continue.');
    }

    public function deny(
        Request $request,
        LoginVerification $verification,
        string $payload,
        LoginVerificationService $loginVerificationService,
    ): RedirectResponse {
        $user = $loginVerificationService->validatePendingRequest($verification, $payload, $request);

        if (! $user) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'This approval request is invalid, expired, or already used.',
                ])
                ->withInput([
                    'email' => $verification->email,
                    'auth_form' => 'login',
                ]);
        }

        $loginVerificationService->deny($verification, $request);
        $this->clearPendingVerificationSession($request);

        return redirect()->route('login')
            ->with('status', 'The login request was denied. If this was not you, please change your password immediately.')
            ->with('auth_form', 'login');
    }

    public function status(Request $request): JsonResponse
    {
        $verification = $this->pendingVerificationFromSession($request);

        if (! $verification) {
            return response()->json([
                'status' => 'missing',
                'redirect' => route('login'),
            ]);
        }

        if ($verification->isConsumed()) {
            return response()->json([
                'status' => 'completed',
                'redirect' => $this->dashboardTargetFor($verification->user),
            ]);
        }

        if ($verification->isDenied()) {
            $this->clearPendingVerificationSession($request);

            return response()->json([
                'status' => 'denied',
                'redirect' => route('login'),
            ]);
        }

        if ($verification->isInvalidated() || $verification->isExpired()) {
            $this->clearPendingVerificationSession($request);

            return response()->json([
                'status' => 'expired',
                'redirect' => route('login'),
            ]);
        }

        if ($verification->verified_at) {
            return response()->json([
                'status' => 'approved',
                'complete_url' => route('login.verification.complete'),
            ]);
        }

        return response()->json([
            'status' => 'pending',
        ]);
    }

    public function complete(Request $request, LoginVerificationService $loginVerificationService): RedirectResponse
    {
        $verification = $this->pendingVerificationFromSession($request);

        if (! $verification) {
            return redirect()->route('login')
                ->with('status', 'Please sign in again to continue.')
                ->with('auth_form', 'login');
        }

        if (! $verification->verified_at) {
            return redirect()
                ->route('login.verification.notice')
                ->with('status', 'Waiting for email approval. Please confirm the link from your Gmail first.');
        }

        if ($verification->isDenied()) {
            $this->clearPendingVerificationSession($request);

            return redirect()->route('login')
                ->with('status', 'This login request was denied. Please sign in again if needed.')
                ->with('auth_form', 'login');
        }

        if ($verification->isInvalidated() || $verification->isExpired()) {
            $this->clearPendingVerificationSession($request);

            return redirect()->route('login')
                ->with('status', 'This verification request expired. Please sign in again.')
                ->with('auth_form', 'login');
        }

        return $this->completeVerifiedLogin($request, $verification, $loginVerificationService);
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

    private function pendingVerificationFromSession(Request $request): ?LoginVerification
    {
        $verificationId = $request->session()->get('login_verification_id');

        if (! $verificationId) {
            return null;
        }

        return LoginVerification::with('user')->find($verificationId);
    }

    private function clearPendingVerificationSession(Request $request): void
    {
        $request->session()->forget([
            'login_verification_id',
            'login_verification_email',
        ]);
    }

    private function dashboardTargetFor(User $user): string
    {
        return match ((string) ($user->user_type ?? 'student')) {
            'admin' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default => route('student.dashboard'),
        };
    }

    private function completeVerifiedLogin(
        Request $request,
        LoginVerification $verification,
        LoginVerificationService $loginVerificationService,
    ): RedirectResponse {
        $user = $verification->user;

        if (! $user || ! $user->hasActiveAccount() || $verification->isConsumed() || $verification->isDenied()) {
            $this->clearPendingVerificationSession($request);

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'This verification link is invalid, expired, or already used. Please log in again.',
                ])
                ->withInput([
                    'email' => $verification->email,
                    'auth_form' => 'login',
                ]);
        }

        $loginVerificationService->consume($verification, $request);

        Auth::login($user, $verification->remember);
        $request->session()->regenerate();
        $this->clearPendingVerificationSession($request);

        UserSessionService::createSession($user);
        $request->session()->forget('url.intended');

        return redirect()->to($this->dashboardTargetFor($user));
    }
}
