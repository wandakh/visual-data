@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
            @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Log Login & Export</h1>
    </div>
    <p class="-mt-4 text-sm text-slate-500">Riwayat login, logout, dan export data oleh user &mdash; terpisah dari log perubahan data.</p>

    <!-- Rekap harian -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <p class="text-2xl font-bold text-slate-800">{{ $rekapPerRole['admin'] }}</p>
            <p class="text-xs text-slate-500">Login Admin hari ini</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <p class="text-2xl font-bold text-slate-800">{{ $rekapPerRole['user'] }}</p>
            <p class="text-xs text-slate-500">Login User hari ini</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100 {{ $totalAdmin > 5 ? 'ring-2 ring-red-300' : '' }}">
            <p class="text-2xl font-bold {{ $totalAdmin > 5 ? 'text-red-600' : 'text-slate-800' }}">{{ $totalAdmin }}</p>
            <p class="text-xs {{ $totalAdmin > 5 ? 'text-red-600 font-medium' : 'text-slate-500' }}">
                Total akun Admin {{ $totalAdmin > 5 ? '(melebihi batas 5!)' : '(maks. 5)' }}
            </p>
        </div>
    </div>

    <!-- Status lembur -->
    @if ($sedangAktifDiLuarJamKerja->isNotEmpty())
        <div class="overflow-hidden rounded-2xl bg-amber-50 shadow-sm ring-1 ring-amber-200">
            <div class="flex items-center gap-2 border-b border-amber-200 px-5 py-3">
                @include('partials.icon', ['name' => 'user', 'class' => 'h-4 w-4 text-amber-600'])
                <h2 class="text-sm font-semibold text-amber-800">
                    {{ $sedangAktifDiLuarJamKerja->count() }} user aktif di luar jam kerja
                </h2>
            </div>
            <div class="divide-y divide-amber-100 px-5">
                @foreach ($sedangAktifDiLuarJamKerja as $item)
                    <div class="flex items-center justify-between py-2 text-sm">
                        <span class="font-medium text-slate-700">{{ $item['user']->name }}</span>
                        <span class="text-amber-700">Auto-logout jam {{ $item['auto_logout_at']->format('H:i') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Filter tanggal -->
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        @if (!$showAll)
            <p class="mb-3 flex items-center gap-2 text-xs font-medium text-indigo-600">
                @include('partials.icon', ['name' => 'filter', 'class' => 'h-3.5 w-3.5'])
                Nampilin aktivitas hari ini aja &mdash;
                <a href="{{ route('user-activity-log') }}?show_all=1" class="underline hover:text-indigo-800">Tampilkan Semua</a>
            </p>
        @endif
        <form action="{{ route('user-activity-log') }}" method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Dari tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Sampai tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <button type="submit" class="flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900">
                @include('partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4'])
                Filter
            </button>
        </form>
    </div>

    <!-- Card: Login & Logout -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
        <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-3">
            @include('partials.icon', ['name' => 'user', 'class' => 'h-4 w-4 text-slate-400'])
            <h2 class="text-sm font-semibold text-slate-600">Login & Logout</h2>
        </div>
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
                    @forelse ($loginLogs as $log)
                        @php
                            $warna = match ($log->action) {
                                'login' => 'bg-green-50 text-green-700',
                                'login_failed' => 'bg-red-50 text-red-700',
                                'overtime_request' => 'bg-amber-50 text-amber-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 tabular-nums whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $log->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $warna }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ in_array($log->action, ['login_failed', 'overtime_request']) ? $log->description : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-400">Belum ada aktivitas login</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($loginLogs->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $loginLogs->links() }}</div>
        @endif
    </div>

    <!-- Card: Export -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
        <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-3">
            @include('partials.icon', ['name' => 'download', 'class' => 'h-4 w-4 text-slate-400'])
            <h2 class="text-sm font-semibold text-slate-600">Export Data</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($exportLogs as $log)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 tabular-nums whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $log->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-slate-400">Belum ada export tercatat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($exportLogs->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $exportLogs->links() }}</div>
        @endif
    </div>
</div>
@endsection
