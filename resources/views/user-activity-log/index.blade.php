@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
            @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Log Sesi & Aktivitas User</h1>
    </div>
    <p class="-mt-4 text-sm text-slate-500">Riwayat login, logout, dan percobaan lembur oleh user. Export data sekarang ada di halaman Log Data.</p>

    <!-- Rekap harian -->
    <div class="grid grid-cols-3 gap-2 sm:gap-3">
        <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-100 sm:p-4">
            <p class="text-xl font-bold text-slate-800 sm:text-2xl">{{ $rekapPerRole['admin'] }}</p>
            <p class="text-[11px] text-slate-500 sm:text-xs">Akun Admin login hari ini</p>
        </div>
        <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-100 sm:p-4">
            <p class="text-xl font-bold text-slate-800 sm:text-2xl">{{ $rekapPerRole['user'] }}</p>
            <p class="text-[11px] text-slate-500 sm:text-xs">Akun User login hari ini</p>
        </div>
        <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-100 sm:p-4 {{ $totalAdmin > 5 ? 'ring-2 ring-red-300' : '' }}">
            <p class="text-xl font-bold sm:text-2xl {{ $totalAdmin > 5 ? 'text-red-600' : 'text-slate-800' }}">{{ $totalAdmin }}</p>
            <p class="text-[11px] sm:text-xs {{ $totalAdmin > 5 ? 'text-red-600 font-medium' : 'text-slate-500' }}">
                Total akun Admin{{ $totalAdmin > 5 ? ' (lebih dari 5!)' : ' (maks. 5)' }}
            </p>
        </div>
    </div>

    <!-- Status lembur -->
    @if ($sedangAktifDiLuarJamKerja->isNotEmpty())
        <div>
            <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-amber-700">
                @include('partials.icon', ['name' => 'user', 'class' => 'h-4 w-4 text-amber-600'])
                {{ $sedangAktifDiLuarJamKerja->count() }} user aktif di luar jam kerja
            </h2>
            <div class="overflow-hidden rounded-2xl bg-amber-50 shadow-sm ring-1 ring-amber-200">
                <div class="divide-y divide-amber-100 px-5">
                    @foreach ($sedangAktifDiLuarJamKerja as $item)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <span class="font-medium text-slate-700">{{ $item['user']->name }}</span>
                            <span class="text-amber-700">Auto-logout jam {{ $item['auto_logout_at']->format('H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @include('partials.filter-status-badge', [
        'showAll' => $showAll,
        'urlSemua' => route('user-activity-log') . '?show_all=1',
        'urlHariIni' => route('user-activity-log'),
        'labelHariIni' => 'Menampilkan aktivitas hari ini',
    ])

    <!-- Filter tanggal -->
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <form action="{{ route('user-activity-log') }}" method="GET" class="flex flex-wrap items-end gap-3">
            @if ($showAll)
                <input type="hidden" name="show_all" value="1">
            @endif
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Dari tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Sampai tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <button type="submit" data-loading-text="Memfilter..." class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                @include('partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4'])
                Filter
            </button>
        </form>
    </div>

    <!-- Login & Logout - Admin -->
    <div>
        <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800">
            @include('partials.icon', ['name' => 'user', 'class' => 'h-4 w-4 text-slate-800'])
            Riwayat Login Admin
        </h2>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($adminLoginLogs as $log)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 tabular-nums whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $log->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $log->action === 'login' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $log->action }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3">@include('partials.empty-state', ['icon' => 'user', 'title' => 'Belum ada aktivitas admin'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($adminLoginLogs->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $adminLoginLogs->links('vendor.pagination.custom') }}</div>
            @endif
        </div>
    </div>

    <!-- Login & Logout - User -->
    <div>
        <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800">
            @include('partials.icon', ['name' => 'user', 'class' => 'h-4 w-4 text-slate-800'])
            Riwayat Login User
        </h2>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                            <th class="px-4 py-3 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($userLoginLogs as $log)
                            @php
                                $warna = match ($log->action) {
                                    'login' => 'bg-green-50 text-green-700',
                                    'overtime_request' => 'bg-amber-50 text-amber-700',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 tabular-nums whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $log->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-medium {{ $warna }}">{{ $log->action }}</span></td>
                                <td class="px-4 py-3 text-slate-500">{{ $log->action === 'overtime_request' ? $log->description : '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">@include('partials.empty-state', ['icon' => 'user', 'title' => 'Belum ada aktivitas user'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($userLoginLogs->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $userLoginLogs->links('vendor.pagination.custom') }}</div>
            @endif
        </div>
    </div>

    <!-- Percobaan Login Gagal -->
    <div>
        <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800">
            @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-4 w-4 text-red-800'])
            Riwayat Login Gagal
        </h2>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($failedLoginLogs as $log)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 tabular-nums whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">@include('partials.empty-state', ['icon' => 'check-circle', 'title' => 'Gak ada percobaan gagal', 'subtitle' => 'Bagus, tidak ada indikasi percobaan login yang mencurigakan.'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($failedLoginLogs->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $failedLoginLogs->links('vendor.pagination.custom') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
