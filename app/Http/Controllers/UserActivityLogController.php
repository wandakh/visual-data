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

    
        $adminRoleIds = User::role('admin')->pluck('id');
        $karyawanRoleIds = User::role('user')->pluck('id');

        $adminLoginLogsQuery = UserActivityLog::with('user')
            ->whereIn('action', ['login', 'logout'])
            ->whereIn('user_id', $adminRoleIds);
        $applyDateFilter($adminLoginLogsQuery);
        $adminLoginLogs = $adminLoginLogsQuery->latest('created_at')->paginate(10, ['*'], 'admin_login_page')->withQueryString();

        $userLoginLogsQuery = UserActivityLog::with('user')
            ->whereIn('action', ['login', 'logout', 'overtime_request'])
            ->whereIn('user_id', $karyawanRoleIds);
        $applyDateFilter($userLoginLogsQuery);
        $userLoginLogs = $userLoginLogsQuery->latest('created_at')->paginate(10, ['*'], 'user_login_page')->withQueryString();

        $failedLoginLogsQuery = UserActivityLog::where('action', 'login_failed');
        $applyDateFilter($failedLoginLogsQuery);
        $failedLoginLogs = $failedLoginLogsQuery->latest('created_at')->paginate(10, ['*'], 'failed_login_page')->withQueryString();

        // Rekap harian (selalu HARI INI, gak ikut filter tanggal di atas —
        // ini soal status "sekarang", bukan riwayat). Ngitung akun UNIK,
        // bukan jumlah aktivitas.
        $loginHariIni = UserActivityLog::where('action', 'login')->whereDate('created_at', today())->get();
        $userIdUnikLoginHariIni = $loginHariIni->pluck('user_id')->filter()->unique();

        $rekapPerRole = ['admin' => 0, 'user' => 0];
        foreach ($userIdUnikLoginHariIni as $uid) {
            $u = User::find($uid);
            if (!$u) {
                continue;
            }
            if ($u->hasRole('admin')) {
                $rekapPerRole['admin']++;
            } elseif ($u->hasRole('user')) {
                $rekapPerRole['user']++;
            }
        }

        $totalAdmin = User::role('admin')->count();

        // "Sedang aktif" (heuristik): ada login hari ini, TAPI belum ada
        // logout SETELAH login terakhirnya.
        $jamKerjaSelesai = today()->setTime(18, 0);
        $sedangAktifDiLuarJamKerja = collect();

        if (now()->gt($jamKerjaSelesai) || now()->lt(today()->setTime(7, 0))) {
            foreach ($userIdUnikLoginHariIni as $userId) {
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
            'adminLoginLogs' => $adminLoginLogs,
            'userLoginLogs' => $userLoginLogs,
            'failedLoginLogs' => $failedLoginLogs,
            'title' => 'Log Sesi',
            'showAll' => $showAll,
            'rekapPerRole' => $rekapPerRole,
            'totalAdmin' => $totalAdmin,
            'sedangAktifDiLuarJamKerja' => $sedangAktifDiLuarJamKerja,
        ]);
    }
}
