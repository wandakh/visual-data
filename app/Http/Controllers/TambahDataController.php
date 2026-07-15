<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SalesRecord;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TambahDataController extends Controller
{
    public function tampilkanCreateForm(): View
    {
        return view('sales.create', [
            'dropdownData' => SalesRecord::dropdownOptions(),
            'title' => 'Create Data',
        ]);
    }

    /**
     * Endpoint JSON: dipanggil lewat AJAX dari form create/edit begitu user
     * milih/ngetik nama customer, buat nyesuain isi dropdown kolom lain
     * (ORG_CODE, KODE_PRODUK, dst) sesuai histori data customer itu aja.
     */
    public function optionsForCustomer(Request $request): \Illuminate\Http\JsonResponse
    {
        $customer = $request->query('customer');

        if (!$customer) {
            return response()->json(SalesRecord::dropdownOptions());
        }

        return response()->json(SalesRecord::dropdownOptionsForCustomer($customer));
    }

    public function insertdata(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'Tanggal' => 'required|date',
            'ORG_CODE' => 'required',
            'NAMA_CUSTOMER' => 'required',
            'KODE_PRODUK' => 'required',
            'AMMOUNT' => 'required',
            'HARGA_JUAL' => 'required',
            'TRX' => 'required',
            'TYPE_MITRA' => 'required',
            'AMMOUNT_FIX' => 'required',
            'PRODUK_FIX' => 'required',
            'BUCKET_NAME' => 'required',
            'Type_Produk' => 'required',
            'TYPE_BISNIS' => 'required',
            'REV_INPPN' => 'required',
            'PAJAK' => 'required',
            'REV_EXPPN' => 'required',
            'HPP' => 'required',
            'TOTAL_HPP_INPPN' => 'required',
            'TOTAL_HPP_EXPPN' => 'required',
            'Margin_INPPN' => 'required',
            'Margin_EXPPN' => 'required',
            'Hari' => 'required',
            'Bulan' => 'required',
            'KET_PROD' => 'required',
        ]);

        $data = SalesRecord::create($validated);

        ActivityLog::record('created', $data->id, Auth::id(), "Tambah data baru #{$data->id} (customer: {$data->NAMA_CUSTOMER})");

        return redirect()->route('database')->with('success', 'Data berhasil ditambahkan');
    }
}
