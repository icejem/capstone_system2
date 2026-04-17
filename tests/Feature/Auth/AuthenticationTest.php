<?php

namespace Tests\Feature\Auth;

use App\Mail\LoginVerificationMail;
use App\Models\LoginVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_users_are_authenticated_only_after_clicking_the_login_verification_link(): void
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

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('student.dashboard', absolute: false));
        $this->assertNotNull($verification->fresh()->verified_at);
        $this->assertNotNull($verification->fresh()->consumed_at);
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
