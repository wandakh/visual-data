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
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $namaPerusahaan = $request->input('nama_perusahaan');

        $query = SalesRecord::query();

        // Semua filter opsional & bisa dikombinasikan. Sengaja diturunkan dari
        // kolom Tanggal (bukan kolom Bulan/Hari yang diisi manual saat input
        // data), supaya hasilnya konsisten dan gak kena masalah data yang
        // diketik beda-beda formatnya.
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

        // Chart 1: jumlah transaksi per customer, dalam scope filter yang aktif
        $customerCounts = $filtered->countBy('NAMA_CUSTOMER')->sortDesc();

        // Chart 2: tren jumlah transaksi per bulan, dalam scope filter yang aktif
        $perBulan = $filtered
            ->groupBy(fn ($item) => Carbon::parse($item->Tanggal)->format('Y-m'))
            ->map->count()
            ->sortKeys();

        // Ringkasan: customer dengan transaksi terbanyak + detail transaksinya
        $topCustomerName = $customerCounts->keys()->first();
        $topCustomerCount = $customerCounts->first();
        $topCustomerTransactions = $topCustomerName
            ? $filtered->where('NAMA_CUSTOMER', $topCustomerName)->sortByDesc('Tanggal')->values()
            : collect();

        $dropdown['NAMA_CUSTOMER'] = SalesRecord::query()
            ->select('NAMA_CUSTOMER')->distinct()->pluck('NAMA_CUSTOMER', 'NAMA_CUSTOMER');

        $tahunTersedia = SalesRecord::query()
            ->selectRaw('DISTINCT YEAR(Tanggal) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('sales.diagram', [
            'customerCounts' => $customerCounts,
            'perBulan' => $perBulan,
            'topCustomerName' => $topCustomerName,
            'topCustomerCount' => $topCustomerCount,
            'topCustomerTransactions' => $topCustomerTransactions,
            'dropdown' => $dropdown,
            'namaBulan' => self::NAMA_BULAN,
            'tahunTersedia' => $tahunTersedia,
            'filters' => [
                'tanggal' => $tanggal,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nama_perusahaan' => $namaPerusahaan,
            ],
            'title' => 'Diagram',
        ]);
    }
}
