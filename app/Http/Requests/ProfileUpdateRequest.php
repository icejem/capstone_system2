<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Services\SmsNotificationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique(User::class)->ignore($this->user()->id),
                function (string $attribute, mixed $value, \Closure $fail) {
                    $raw = trim((string) $value);
                    if ($raw !== '' && ! SmsNotificationService::normalizePhoneNumber($raw)) {
                        $fail('Please enter a valid Philippine mobile number (e.g. 09171234567).');
                    }
                },
            ],
            'year_level' => [
                Rule::requiredIf(fn () => ($this->user()?->user_type ?? '') === 'student'),
                'nullable',
                Rule::in(User::yearLevels()),
            ],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
