<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\SmsNotificationService;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['profile_photo']);
        $profilePhotoDisk = config('filesystems.profile_photos_disk', 'public');
        $oldPhoneNumber = (string) ($request->user()->phone_number ?? '');
        if (array_key_exists('phone_number', $data)) {
            $data['phone_number'] = SmsNotificationService::normalizePhoneNumber($data['phone_number']);
        }
        if (array_key_exists('year_level', $data)) {
            $data['yearlevel'] = User::legacyYearLevelValue($data['year_level']);
        }

        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($request->user()->profile_photo_path) {
                Storage::disk($profilePhotoDisk)->delete($request->user()->profile_photo_path);
            }

            $request->user()->profile_photo_path = $request->file('profile_photo')->store('profile-photos', $profilePhotoDisk);
        }

        $request->user()->save();

        $newPhoneNumber = (string) ($request->user()->phone_number ?? '');
        if ($newPhoneNumber !== '' && $newPhoneNumber !== $oldPhoneNumber) {
            SmsNotificationService::send(
                $newPhoneNumber,
                'Your mobile number was updated successfully and this number is now active for SMS notifications.',
                [
                    'user_id' => $request->user()->id,
                    'user_type' => $request->user()->user_type,
                    'source' => 'profile_phone_update_confirmation',
                ]
            );
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
