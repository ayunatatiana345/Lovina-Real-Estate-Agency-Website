<?php

// Tara handles admin authentication here.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $settings = CompanySetting::getSettings();
        return view('admin.auth.login', compact('settings'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'login' => 'The email or password you entered is incorrect.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->setRememberToken(\Illuminate\Support\Str::random(60));
            $user->save();
        }

        $recallerCookie = Auth::guard('web')->getRecallerName();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget($recallerCookie));

        foreach ($request->cookies->keys() as $cookieName) {
            if (str_starts_with($cookieName, 'remember_')) {
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget($cookieName));
            }
        }

        return redirect()->route('home')
            ->withCookie(\Illuminate\Support\Facades\Cookie::forget($recallerCookie))
            ->with('success', 'Logged out successfully.');
    }
}
