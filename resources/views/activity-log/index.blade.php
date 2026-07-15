@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
            @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Log Data</h1>
    </div>
    <p class="-mt-4 text-sm text-slate-500">Riwayat tambah, edit, hapus, pulihkan, dan import data &mdash; terpisah dari log login/export.</p>

    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        @if (!$showAll)
            <p class="mb-3 flex items-center gap-2 text-xs font-medium text-indigo-600">
                @include('partials.icon', ['name' => 'filter', 'class' => 'h-3.5 w-3.5'])
                Nampilin aktivitas hari ini aja &mdash;
                <a href="{{ route('activity-log') }}?show_all=1" class="underline hover:text-indigo-800">Tampilkan Semua</a>
            </p>
        @endif
        <form action="{{ route('activity-log') }}" method="GET" class="flex flex-wrap items-end gap-3">
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

    <!-- Card: Perubahan Data -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
        <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-3">
            @include('partials.icon', ['name' => 'pencil', 'class' => 'h-4 w-4 text-slate-400'])
            <h2 class="text-sm font-semibold text-slate-600">Perubahan Data</h2>
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
                    @forelse ($dataLogs as $log)
                        @php
                            $warna = match ($log->action) {
                                'created' => 'bg-green-50 text-green-700',
                                'updated' => 'bg-amber-50 text-amber-700',
                                'deleted' => 'bg-red-50 text-red-700',
                                'restored' => 'bg-sky-50 text-sky-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 tabular-nums whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $log->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $warna }}">{{ $log->action }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-400">Belum ada perubahan data tercatat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($dataLogs->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $dataLogs->links() }}</div>
        @endif
    </div>

    <!-- Card: Import Excel -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
        <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-3">
            @include('partials.icon', ['name' => 'upload', 'class' => 'h-4 w-4 text-slate-400'])
            <h2 class="text-sm font-semibold text-slate-600">Import Excel</h2>
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
                    @forelse ($importLogs as $log)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 tabular-nums whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $log->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-slate-400">Belum ada import tercatat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($importLogs->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $importLogs->links() }}</div>
        @endif
    </div>
</div>
@endsection
