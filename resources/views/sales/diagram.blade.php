@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Diagram</h1>

    <!-- Filter -->
    <form action="{{ route('diagram') }}" method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Tanggal spesifik</label>
            <input type="date" name="tanggal" value="{{ $filters['tanggal'] }}"
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Bulan</label>
            <select name="bulan" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">Semua Bulan</option>
                @foreach ($namaBulan as $angka => $nama)
                    <option value="{{ $angka }}" {{ (string) $filters['bulan'] === (string) $angka ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Tahun</label>
            <select name="tahun" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">Semua Tahun</option>
                @foreach ($tahunTersedia as $tahun)
                    <option value="{{ $tahun }}" {{ (string) $filters['tahun'] === (string) $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Nama Perusahaan</label>
            <select name="nama_perusahaan" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">Semua Perusahaan</option>
                @foreach ($dropdown['NAMA_CUSTOMER'] as $nama)
                    <option value="{{ $nama }}" {{ $filters['nama_perusahaan'] === $nama ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            @include('partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4'])
            Terapkan Filter
        </button>
        @if (array_filter($filters))
            <a href="{{ route('diagram') }}" class="flex items-center gap-1 rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-50">
                @include('partials.icon', ['name' => 'x-mark', 'class' => 'h-4 w-4'])
                Reset
            </a>
        @endif
    </form>

    <!-- Ringkasan transaksi terbanyak — signature card -->
    <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-[#0f1729] via-[#1a2540] to-indigo-900 p-6 shadow-sm">
        @if ($topCustomerName)
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/10 text-amber-300">
                    @include('partials.icon', ['name' => 'trophy', 'class' => 'h-6 w-6'])
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-indigo-300">Transaksi Terbanyak &middot; sesuai filter</p>
                    <h2 class="font-display mt-1 text-2xl font-bold tracking-tight text-white">{{ $topCustomerName }}</h2>
                    <span class="mt-2 inline-flex rounded-full bg-white/10 px-3 py-1 text-sm font-medium text-indigo-100">
                        {{ $topCustomerCount }} transaksi
                    </span>
                </div>
            </div>
        @else
            <p class="text-sm text-indigo-200">Belum ada data yang cocok dengan filter ini.</p>
        @endif
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <h2 class="mb-3 text-sm font-semibold text-slate-600">Transaksi per Customer</h2>
            <canvas id="chartCustomer" height="280"></canvas>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <h2 class="mb-3 text-sm font-semibold text-slate-600">Tren Transaksi per Bulan</h2>
            <canvas id="chartBulan" height="280"></canvas>
        </div>
    </div>

    <!-- Detail transaksi milik customer dengan transaksi terbanyak -->
    @if ($topCustomerName)
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="border-b border-slate-100 px-5 py-3">
                <h2 class="text-sm font-semibold text-slate-600">
                    Detail Transaksi &mdash; {{ $topCustomerName }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Kode Produk</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">Harga Jual</th>
                            <th class="px-4 py-3 text-left">TRX</th>
                            <th class="px-4 py-3 text-left">Type Mitra</th>
                            @can('view-margin')
                                <th class="px-4 py-3 text-left">Margin In PPN</th>
                                <th class="px-4 py-3 text-left">Margin Ex PPN</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($topCustomerTransactions as $trx)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($trx->Tanggal)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $trx->KODE_PRODUK }}</td>
                                <td class="px-4 py-3">{{ $trx->AMMOUNT }}</td>
                                <td class="px-4 py-3">{{ $trx->HARGA_JUAL }}</td>
                                <td class="px-4 py-3">{{ $trx->TRX }}</td>
                                <td class="px-4 py-3">{{ $trx->TYPE_MITRA }}</td>
                                @can('view-margin')
                                    <td class="px-4 py-3">{{ $trx->Margin_INPPN }}</td>
                                    <td class="px-4 py-3">{{ $trx->Margin_EXPPN }}</td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const customerCounts = @json($customerCounts);
    const perBulan = @json($perBulan);

    new Chart(document.getElementById('chartCustomer'), {
        type: 'bar',
        data: {
            labels: Object.keys(customerCounts),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: Object.values(customerCounts),
                backgroundColor: 'rgba(99, 102, 241, 0.6)',
            }],
        },
        options: { scales: { y: { beginAtZero: true } } },
    });

    new Chart(document.getElementById('chartBulan'), {
        type: 'line',
        data: {
            labels: Object.keys(perBulan),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: Object.values(perBulan),
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.15)',
                fill: true,
                tension: 0.3,
            }],
        },
        options: { scales: { y: { beginAtZero: true } } },
    });
</script>
@endsection
