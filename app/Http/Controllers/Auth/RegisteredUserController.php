<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Build validation rules for person-name fields.
     */
    private function nameRules(bool $required = true): array
    {
        return array_filter([
            $required ? 'required' : 'nullable',
            'string',
            'max:255',
            'regex:/^(?=.*\pL)[\pL\s\'.-]+$/u',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value === null || trim((string) $value) === '') {
                    return;
                }

                $normalized = mb_strtolower((string) preg_replace('/[^\pL]/u', '', (string) $value));
                $length = mb_strlen($normalized);
                $vowelCount = preg_match_all('/[aeiouy]/u', $normalized);

                if ($length >= 4 && $vowelCount === 0) {
                    $fail('Please enter a valid '.str_replace('_', ' ', $attribute).'.');
                    return;
                }

                if ($length >= 8 && ($vowelCount / $length) < 0.25) {
                    $fail('Please enter a valid '.str_replace('_', ' ', $attribute).'.');
                }
            },
        ]);
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => $this->nameRules(),
            'last_name' => $this->nameRules(),
            'middle_name' => $this->nameRules(false),
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'student_id' => ['required', 'regex:/^\d{8}$/', 'unique:users,student_id'],
            'yearlevel' => ['nullable', 'string', 'max:50'],
        ], [
            'first_name.regex' => 'First name must contain letters only.',
            'last_name.regex' => 'Last name must contain letters only.',
            'middle_name.regex' => 'Middle name must contain letters only.',
            'student_id.required' => 'Student ID is required.',
            'student_id.regex' => 'Student ID must be exactly 8 digits.',
            'student_id.unique' => 'This Student ID is already registered.',
        ]);

        // Combine first_name, middle_name, and last_name into 'name'
        $fullName = trim($validated['first_name'] . ' ' . 
                         ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . 
                         $validated['last_name']);

        $user = User::create([
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => 'student',
            'student_id' => $validated['student_id'] ?? null,
            'yearlevel' => $validated['yearlevel'] ?? null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('student.dashboard'));
    }
}
