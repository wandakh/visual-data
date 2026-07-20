<?php

namespace App\Http\Controllers;

use App\Exports\DatabaseExport;
use App\Exports\DatabaseSummaryExport;
use App\Imports\DatabaseImport;
use App\Models\ActivityLog;
use App\Models\SalesRecord;
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

        $orgCode = Auth::user()->scopedOrgCode();

        try {
            $import = new DatabaseImport($orgCode);
            Excel::import($import, $request->file('excel_file'));

            if ($import->imported === 0) {
                $pesanGagal = 'Import selesai tapi 0 baris berhasil masuk. ';
                $pesanGagal .= $import->ditolakOrgCode > 0
                    ? "Semua {$import->ditolakOrgCode} baris ditolak karena ORG_CODE-nya bukan cabang kamu ({$orgCode})."
                    : 'Kemungkinan besar nama kolom header di baris pertama file Excel kamu beda dengan yang sistem kenali (Tanggal, ORG_CODE, NAMA_CUSTOMER, dst).';

                return redirect()->route('database')->with('error', $pesanGagal);
            }

            $pesan = "{$import->imported} baris data berhasil diimport";
            if ($import->dilewati > 0) {
                $pesan .= ", {$import->dilewati} baris dilewati (kosong/header gak dikenali)";
            }
            if ($import->ditolakOrgCode > 0) {
                $pesan .= ", {$import->ditolakOrgCode} baris ditolak (ORG_CODE bukan cabang kamu)";
            }
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
        $exportType = $request->export_type;
        $orgCode = Auth::user()->scopedOrgCode();

        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'customer_name' => $request->input('filter_export') ?: $request->input('customer_name'),
            'search' => $request->input('search'),
            'show_all' => $request->boolean('show_all'),
        ];

        $query = SalesRecord::query()->when($orgCode, fn ($q) => $q->where('ORG_CODE', $orgCode));
        $data = SalesRecord::applyFilters($query, $filters, useCreatedAtDefault: true)->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal melakukan ekspor: data tidak ditemukan (sesuai filter yang lagi aktif)');
        }

        try {
            if ($exportType === 'summary') {
                $filename = 'summary_' . date('Y-m-d') . '.xlsx';
                $export = new DatabaseSummaryExport($data);
            } else {
                $customerName = str_replace(' ', '_', strtolower($data->first()->NAMA_CUSTOMER));
                $filename = $customerName . '_' . $exportType . '_' . date('Y-m-d') . '.xlsx';
                $export = new DatabaseExport($data);
            }

            $keterangan = $filters['customer_name']
                ? "Export data customer \"{$filters['customer_name']}\" ({$exportType}), {$data->count()} baris"
                : "Export semua data ({$exportType}), {$data->count()} baris";
            // Diperbaiki: export sekarang tercatat di Log Data (satu tempat
            // sama import), bukan lagi di Log Login & Export.
            ActivityLog::record('exported', null, Auth::id(), $keterangan);

            return Excel::download($export, $filename);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal melakukan ekspor data: ' . $e->getMessage());
        }
    }
}
