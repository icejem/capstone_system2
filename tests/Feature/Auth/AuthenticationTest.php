<?php

namespace Tests\Feature\Auth;

use App\Mail\LoginVerificationMail;
use App\Models\LoginVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_receive_login_verification_email_before_authentication(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login.verification.notice', absolute: false));
        $this->assertDatabaseHas('login_verifications', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
        Mail::assertSent(LoginVerificationMail::class, fn (LoginVerificationMail $mail) => $mail->hasTo($user->email));
    }

    public function test_email_approval_marks_the_request_approved_but_does_not_log_in_that_request(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $verificationUrl = null;

        Mail::assertSent(LoginVerificationMail::class, function (LoginVerificationMail $mail) use ($user, &$verificationUrl) {
            if (! $mail->hasTo($user->email)) {
                return false;
            }

            $verificationUrl = $mail->verificationUrl;

            return true;
        });

        $verification = LoginVerification::query()->latest('id')->firstOrFail();

        $response = $this->get($verificationUrl);

        $this->assertGuest();
        $response->assertOk();
        $response->assertSee('Login approved');
        $this->assertNotNull($verification->fresh()->verified_at);
        $this->assertNull($verification->fresh()->consumed_at);
    }

    public function test_mobile_email_approval_can_complete_the_original_desktop_login(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $verificationUrl = null;

        Mail::assertSent(LoginVerificationMail::class, function (LoginVerificationMail $mail) use ($user, &$verificationUrl) {
            if (! $mail->hasTo($user->email)) {
                return false;
            }

            $verificationUrl = $mail->verificationUrl;

            return true;
        });

        $verification = LoginVerification::query()->latest('id')->firstOrFail();

        $verification->forceFill([
            'verified_at' => now(),
        ])->save();

        $this->assertGuest();
        $this->assertNotNull($verification->fresh()->verified_at);
        $this->assertNull($verification->fresh()->consumed_at);

        $statusResponse = $this->getJson(route('login.verification.status'));
        $statusResponse->assertOk()->assertJson([
            'status' => 'approved',
        ]);

        $completeResponse = $this->get(route('login.verification.complete'));

        $this->assertAuthenticatedAs($user);
        $completeResponse->assertRedirect(route('dashboard', absolute: false));
        $this->assertNotNull($verification->fresh()->consumed_at);
    }

    public function test_remember_me_sets_the_recaller_cookie_after_login_is_completed(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $verification = LoginVerification::query()->latest('id')->firstOrFail();
        $verification->forceFill([
            'verified_at' => now(),
        ])->save();

        $completeResponse = $this->get(route('login.verification.complete'));

        $this->assertAuthenticatedAs($user);
        $this->assertTrue((bool) $verification->fresh()->remember);
        $completeResponse->assertCookie(Auth::guard()->getRecallerName());
    }

    public function test_denied_login_requests_are_not_authenticated(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $denyUrl = null;

        Mail::assertSent(LoginVerificationMail::class, function (LoginVerificationMail $mail) use ($user, &$denyUrl) {
            if (! $mail->hasTo($user->email)) {
                return false;
            }

            $denyUrl = $mail->denyUrl;

            return true;
        });

        $verification = LoginVerification::query()->latest('id')->firstOrFail();

        $response = $this->get($denyUrl);

        $response->assertOk();
        $response->assertSee('Login request denied');

        $this->assertGuest();
        $this->assertNotNull($verification->fresh()->denied_at);
        $this->assertNull($verification->fresh()->consumed_at);
    }

    public function test_original_session_sees_denied_status_when_login_is_rejected(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $verification = LoginVerification::query()->latest('id')->firstOrFail();
        $verification->forceFill([
            'denied_at' => now(),
            'denied_reason' => 'user_denied',
            'invalidated_at' => now(),
        ])->save();

        $statusResponse = $this->getJson(route('login.verification.status'));
        $statusResponse->assertOk()->assertJson([
            'status' => 'denied',
        ]);
    }

    public function test_resending_a_login_verification_invalidates_the_previous_token(): void
    {
        $user = User::factory()->create();
        Mail::fake();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $firstVerification = LoginVerification::query()->latest('id')->firstOrFail();

        $this->travel(61)->seconds();

        $response = $this->post(route('login.verification.resend'));

        $secondVerification = LoginVerification::query()->latest('id')->firstOrFail();

        $response->assertRedirect(route('login.verification.notice', absolute: false));
        $this->assertNotSame($firstVerification->id, $secondVerification->id);
        $this->assertNotNull($firstVerification->fresh()->invalidated_at);
        Mail::assertSent(LoginVerificationMail::class, 2);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
