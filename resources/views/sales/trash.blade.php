@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ openDetail: null, deleteTarget: null, restoreTarget: null }">
        <div class="flex items-center gap-3">
        <a href="{{ route('database') }}" class="flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 hover:bg-slate-200">
            @include('partials.icon', ['name' => 'chevron-left', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Data Terhapus</h1>
    </div>
    <p class="-mt-4 text-sm text-slate-500">Data yang dihapus bisa dipulihkan dalam 24 jam. Lewat dari itu, otomatis terhapus permanen.</p>

    <!-- Widget rekap per alasan -->
    <div class="flex w-full gap-3 sm:gap-4 overflow-x-auto">
        @forelse (\App\Models\SalesRecord::ALASAN_HAPUS as $value => $label)
        <div class="flex-1 rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-100 sm:p-4">
            <p class="text-2xl font-bold text-slate-800">{{ $rekapAlasan[$value] ?? 0 }}</p>
            <p class="text-xs text-slate-500">{{ $label }}</p>
        </div>
        @empty
        <p class="w-full text-sm text-slate-500">No records found.</p>
        @endforelse
    </div>

    @include('partials.filter-status-badge', [
        'showAll' => $showAll,
        'urlSemua' => route('database.trash') . '?show_all=1',
        'urlHariIni' => route('database.trash'),
        'labelHariIni' => 'Menampilkan yang dihapus hari ini',
        'labelSemua' => 'Menampilkan semua data terhapus',
    ])

    <!-- Filter tanggal -->
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <form action="{{ route('database.trash') }}" method="GET" class="flex flex-wrap items-end gap-3">
            @if ($showAll)
                <input type="hidden" name="show_all" value="1">
            @endif
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Dihapus dari tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Sampai tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <button type="submit" data-loading-text="Memfilter..." class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                @include('partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4'])
                Filter
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Alasan Hapus</th>
                        <th class="px-4 py-3 text-left">Dihapus pada</th>
                        <th class="px-4 py-3 text-left">Sisa waktu</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($data as $item)
                        @php
                            $batasWaktu = $item->deleted_at->addDay();
                            $totalMenit = max(0, (int) now()->diffInMinutes($batasWaktu, false));
                            $sisaJam = intdiv($totalMenit, 60);
                            $sisaMenit = $totalMenit % 60;
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-400">{{ $data->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ \Carbon\Carbon::parse($item->Tanggal)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $item->NAMA_CUSTOMER }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ \App\Models\SalesRecord::ALASAN_HAPUS[$item->deleted_reason] ?? '-' }}</td>
                            <td class="px-4 py-3 tabular-nums text-slate-500">{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <span title="Setelah waktu ini habis, data dihapus permanen dan gak bisa dipulihkan lagi" class="rounded-full px-2 py-1 text-xs font-medium {{ $totalMenit <= 240 ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $sisaJam }}j {{ $sisaMenit }}m lagi
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                               <button type="button" @click="restoreTarget = { url: '{{ route('database.restore', $item->id) }}', nama: @js($item->NAMA_CUSTOMER) }"
                                class="ml-auto flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100">
                        @include('partials.icon', ['name' => 'restore', 'class' => 'h-3.5 w-3.5'])
                        Pulihkan
                            </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                @include('partials.empty-state', ['icon' => 'trash', 'title' => 'Gak ada data terhapus', 'subtitle' => 'Data yang dihapus bakal muncul di sini selama 24 jam.'])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-4 py-4">
            {{ $data->links('vendor.pagination.custom') }}
        </div>
    </div>
    @include('partials.modal-pulihkan')
</div>

@endsection
