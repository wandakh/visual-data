<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\DatabaseExport;
use App\Imports\DatabaseImport;
use App\Models\Database;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;


class ExcelController extends Controller
{

    public function import(Request $request)
    {
        // jadi tabel hpp itu required nya harus diisi tapi karna ga semuanya punya value harus nambahin ini
        // ALTER TABLE `databases` MODIFY HPP DECIMAL(10, 2) DEFAULT 0.00;
        $request->validate([
            'excel_file' => 'required|mimes:xlsx|max:10240', 
        ]);

        // Proses impor menggunakan Excel facade
        Excel::import(new DatabaseImport(), $request->file('excel_file'));

        // Redirect atau berikan respon sesuai kebutuhan
        return redirect()->route('database')->with('success', 'Data berhasil diimport.');
    }

    public function export(Request $request)
    {
        $filterNama = $request->filter_export;
        $exportType = $request->export_type;
        $data = Database::when($filterNama, function ($query) use ($filterNama) {
            return $query->where('NAMA_CUSTOMER', 'LIKE', '%' . $filterNama . '%');
        })->get();
    
        try {
            if ($exportType == 'summary') {
                // buat nama file, ada tanggalnya
                $filename = 'summary_' . date('Y-m-d') . '.xlsx';
            } else {
                //ambil nama customer buat dijadiin nama file
                $firstCustomer = $data->first()->NAMA_CUSTOMER;
                //ini gantii spasi jadi (_) terus ubah jadi huruf kecil
                $customerName = str_replace(' ', '_', strtolower($firstCustomer));
                // Tambah tipe ekspor ke nama file
                $filename = $customerName . '_' . $exportType . '_' . date('Y-m-d') . '.xlsx';
            }
    
            // Tambahkan baris header ke array data
            $headerRow = ['Tanggal', 'ORG_CODE', 'NAMA_CUSTOMER', 'KODE_PRODUK', 'AMMOUNT', 'HARGA_JUAL', 'TRX', 'TYPE_MITRA', 'AMMOUNT_FIX', 'PRODUK_FIX', 'BUCKET_NAME', 'Type_Produk', 'TYPE_BISNIS', 'REV_INPPN', 'PAJAK', 'REV_EXPPN', 'HPP', 'TOTAL_HPP_INPPN', 'TOTAL_HPP_EXPPN', 'Margin_INPPN', 'Margin_EXPPN', 'Hari', 'Bulan', 'KET_PROD'];
            $data->prepend($headerRow);
    
            // Ekspor data ke file Excel
            return Excel::download(new DatabaseExport($data), $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('gagal_ekspor', 'Gagal melakukan ekspor data. Error: ' . $e->getMessage());
        }
    }
}    