<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SalesRecord;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DatabaseController extends Controller
{
    public function index(Request $request): View
    {
        $orgCode = auth()->user()->scopedOrgCode();

        $dropdown['NAMA_CUSTOMER'] = SalesRecord::query()
            ->when($orgCode, fn ($q) => $q->where('ORG_CODE', $orgCode))
            ->select('NAMA_CUSTOMER')->distinct()->pluck('NAMA_CUSTOMER', 'NAMA_CUSTOMER');

        $perPage = (int) $request->input('per_page', 10);
        $showAll = $request->boolean('show_all');

        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'customer_name' => $request->input('customer_name'),
            'search' => $request->input('search'),
            'show_all' => $showAll,
        ];

        $baseQuery = fn () => SalesRecord::query()->when($orgCode, fn ($q) => $q->where('ORG_CODE', $orgCode));

        $query = SalesRecord::applyFilters($baseQuery(), $filters, useCreatedAtDefault: true);

        $data = $query->orderBy('id')->paginate($perPage)->withQueryString();

        // Card statistik — dihitung dari scope filter yang SAMA kayak tabel
        // di bawahnya (bukan dari keseluruhan tabel), biar konsisten.
        $statsQuery = SalesRecord::applyFilters($baseQuery(), $filters, useCreatedAtDefault: true);
        $semuaDataTerfilter = $statsQuery->get(['AMMOUNT', 'HARGA_JUAL', 'NAMA_CUSTOMER']);

        $stats = [
            'total_transaksi' => $semuaDataTerfilter->count(),
            'total_pendapatan' => $semuaDataTerfilter->sum(fn ($r) => (float) $r->HARGA_JUAL),
            'total_customer' => $semuaDataTerfilter->pluck('NAMA_CUSTOMER')->unique()->count(),
        ];

        $defaultHariIni = !$showAll && !$filters['start_date'] && !$filters['end_date'];

        return view('sales.index', [
            'data' => $data,
            'dropdown' => $dropdown,
            'title' => 'Home',
            'request' => $request,
            'showAll' => $showAll,
            'filterActive' => $defaultHariIni,
            'stats' => $stats,
        ]);
    }

    public function delete(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|in:salah_ketik,data_duplikat,data_tidak_valid,lainnya',
            'reason_detail' => 'required_if:reason,lainnya|nullable|string|max:255',
        ]);

        $data = SalesRecord::find($id);

        if (!$data) {
            return redirect()->route('database')->with('error', 'Data tidak ditemukan');
        }

        $label = match ($validated['reason']) {
            'salah_ketik' => 'Salah ketik/input',
            'data_duplikat' => 'Data duplikat',
            'data_tidak_valid' => 'Data tidak valid',
            'lainnya' => 'Lainnya: ' . ($validated['reason_detail'] ?? '-'),
        };

        $data->deleted_reason = $validated['reason'];
        $data->save();
        $data->delete(); // soft delete

        ActivityLog::record('deleted', $id, Auth::id(), "Hapus data #{$id} (customer: {$data->NAMA_CUSTOMER}) — Alasan: {$label}");

        return redirect()->route('database')->with('success', 'Data berhasil dihapus (bisa dipulihkan dalam 24 jam lewat menu Data Terhapus)');
    }

    public function show(int $id): SalesRecord
    {
        $data = SalesRecord::findOrFail($id);

        $orgCode = auth()->user()->scopedOrgCode();
        if ($orgCode && $data->ORG_CODE !== $orgCode) {
            abort(403, 'Data ini bukan milik cabang (ORG_CODE) kamu');
        }

        return $data;
    }

    /**
     * Halaman "Data Terhapus". Sebelum nampilin daftar, bersihin dulu (hapus
     * PERMANEN) data yang udah lewat 24 jam di trash -> sesuai kebijakan
     * retensi: lewat 24 jam gak bisa dipulihkan lagi.
     */
    public function trash(Request $request): View
    {
        $kadaluarsa = SalesRecord::onlyTrashed()
            ->where('deleted_at', '<', now()->subDay())
            ->get();

        foreach ($kadaluarsa as $item) {
            ActivityLog::record('deleted', $item->id, null, "Data #{$item->id} (customer: {$item->NAMA_CUSTOMER}) dihapus permanen otomatis setelah 24 jam di trash");
        }

        SalesRecord::onlyTrashed()->where('deleted_at', '<', now()->subDay())->forceDelete();

        $showAll = $request->boolean('show_all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$showAll && !$startDate && !$endDate) {
            $startDate = $endDate = today()->toDateString();
        }

        $query = SalesRecord::onlyTrashed();

        if ($startDate) {
            $query->whereDate('deleted_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('deleted_at', '<=', $endDate);
        }

        $data = (clone $query)->latest('deleted_at')->paginate(15)->withQueryString();

        // Widget rekap: total hapus per alasan, dalam scope filter yang sama.
        $rekapAlasan = (clone $query)->selectRaw('deleted_reason, COUNT(*) as total')
            ->groupBy('deleted_reason')
            ->pluck('total', 'deleted_reason');

        return view('sales.trash', [
            'data' => $data,
            'title' => 'Data Terhapus',
            'showAll' => $showAll,
            'rekapAlasan' => $rekapAlasan,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $data = SalesRecord::onlyTrashed()->find($id);

        if (!$data) {
            return redirect()->route('database.trash')->with('error', 'Data tidak ditemukan di daftar terhapus');
        }

        // Jaga-jaga: kalau restore diakses langsung lewat URL (bukan dari
        // halaman trash yang udah nge-purge otomatis), tetap cegah restore
        // data yang harusnya udah kadaluarsa.
        if ($data->deleted_at->lt(now()->subDay())) {
            return redirect()->route('database.trash')->with('error', 'Data ini sudah lebih dari 24 jam di trash, gak bisa dipulihkan lagi');
        }

        $data->restore();

        ActivityLog::record('restored', $id, Auth::id(), "Pulihkan data #{$id} (customer: {$data->NAMA_CUSTOMER})");

        return redirect()->route('database.trash')->with('success', 'Data berhasil dipulihkan');
    }
}
