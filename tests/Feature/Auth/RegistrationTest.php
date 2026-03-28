<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'middle_name' => '',
            'email' => 'testuser@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'student_id' => '20240001',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('student.dashboard', absolute: false));
    }

    public function test_registration_rejects_gibberish_names(): void
    {
        $response = $this->from('/register')->post('/register', [
            'first_name' => 'hadhsdgafgmfdfdghgd',
            'last_name' => 'User',
            'middle_name' => '',
            'email' => 'validuser@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'student_id' => '20240002',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['first_name']);
        $this->assertGuest();
    }

    public function test_registration_requires_a_gmail_address(): void
    {
        $response = $this->from('/register')->post('/register', [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'middle_name' => '',
            'email' => 'maria@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'student_id' => '20240003',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
}
