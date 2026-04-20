<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\StudentRegistrationRoster;
use App\Models\User;
use App\Rules\GmailAddress;
use App\Rules\RealName;
use App\Rules\StrongPassword;
use App\Services\SmsNotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private function normalizeRosterName(?string $value): string
    {
        $normalized = trim((string) $value);
        $normalized = (string) preg_replace('/\s+/u', ' ', $normalized);

        return Str::lower($normalized);
    }

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

    private function passwordRules(): array
    {
        return [
            'required',
            'confirmed',
            'min:8',
            new StrongPassword(),
        ];
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
            'phone_number' => trim((string) $request->input('phone_number')),
        ]);

        $validated = $request->validate([
            'first_name' => $this->nameRules(),
            'last_name' => $this->nameRules(),
            'middle_name' => $this->nameRules(false),
            'email' => ['required', 'string', 'email', 'max:255', new GmailAddress(), 'unique:'.User::class],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! SmsNotificationService::normalizePhoneNumber((string) $value)) {
                        $fail('Please enter a valid Philippine mobile number (e.g. 09171234567).');
                    }
                },
                'unique:users,phone_number',
            ],
            'password' => $this->passwordRules(),
            'student_id' => ['required', 'regex:/^\d{8}$/', 'unique:users,student_id'],
            'year_level' => ['required', Rule::in(User::yearLevels())],
            'terms_accepted' => ['accepted'],
            'privacy_accepted' => ['accepted'],
        ], [
            'email.email' => 'Please enter a valid Gmail address.',
            'email.unique' => 'This Gmail address is already registered.',
            'phone_number.required' => 'Mobile number is required for SMS reminders.',
            'phone_number.unique' => 'This mobile number is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Password must be at least 8 characters long.',
            'student_id.required' => 'Student ID is required.',
            'student_id.regex' => 'Student ID must be exactly 8 digits.',
            'student_id.unique' => 'This Student ID is already registered.',
            'year_level.required' => 'Year level is required.',
            'year_level.in' => 'Please choose a valid year level from the list.',
            'terms_accepted.accepted' => 'Please read and accept the Terms and Conditions before creating your account.',
            'privacy_accepted.accepted' => 'Please read and accept the Privacy Policy before creating your account.',
        ]);

        $latestBatchToken = StudentRegistrationRoster::query()
            ->orderByDesc('created_at')
            ->value('batch_token');

        if (! $latestBatchToken) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_id' => 'Student registration is not available yet. Please wait for the admin to upload the allowed student list.',
                ]);
        }

        $isEligibleStudent = StudentRegistrationRoster::query()
            ->where('batch_token', $latestBatchToken)
            ->where('student_id', (string) $validated['student_id'])
            ->whereRaw('LOWER(first_name) = ?', [$this->normalizeRosterName($validated['first_name'])])
            ->whereRaw('LOWER(last_name) = ?', [$this->normalizeRosterName($validated['last_name'])])
            ->exists();

        if (! $isEligibleStudent) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_id' => 'You are not allowed to create a student account. Please make sure your Student ID, first name, and last name match the official imported student list.',
                ]);
        }

        $fullName = trim($validated['first_name'].' '.
            ($validated['middle_name'] ? $validated['middle_name'].' ' : '').
            $validated['last_name']);

        $user = User::create([
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'email' => $validated['email'],
            'phone_number' => SmsNotificationService::normalizePhoneNumber($validated['phone_number']),
            'password' => Hash::make($validated['password']),
            'user_type' => 'student',
            'account_status' => 'active',
            'student_id' => $validated['student_id'] ?? null,
            'year_level' => $validated['year_level'],
            'yearlevel' => User::legacyYearLevelValue($validated['year_level']),
        ]);

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Your account has been created. Please log in to continue.');
    }
}
