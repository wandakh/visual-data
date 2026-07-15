<?php

namespace App\Http\Middleware;

use App\Models\OvertimeRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * User biasa cuma boleh akses web ini jam 07:00-18:00. Di luar jam itu,
 * kecuali lagi punya "akses lembur" yang masih berlaku, mereka diarahkan ke
 * halaman /lembur (bukan langsung di-logout paksa) — supaya tetap bisa
 * request akses lembur dari situ kalau memang perlu.
 *
 * Admin gak kena batasan ini sama sekali (akses 24 jam).
 */
class RestrictAccessHours
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->hasRole('admin')) {
            return $next($request);
        }

        $sekarang = now();
        $dalamJamKerja = $sekarang->between(
            $sekarang->copy()->setTime(7, 0),
            $sekarang->copy()->setTime(18, 0)
        );

        if ($dalamJamKerja || OvertimeRequest::activeFor($user)) {
            return $next($request);
        }

        return redirect()->route('overtime.blocked');
    }
}
