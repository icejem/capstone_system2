<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\GmailAddress;
use App\Rules\RealName;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Build validation rules for person-name fields.
     */
    private function nameRules(bool $required = true): array
    {
        return array_values(array_filter([
            $required ? 'required' : 'nullable',
            'string',
            new RealName(),
        ]));
    }

    private function normalizeName(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);
        $normalized = (string) preg_replace('/\s+/u', ' ', $normalized);

        return $normalized === '' ? null : $normalized;
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
        $request->merge([
            'first_name' => $this->normalizeName($request->input('first_name')),
            'last_name' => $this->normalizeName($request->input('last_name')),
            'middle_name' => $this->normalizeName($request->input('middle_name')),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'first_name' => $this->nameRules(),
            'last_name' => $this->nameRules(),
            'middle_name' => $this->nameRules(false),
            'email' => ['required', 'string', 'email', 'max:255', new GmailAddress(), 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'student_id' => ['required', 'regex:/^\d{8}$/', 'unique:users,student_id'],
            'yearlevel' => ['nullable', 'string', 'max:50'],
        ], [
            'email.email' => 'Please enter a valid Gmail address.',
            'email.unique' => 'This Gmail address is already registered.',
            'student_id.required' => 'Student ID is required.',
            'student_id.regex' => 'Student ID must be exactly 8 digits.',
            'student_id.unique' => 'This Student ID is already registered.',
        ]);

        $fullName = trim($validated['first_name'].' '.
            ($validated['middle_name'] ? $validated['middle_name'].' ' : '').
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
