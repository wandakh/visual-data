@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="font-display text-2xl font-bold tracking-tight text-slate-800">Diagram</h1>

    <!-- Filter -->
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-600">
            @include('partials.icon', ['name' => 'filter', 'class' => 'h-4 w-4 text-slate-400'])
            Filter
        </div>
        <form action="{{ route('diagram') }}" method="GET" class="flex flex-wrap items-end gap-3">
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
<option value="{{ $nama }}" {{ ($filters['nama_perusahaan'] ?? '') === $nama ? 'selected' : '' }}>{{ $nama }}</option>                    @endforeach
                </select>
            </div>
            <button type="submit" data-loading-text="Menerapkan..." class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
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
    </div>

    <!-- Ringkasan transaksi terbanyak — signature card -->
    <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-[#0f1729] via-[#1a2540] to-indigo-900 p-6 shadow-sm">
        @if ($topCustomerName)
            <div class="flex flex-wrap items-start justify-between gap-6">
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
                <div class="flex gap-6 text-right">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-indigo-300">Total Pendapatan</p>
                        <p class="font-display text-xl font-bold text-white">Rp {{ number_format($topCustomerTransactions->sum(fn($t) => (float) $t->HARGA_JUAL), 0, ',', '.') }}</p>
                    </div>
                    @if ($isAdmin)
                        <div>
                            <p class="text-xs uppercase tracking-wider text-indigo-300">Total Keuntungan</p>
                            <p class="font-display text-xl font-bold text-emerald-300">Rp {{ number_format($topCustomerTransactions->sum(fn($t) => (float) $t->Margin_INPPN + (float) $t->Margin_EXPPN), 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <p class="text-sm text-indigo-200">Belum ada data yang cocok dengan filter ini.</p>
        @endif
    </div>

    @if ($isAdmin)
        {{-- ================= CHART ADMIN (Helicopter View) ================= --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100 lg:col-span-2">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-600">
                    @include('partials.icon', ['name' => 'chart-bar', 'class' => 'h-4 w-4 text-slate-400'])
                    Tren Pendapatan vs Keuntungan
                </h2>
                <canvas id="chartTrenAdmin" height="90"></canvas>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-600">
                    @include('partials.icon', ['name' => 'building', 'class' => 'h-4 w-4 text-slate-400'])
                    Top Performa Cabang
                </h2>
                <canvas id="chartTopCabang" height="260"></canvas>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-600">
                    @include('partials.icon', ['name' => 'trophy', 'class' => 'h-4 w-4 text-slate-400'])
                    Top Customer Global
                </h2>
                <canvas id="chartTopCustomerGlobal" height="260"></canvas>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100 lg:col-span-2">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-600">
                    @include('partials.icon', ['name' => 'chart-bar', 'class' => 'h-4 w-4 text-slate-400'])
                    Komposisi Penjualan per Tipe Produk
                </h2>
                <div class="mx-auto max-w-sm">
                    <canvas id="chartKomposisi"></canvas>
                </div>
            </div>
        </div>
    @else
        {{-- ================= CHART KARYAWAN (Fokus Cabang Sendiri) ================= --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100 lg:col-span-2">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-600">
                    @include('partials.icon', ['name' => 'chart-bar', 'class' => 'h-4 w-4 text-slate-400'])
                    Tren Transaksi Harian
                </h2>
                <canvas id="chartTrenKaryawan" height="90"></canvas>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-600">
                    @include('partials.icon', ['name' => 'trophy', 'class' => 'h-4 w-4 text-slate-400'])
                    Top Customer Cabang
                </h2>
                <canvas id="chartTopCustomerCabang" height="260"></canvas>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-600">
                    @include('partials.icon', ['name' => 'chart-bar', 'class' => 'h-4 w-4 text-slate-400'])
                    Produk Terlaris Cabang
                </h2>
                <canvas id="chartProdukTerlaris" height="260"></canvas>
            </div>
        </div>
    @endif

    <!-- Detail transaksi milik customer dengan transaksi terbanyak -->
    @if ($topCustomerName)
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-3">
                @include('partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4 text-slate-400'])
                <h2 class="text-sm font-semibold text-slate-600">
                    Detail Transaksi &mdash; {{ $topCustomerName }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Kode Produk</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-right">Harga Jual</th>
                            <th class="px-4 py-3 text-right">TRX</th>
                            <th class="px-4 py-3 text-left">Type Mitra</th>
                            @can('view-margin')
                                <th class="px-4 py-3 text-right">Margin In PPN</th>
                                <th class="px-4 py-3 text-right">Margin Ex PPN</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($topCustomerTransactions as $trx)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 tabular-nums">{{ \Carbon\Carbon::parse($trx->Tanggal)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $trx->KODE_PRODUK }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $trx->AMMOUNT, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $trx->HARGA_JUAL, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $trx->TRX }}</td>
                                <td class="px-4 py-3">{{ $trx->TYPE_MITRA }}</td>
                                @can('view-margin')
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $trx->Margin_INPPN, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $trx->Margin_EXPPN, 0, ',', '.') }}</td>
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
    // Palet warna baku dipakai di SELURUH chart aplikasi ini:
    // biru/indigo = pendapatan, hijau = keuntungan/positif, amber = netral/cabang,
    // violet = customer, slate = kategori lain-lain.
    const WARNA = {
        pendapatan: { line: 'rgb(79, 70, 229)', fill: 'rgba(79, 70, 229, 0.12)' },
        keuntungan: { line: 'rgb(16, 185, 129)', fill: 'rgba(16, 185, 129, 0.12)' },
        cabang: 'rgba(245, 158, 11, 0.75)',
        customer: 'rgba(99, 102, 241, 0.75)',
        komposisi: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16'],
    };

    const formatRupiah = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);

    const tooltipRupiah = {
        callbacks: {
            label: (ctx) => `${ctx.dataset.label}: ${formatRupiah(ctx.parsed.y ?? ctx.parsed.x ?? ctx.parsed)}`,
        },
    };
    const tooltipAngka = {
        callbacks: {
            label: (ctx) => `${ctx.dataset.label || ctx.label}: ${new Intl.NumberFormat('id-ID').format(ctx.parsed.y ?? ctx.parsed.x ?? ctx.parsed)}`,
        },
    };

    @if ($isAdmin)
        const trenHarian = @json($trenHarian);
        new Chart(document.getElementById('chartTrenAdmin'), {
            type: 'line',
            data: {
                labels: Object.keys(trenHarian),
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: Object.values(trenHarian).map(v => v.pendapatan),
                        borderColor: WARNA.pendapatan.line,
                        backgroundColor: WARNA.pendapatan.fill,
                        fill: true, tension: 0.3,
                    },
                    {
                        label: 'Keuntungan',
                        data: Object.values(trenHarian).map(v => v.keuntungan),
                        borderColor: WARNA.keuntungan.line,
                        backgroundColor: WARNA.keuntungan.fill,
                        fill: true, tension: 0.3,
                    },
                ],
            },
            options: {
                plugins: { tooltip: tooltipRupiah },
                scales: { y: { beginAtZero: true, ticks: { callback: formatRupiah } } },
            },
        });

        const topCabang = @json($topCabang);
        new Chart(document.getElementById('chartTopCabang'), {
            type: 'bar',
            data: {
                labels: topCabang.map(c => c.org_code),
                datasets: [{ label: 'Jumlah Transaksi', data: topCabang.map(c => c.jumlah_transaksi), backgroundColor: WARNA.cabang, borderRadius: 6 }],
            },
            options: { plugins: { tooltip: tooltipAngka }, scales: { y: { beginAtZero: true } } },
        });

        const topCustomerGlobal = @json($topCustomerGlobal);
        new Chart(document.getElementById('chartTopCustomerGlobal'), {
            type: 'bar',
            data: {
                labels: topCustomerGlobal.map(c => c.nama),
                datasets: [{ label: 'Pendapatan', data: topCustomerGlobal.map(c => c.pendapatan), backgroundColor: WARNA.customer, borderRadius: 6 }],
            },
            options: {
                indexAxis: 'y',
                plugins: { tooltip: tooltipRupiah },
                scales: { x: { beginAtZero: true, ticks: { callback: formatRupiah } } },
            },
        });

        const komposisi = @json($komposisiProduk);
        new Chart(document.getElementById('chartKomposisi'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(komposisi),
                datasets: [{ data: Object.values(komposisi), backgroundColor: WARNA.komposisi }],
            },
            options: { plugins: { tooltip: tooltipAngka, legend: { position: 'bottom' } } },
        });
    @else
        const trenTransaksiHarian = @json($trenTransaksiHarian);
        new Chart(document.getElementById('chartTrenKaryawan'), {
            type: 'line',
            data: {
                labels: Object.keys(trenTransaksiHarian),
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: Object.values(trenTransaksiHarian),
                    borderColor: WARNA.pendapatan.line,
                    backgroundColor: WARNA.pendapatan.fill,
                    fill: true, tension: 0.3,
                }],
            },
            options: { plugins: { tooltip: tooltipAngka }, scales: { y: { beginAtZero: true } } },
        });

        const topCustomerCabang = @json($topCustomerCabang);
        new Chart(document.getElementById('chartTopCustomerCabang'), {
            type: 'bar',
            data: {
                labels: topCustomerCabang.map(c => c.nama),
                datasets: [{ label: 'Pendapatan', data: topCustomerCabang.map(c => c.pendapatan), backgroundColor: WARNA.customer, borderRadius: 6 }],
            },
            options: {
                indexAxis: 'y',
                plugins: { tooltip: tooltipRupiah },
                scales: { x: { beginAtZero: true, ticks: { callback: formatRupiah } } },
            },
        });

        const produkTerlaris = @json($produkTerlarisCabang);
        new Chart(document.getElementById('chartProdukTerlaris'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(produkTerlaris),
                datasets: [{ data: Object.values(produkTerlaris), backgroundColor: WARNA.komposisi }],
            },
            options: { plugins: { tooltip: tooltipAngka, legend: { position: 'bottom' } } },
        });
    @endif
</script>
@endsection
