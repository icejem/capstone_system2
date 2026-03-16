<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'user_type' => 'required|in:student,instructor',
        ]);

        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        Auth::login($user);
        
        // Track the session
        UserSessionService::createSession($user);
        
        if ($user->user_type === 'student') {
            return redirect()->route('student.dashboard');
        }
        if ($user->user_type === 'instructor') {
            return redirect()->route('instructor.dashboard');
        }
        return redirect('/');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Track the session
            UserSessionService::createSession($user);
            
            if ($user && $user->user_type === 'student') {
                return redirect()->route('student.dashboard');
            }
            if ($user && $user->user_type === 'instructor') {
                return redirect()->route('instructor.dashboard');
            }
            if ($user && $user->user_type === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            $request->session()->forget('url.intended');
            return redirect('/');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        // Track session logout and calculate active minutes
        $user = Auth::user();
        if ($user) {
            UserSessionService::endSession($user);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
