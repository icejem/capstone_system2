<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="profile_photo" :value="__('Profile Photo')" />
            <div class="mt-2 flex items-center gap-3">
                @if (!empty($user->profile_photo_path))
                    @php
                        $profilePhotoDisk = config('filesystems.profile_photos_disk', config('filesystems.default', 'public'));
                    @endphp
                    <img
                        src="{{ Storage::disk($profilePhotoDisk)->url($user->profile_photo_path) }}"
                        alt="Profile Photo"
                        style="width:52px;height:52px;border-radius:999px;object-fit:cover;border:1px solid #d1d5db;"
                    >
                @else
                    <div style="width:52px;height:52px;border-radius:999px;background:#ede9fe;color:#5b21b6;display:grid;place-items:center;font-weight:700;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <x-text-input id="profile_photo" name="profile_photo" type="file" class="block w-full" accept="image/png,image/jpeg,image/jpg,image/webp" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @if (($user->user_type ?? '') === 'student')
            <div>
                <x-input-label for="student_id" :value="__('Student ID')" />
                <x-text-input
                    id="student_id"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('student_id', $user->student_id)"
                    readonly
                    disabled
                />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button class="profile-primary-btn">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
