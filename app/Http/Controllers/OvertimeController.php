<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRequest;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OvertimeController extends Controller
{
    /**
     * Halaman yang muncul buat user biasa yang akses di luar jam kerja
     * (07:00-18:00) dan belum punya akses lembur aktif.
     */
    public function blocked(): View|RedirectResponse
    {
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('database');
        }

        $sekarang = now();
        $dalamJamKerja = $sekarang->between(
            $sekarang->copy()->setTime(7, 0),
            $sekarang->copy()->setTime(18, 0)
        );

        if ($dalamJamKerja || OvertimeRequest::activeFor(Auth::user())) {
            return redirect()->route('database');
        }

        return view('overtime.blocked', ['title' => 'Di Luar Jam Kerja']);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15|max:480', // maks 8 jam
        ]);

        $grantedUntil = now()->addMinutes($validated['duration_minutes']);

        OvertimeRequest::create([
            'user_id' => Auth::id(),
            'reason' => $validated['reason'],
            'duration_minutes' => $validated['duration_minutes'],
            'granted_until' => $grantedUntil,
        ]);

        UserActivityLog::record(
            'overtime_request',
            Auth::id(),
            Auth::user()->name . " minta akses lembur {$validated['duration_minutes']} menit — alasan: {$validated['reason']} (berlaku sampai {$grantedUntil->format('H:i')})"
        );

        return redirect()->route('database')->with('success', "Akses lembur diberikan sampai jam {$grantedUntil->format('H:i')}");
    }
}
