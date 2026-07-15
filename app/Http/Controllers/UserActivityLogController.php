<?php

namespace App\Http\Controllers;

use App\Models\OvertimeRequest;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $showAll = $request->boolean('show_all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$showAll && !$startDate && !$endDate) {
            $startDate = $endDate = today()->toDateString();
        }

        $applyDateFilter = function ($query) use ($startDate, $endDate) {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        };

        $loginLogsQuery = UserActivityLog::with('user')->whereIn('action', ['login', 'logout', 'login_failed', 'overtime_request']);
        $applyDateFilter($loginLogsQuery);
        $loginLogs = $loginLogsQuery->latest('created_at')->paginate(10, ['*'], 'login_page')->withQueryString();

        $exportLogsQuery = UserActivityLog::with('user')->where('action', 'export');
        $applyDateFilter($exportLogsQuery);
        $exportLogs = $exportLogsQuery->latest('created_at')->paginate(10, ['*'], 'export_page')->withQueryString();

        // Rekap harian (selalu HARI INI, gak ikut filter tanggal di atas —
        // ini soal status "sekarang", bukan riwayat).
        $loginHariIni = UserActivityLog::with('user')
            ->where('action', 'login')
            ->whereDate('created_at', today())
            ->get();

        $rekapPerRole = [
            'admin' => $loginHariIni->filter(fn ($log) => $log->user?->hasRole('admin'))->count(),
            'user' => $loginHariIni->filter(fn ($log) => $log->user?->hasRole('user'))->count(),
        ];

        $totalAdmin = User::role('admin')->count();

        // "Sedang aktif" (heuristik): ada login hari ini, TAPI belum ada
        // logout SETELAH login terakhirnya. Dipakai buat nunjukin siapa yang
        // masih login di luar jam kerja + kapan bakal di-auto-logout.
        $jamKerjaSelesai = today()->setTime(18, 0);
        $sedangAktifDiLuarJamKerja = collect();

        if (now()->gt($jamKerjaSelesai) || now()->lt(today()->setTime(7, 0))) {
            $userIds = $loginHariIni->pluck('user_id')->unique();

            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if (!$user || $user->hasRole('admin')) {
                    continue; // admin gak kena batasan jam kerja
                }

                $loginTerakhir = $loginHariIni->where('user_id', $userId)->sortByDesc('created_at')->first();
                $logoutSetelahnya = UserActivityLog::where('user_id', $userId)
                    ->where('action', 'logout')
                    ->where('created_at', '>', $loginTerakhir->created_at)
                    ->exists();

                if (!$logoutSetelahnya) {
                    $overtime = OvertimeRequest::activeFor($user);
                    $sedangAktifDiLuarJamKerja->push([
                        'user' => $user,
                        'auto_logout_at' => $overtime?->granted_until ?? $jamKerjaSelesai,
                    ]);
                }
            }
        }

        return view('user-activity-log.index', [
            'loginLogs' => $loginLogs,
            'exportLogs' => $exportLogs,
            'title' => 'Log Login & Export',
            'showAll' => $showAll,
            'rekapPerRole' => $rekapPerRole,
            'totalAdmin' => $totalAdmin,
            'sedangAktifDiLuarJamKerja' => $sedangAktifDiLuarJamKerja,
        ]);
    }
}
