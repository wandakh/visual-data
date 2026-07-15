<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(): View
    {
        return view('auth.login', ['title' => 'Login']);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            UserActivityLog::record('login', Auth::id(), Auth::user()->name . ' login ke sistem');

            return redirect('/database')->with('bisalogin', Auth::user()->name . ' berhasil login');
        }

        // Dicatat juga kalau gagal (tanpa nyimpen password-nya), biar kalau
        // ada percobaan brute-force/tebak-tebak password kelihatan di log.
        UserActivityLog::record('login_failed', null, "Percobaan login gagal untuk email: {$credentials['email']}");

        return redirect('/sesi')->with('loginError', 'Login gagal, cek kembali email/password kamu');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $userName = Auth::user()->name;
            UserActivityLog::record('logout', $userId, "{$userName} logout dari sistem");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/sesi')->with('berhasil', 'Berhasil logout');
    }

    public function register(): View
    {
        return view('auth.register', ['title' => 'Register']);
    }

    public function create(Request $request): RedirectResponse
    {
        // Diperbaiki: kebijakan password diperketat (sebelumnya minimal 5
        // karakter tanpa syarat lain — gampang ditebak).
        $validated = $request->validate([
            'name' => ['required', 'max:255', 'regex:/^[A-Z][a-zA-Z]*(\s[A-Z][a-zA-Z]*)*$/'],
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('user');

        return redirect('/sesi')->with('berhasil', 'Register berhasil, silahkan login');
    }
}
