@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ openDetail: null, deleteTarget: null }">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Data Penjualan</h1>
            <p class="text-sm text-slate-500">{{ $filterActive ? 'Data yang diinput hari ini' : 'Sesuai filter yang aktif' }}</p>
        </div>
        <div class="flex gap-2">
            @can('export-data')
                <button type="button" @click="$dispatch('open-modal', 'export-modal')"
                        class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
                    @include('partials.icon', ['name' => 'download', 'class' => 'h-4 w-4'])
                    Export Excel
                </button>
            @endcan
            @can('import-excel')
                <button type="button" @click="$dispatch('open-modal', 'import-modal')"
                        class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    @include('partials.icon', ['name' => 'upload', 'class' => 'h-4 w-4'])
                    Import Excel
                </button>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-100">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-100">{{ session('error') }}</div>
    @endif
    @if (session('bisalogin'))
        <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-100">{{ session('bisalogin') }}</div>
    @endif

    <!-- Kartu statistik -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                @include('partials.icon', ['name' => 'chart-bar', 'class' => 'h-5 w-5'])
            </div>
            <div>
                <p class="font-display text-xl font-bold text-slate-800">{{ number_format($stats['total_transaksi'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500">Total Transaksi</p>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                @include('partials.icon', ['name' => 'download', 'class' => 'h-5 w-5'])
            </div>
            <div>
                <p class="font-display text-xl font-bold text-slate-800">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500">Total Pendapatan</p>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                @include('partials.icon', ['name' => 'trophy', 'class' => 'h-5 w-5'])
            </div>
            <div>
                <p class="font-display text-xl font-bold text-slate-800">{{ number_format($stats['total_customer'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500">Total Customer</p>
            </div>
        </div>
    </div>

    @include('partials.filter-status-badge', [
        'showAll' => $showAll,
        'urlSemua' => url('/database') . '?show_all=1',
        'urlHariIni' => url('/database'),
        'labelHariIni' => 'Menampilkan data yang diinput hari ini',
    ])

    <!-- Filter -->
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <form action="{{ url('/database') }}" method="GET" class="flex flex-wrap items-end gap-3">
            @if ($showAll)
                <input type="hidden" name="show_all" value="1">
            @endif
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Cari (customer / kode produk)</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-slate-400">
                        @include('partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4'])
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama customer atau kode produk..."
                           class="w-64 rounded-lg border border-slate-300 py-2 pl-8 pr-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Tanggal mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Tanggal akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Customer</label>
                <select name="customer_name" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Customer</option>
                    @foreach ($dropdown['NAMA_CUSTOMER'] as $nama)
                        <option value="{{ $nama }}" {{ request('customer_name') === $nama ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Per halaman</label>
                <select name="per_page" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @foreach ([5, 10, 20, 50, 100] as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" data-loading-text="Memfilter..." class="flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-900">
                @include('partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4'])
                Filter
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="sticky top-0 bg-slate-50/95 text-xs uppercase tracking-wide text-slate-500 backdrop-blur">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Org Code</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Kode Produk</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-right">Harga Jual</th>
                        <th class="px-4 py-3 text-right">TRX</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($data as $item)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-400">{{ $data->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ \Carbon\Carbon::parse($item->Tanggal)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $item->ORG_CODE }}</td>
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $item->NAMA_CUSTOMER }}</td>
                            <td class="px-4 py-3">{{ $item->KODE_PRODUK }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $item->AMMOUNT, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $item->HARGA_JUAL, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $item->TRX }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <button type="button" @click="openDetail = openDetail === {{ $item->id }} ? null : {{ $item->id }}"
                                            class="flex items-center gap-1 rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-medium text-sky-700 hover:bg-sky-100">
                                        @include('partials.icon', ['name' => 'eye', 'class' => 'h-3.5 w-3.5'])
                                        Detail
                                    </button>
                                    @can('edit-data')
                                        <a href="{{ route('tampilkandata', $item->id) }}"
                                           class="flex items-center gap-1 rounded-lg bg-amber-50 px-2.5 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-100">
                                            @include('partials.icon', ['name' => 'pencil', 'class' => 'h-3.5 w-3.5'])
                                            Edit
                                        </a>
                                    @endcan
                                    @can('delete-data')
                                        <button type="button"
                                                @click="deleteTarget = { id: {{ $item->id }}, nama: @js($item->NAMA_CUSTOMER), tanggal: @js(\Carbon\Carbon::parse($item->Tanggal)->format('d/m/Y')), amount: @js(number_format((float) $item->HARGA_JUAL, 0, ',', '.')) }"
                                                class="flex items-center gap-1 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100">
                                            @include('partials.icon', ['name' => 'trash', 'class' => 'h-3.5 w-3.5'])
                                            Hapus
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        <tr x-show="openDetail === {{ $item->id }}" x-cloak>
                            <td colspan="9" class="bg-slate-50/70 px-4 py-4">
                                @include('partials.detail-fields', ['item' => $item])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                @include('partials.empty-state', ['icon' => 'clipboard', 'title' => 'Belum ada data penjualan', 'subtitle' => 'Coba ubah filter tanggal/customer, atau klik Tampilkan Semua.'])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col items-center gap-2 border-t border-slate-100 px-4 py-4 sm:flex-row sm:justify-between">
            <p class="text-xs text-slate-500">
                Menampilkan {{ $data->firstItem() ?? 0 }}&ndash;{{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} data
            </p>
            {{ $data->links('vendor.pagination.custom') }}
        </div>
    </div>

    @can('delete-data')
        @include('partials.modal-delete')
    @endcan
</div>

@include('partials.modal-export')
@include('partials.modal-import')
@endsection
