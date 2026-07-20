<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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

        UserActivityLog::record('login_failed', null, "Percobaan login gagal untuk email: {$credentials['email']}");

        return redirect('/sesi')->with('loginError', 'Login gagal, cek kembali email/password kamu');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $userName = Auth::user()->name;
            UserActivityLog::record('logout', Auth::id(), "{$userName} logout dari sistem");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/sesi')->with('berhasil', 'Berhasil logout');
    }
}
