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
        $orgCode = Auth::user()->scopedOrgCode();

        return view('sales.create', [
            'dropdownData' => SalesRecord::dropdownOptions($orgCode),
            'lockedOrgCode' => $orgCode,
            'title' => 'Input Data',
        ]);
    }

    public function optionsForCustomer(Request $request): \Illuminate\Http\JsonResponse
    {
        $customer = $request->query('customer');
        $orgCode = Auth::user()->scopedOrgCode();

        if (!$customer) {
            return response()->json(SalesRecord::dropdownOptions($orgCode));
        }

        return response()->json(SalesRecord::dropdownOptionsForCustomer($customer, $orgCode));
    }

    public function insertdata(Request $request): RedirectResponse
    {
        $orgCode = Auth::user()->scopedOrgCode();

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

      
        if ($orgCode) {
            $validated['ORG_CODE'] = $orgCode;
        }

        $data = SalesRecord::create($validated);

        ActivityLog::record('created', $data->id, Auth::id(), "Tambah data baru #{$data->id} (customer: {$data->NAMA_CUSTOMER})");

        return redirect()->route('database')->with('success', 'Data berhasil ditambahkan');
    }
}
