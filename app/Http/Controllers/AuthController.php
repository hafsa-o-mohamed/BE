<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->hasAccessToDashboard()) {
                return redirect()->intended('dashboard');
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'You do not have access to the dashboard.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Update the user's password.
     *
     * Expects the request to have:
     * - current_password: The user's current password.
     * - password: The new password.
     * - password_confirmation: Confirmation for the new password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Regenerate session for added security.
        $request->session()->regenerate();

        return back()->with('status', 'Password changed successfully.');
    }

    /**
     * Update the user's email.
     *
     * Expects the request to have:
     * - email: A new email address (must be unique).
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $user = $request->user();

        if ($user->email === $request->email) {
            return back()->withErrors([
                'email' => 'The new email must be different from the current one.',
            ]);
        }

        $user->email = $request->email;
        $user->save();

        return back()->with('status', 'Email updated successfully.');
    }

    /**
     * Update the user's phone number.
     *
     * Expects the request to have:
     * - phone: A new phone number (must be unique).
     */
    public function updatePhone(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'unique:users,phone'],
        ]);

        $user = $request->user();
        $user->phone = $request->phone;
        $user->save();

        return back()->with('status', 'Phone number updated successfully.');
    }
}