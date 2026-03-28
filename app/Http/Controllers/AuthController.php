<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ─── Login ───────────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login_error' => 'Invalid email or password. Please try again.',
            ])->withInput($request->only('email'));
        }

        session([
            'user_id'    => $user->id,
            'user_name'  => $user->full_name,
            'user_email' => $user->email,
        ]);

        return redirect()->route('dashboard');
    }

    // ─── Register ────────────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name'            => 'required|string|max:100',
            'last_name'             => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ], [
            'email.unique'                  => 'This email address is already registered.',
            'password_confirmation.same'    => 'Password and Re-Password do not match.',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    // ─── Logout ──────────────────────────────────────────────────────────────

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }

    // ─── AJAX: Check email unique ─────────────────────────────────────────────

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)
                      ->when($request->user_id, fn($q) => $q->where('id', '!=', $request->user_id))
                      ->exists();

        return response()->json(['exists' => $exists]);
    }
}
