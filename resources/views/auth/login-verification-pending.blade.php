@extends('layouts.auth')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Check Your Email</h1>
            <p class="text-gray-600 mt-2">We've sent you a verification link</p>
        </div>

        <!-- Content -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <p class="text-gray-700 text-center">
                A verification link has been sent to:
                <br>
                <strong class="text-blue-600 block mt-2">{{ $email }}</strong>
            </p>
        </div>

        <!-- Instructions -->
        <div class="space-y-4 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-500 text-white text-sm font-bold">
                    1
                </div>
                <p class="ml-3 text-gray-700">Click the verification link in the email</p>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-500 text-white text-sm font-bold">
                    2
                </div>
                <p class="ml-3 text-gray-700">You'll be logged in automatically</p>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full bg-blue-500 text-white text-sm font-bold">
                    3
                </div>
                <p class="ml-3 text-gray-700">Link expires in <strong>10 minutes</strong></p>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <p class="ml-3 text-sm text-amber-700">
                    <strong>Security Tip:</strong> We'll never ask you for your password via email.
                </p>
            </div>
        </div>

        <!-- Resend Section -->
        <div class="border-t pt-6">
            <p class="text-gray-600 text-sm text-center mb-4">
                Didn't receive the email?
            </p>
            <form action="{{ route('auth.resend-verification') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button
                    type="submit"
                    class="w-full bg-white text-blue-600 border border-blue-600 font-semibold py-2 px-4 rounded-lg hover:bg-blue-50 transition-colors duration-200"
                >
                    Resend Verification Email
                </button>
            </form>
        </div>

        <!-- Back to Login -->
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                Back to Login
            </a>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Common Issues</h3>

        <div class="space-y-4">
            <div>
                <h4 class="font-medium text-gray-900 text-sm mb-1">Link not working?</h4>
                <p class="text-gray-600 text-sm">Your link may have expired. Request a new one above.</p>
            </div>

            <div>
                <h4 class="font-medium text-gray-900 text-sm mb-1">Check spam folder</h4>
                <p class="text-gray-600 text-sm">Sometimes verification emails end up in spam. Please check.</p>
            </div>

            <div>
                <h4 class="font-medium text-gray-900 text-sm mb-1">Still having trouble?</h4>
                <p class="text-gray-600 text-sm">
                    Contact support at <a href="mailto:support@consultation.edu" class="text-blue-600 hover:underline">support@consultation.edu</a>
                </p>
            </div>
        </div>
    </div>
</div>

@if ($message = session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('{{ $message }}');
        });
    </script>
@endif

@if ($message = session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('{{ $message }}');
        });
    </script>
@endif

@endsection
