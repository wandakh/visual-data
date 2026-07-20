<?php

namespace App\Http\Controllers;

use App\Models\SalesRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiagramController extends Controller
{
    private const NAMA_BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function diagram(Request $request): View
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $orgCode = $user->scopedOrgCode();

        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $namaPerusahaan = $request->input('nama_perusahaan');

        $query = SalesRecord::query()->when($orgCode, fn ($q) => $q->where('ORG_CODE', $orgCode));

        if ($tanggal) {
            $query->whereDate('Tanggal', $tanggal);
        }
        if ($bulan) {
            $query->whereMonth('Tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('Tanggal', $tahun);
        }
        if ($namaPerusahaan) {
            $query->where('NAMA_CUSTOMER', $namaPerusahaan);
        }

        $filtered = $query->get();

        // Ringkasan "Transaksi Terbanyak" (dipakai dua-duanya, admin & karyawan)
        $customerCounts = $filtered->countBy('NAMA_CUSTOMER')->sortDesc();
        $topCustomerName = $customerCounts->keys()->first();
        $topCustomerCount = $customerCounts->first();
        $topCustomerTransactions = $topCustomerName
            ? $filtered->where('NAMA_CUSTOMER', $topCustomerName)->sortByDesc('Tanggal')->values()
            : collect();

        $dropdown['NAMA_CUSTOMER'] = SalesRecord::query()
            ->when($orgCode, fn ($q) => $q->where('ORG_CODE', $orgCode))
            ->select('NAMA_CUSTOMER')->distinct()->pluck('NAMA_CUSTOMER', 'NAMA_CUSTOMER');

        $tahunTersedia = SalesRecord::query()
            ->when($orgCode, fn ($q) => $q->where('ORG_CODE', $orgCode))
            ->selectRaw('DISTINCT YEAR(Tanggal) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $data = [
            'customerCounts' => $customerCounts,
            'topCustomerName' => $topCustomerName,
            'topCustomerCount' => $topCustomerCount,
            'topCustomerTransactions' => $topCustomerTransactions,
            'dropdown' => $dropdown,
            'namaBulan' => self::NAMA_BULAN,
            'tahunTersedia' => $tahunTersedia,
            'filters' => compact('tanggal', 'bulan', 'tahun', 'namaPerusahaan'),
            'title' => 'Analitik',
            'isAdmin' => $isAdmin,
        ];

        if ($isAdmin) {
            return view('sales.diagram', array_merge($data, $this->chartsAdmin($filtered)));
        }

        return view('sales.diagram', array_merge($data, $this->chartsKaryawan($filtered)));
    }

    /**
     * Admin: helicopter view — tren pendapatan vs keuntungan GLOBAL, ranking
     * antar cabang, top customer GLOBAL (lintas cabang), komposisi produk.
     */
    private function chartsAdmin($filtered): array
    {
        $trenHarian = $filtered
            ->groupBy(fn ($r) => Carbon::parse($r->Tanggal)->format('Y-m-d'))
            ->map(fn ($rows) => [
                'pendapatan' => round($rows->sum(fn ($r) => (float) $r->HARGA_JUAL)),
                'keuntungan' => round($rows->sum(fn ($r) => (float) $r->Margin_INPPN + (float) $r->Margin_EXPPN)),
            ])
            ->sortKeys();

        $topCabang = $filtered
            ->groupBy('ORG_CODE')
            ->map(fn ($rows, $org) => [
                'org_code' => $org,
                'jumlah_transaksi' => $rows->count(),
                'pendapatan' => round($rows->sum(fn ($r) => (float) $r->HARGA_JUAL)),
            ])
            ->sortByDesc('jumlah_transaksi')
            ->take(10)
            ->values();

        $topCustomerGlobal = $filtered
            ->groupBy('NAMA_CUSTOMER')
            ->map(fn ($rows, $nama) => [
                'nama' => $nama,
                'pendapatan' => round($rows->sum(fn ($r) => (float) $r->HARGA_JUAL)),
            ])
            ->sortByDesc('pendapatan')
            ->take(10)
            ->values();

        $komposisiProduk = $filtered
            ->groupBy(fn ($r) => $r->Type_Produk ?: 'Lainnya')
            ->map->count()
            ->sortDesc()
            ->take(8);

        return compact('trenHarian', 'topCabang', 'topCustomerGlobal', 'komposisiProduk');
    }

    /**
     * Karyawan: fokus ke cabang sendiri. $filtered udah otomatis kefilter ke
     * ORG_CODE mereka dari query utama, jadi gak perlu filter ulang di sini.
     * Data margin/keuntungan SENGAJA gak dihitung/dikirim sama sekali.
     */
    private function chartsKaryawan($filtered): array
    {
        $trenTransaksiHarian = $filtered
            ->groupBy(fn ($r) => Carbon::parse($r->Tanggal)->format('Y-m-d'))
            ->map->count()
            ->sortKeys();

        $topCustomerCabang = $filtered
            ->groupBy('NAMA_CUSTOMER')
            ->map(fn ($rows, $nama) => [
                'nama' => $nama,
                'pendapatan' => round($rows->sum(fn ($r) => (float) $r->HARGA_JUAL)),
            ])
            ->sortByDesc('pendapatan')
            ->take(10)
            ->values();

        $produkTerlarisCabang = $filtered
            ->groupBy(fn ($r) => $r->Type_Produk ?: 'Lainnya')
            ->map->count()
            ->sortDesc()
            ->take(8);

        return compact('trenTransaksiHarian', 'topCustomerCabang', 'produkTerlarisCabang');
    }
}
