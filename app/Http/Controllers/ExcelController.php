<?php

namespace App\Http\Controllers;

use App\Exports\DatabaseExport;
use App\Exports\DatabaseSummaryExport;
use App\Imports\DatabaseImport;
use App\Models\ActivityLog;
use App\Models\SalesRecord;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:102400',
        ]);

        try {
            $import = new DatabaseImport();
            Excel::import($import, $request->file('excel_file'));

            if ($import->imported === 0) {
                return redirect()->route('database')->with('error',
                    'Import selesai tapi 0 baris berhasil masuk. Kemungkinan besar nama '
                    . 'kolom header di baris pertama file Excel kamu beda dengan yang '
                    . 'sistem kenali (Tanggal, ORG_CODE, NAMA_CUSTOMER, dst). Cek nama '
                    . 'header di file kamu, atau kirim contoh file-nya buat dicek lagi.'
                );
            }

            $pesan = "{$import->imported} baris data berhasil diimport";
            if ($import->dilewati > 0) {
                $pesan .= ", {$import->dilewati} baris dilewati (kosong atau header-nya gak dikenali)";
            }
            // Diperbaiki: sekarang keterangan log import juga nyantumin
            // rentang tanggal dari data yang diimport.
            if ($import->tanggalTerawal && $import->tanggalTerakhir) {
                $pesan .= ". Rentang tanggal: {$import->tanggalTerawal} s/d {$import->tanggalTerakhir}";
            }

            ActivityLog::record('imported', null, Auth::id(), $pesan);

            return redirect()->route('database')->with('success', $pesan);
        } catch (\Throwable $e) {
            return redirect()->route('database')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $filterNama = $request->filter_export;
        $exportType = $request->export_type;

        $data = SalesRecord::query()
            ->when($filterNama, fn ($query) => $query->where('NAMA_CUSTOMER', 'LIKE', '%' . $filterNama . '%'))
            ->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal melakukan ekspor: data tidak ditemukan');
        }

        try {
            if ($exportType === 'summary') {
                $filename = 'summary_' . date('Y-m-d') . '.xlsx';
                // Diperbaiki: sebelumnya "Summary" isinya SAMA PERSIS dengan
                // "Semua Kolom" (cuma beda nama file). Sekarang beneran beda
                // struktur: summary = rekap per customer, bukan data mentah.
                $export = new DatabaseSummaryExport($data);
            } else {
                $customerName = str_replace(' ', '_', strtolower($data->first()->NAMA_CUSTOMER));
                $filename = $customerName . '_' . $exportType . '_' . date('Y-m-d') . '.xlsx';
                $export = new DatabaseExport($data);
            }

            $keterangan = $filterNama
                ? "Export data customer \"{$filterNama}\" ({$exportType}), {$data->count()} baris"
                : "Export semua data ({$exportType}), {$data->count()} baris";
            UserActivityLog::record('export', Auth::id(), $keterangan);

            return Excel::download($export, $filename);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal melakukan ekspor data: ' . $e->getMessage());
        }
    }
}
