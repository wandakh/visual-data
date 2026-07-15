<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SalesRecord;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UpdateController extends Controller
{
    public function tampilkandata(int $id): View
    {
        $data = SalesRecord::findOrFail($id);

        return view('sales.edit', [
            'data' => $data,
            'dropdownData' => SalesRecord::dropdownOptions(),
            'title' => 'Update Data',
        ]);
    }

    public function updatedata(Request $request, int $id): RedirectResponse
    {
        $data = SalesRecord::findOrFail($id);

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

        $data->update($validated);

        ActivityLog::record('updated', $id, Auth::id(), "Update data #{$id} (customer: {$data->NAMA_CUSTOMER})");

        return redirect()->route('database')->with('success', 'Data berhasil diupdate');
    }
}
