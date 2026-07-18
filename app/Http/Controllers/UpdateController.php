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
        $orgCode = Auth::user()->scopedOrgCode();

        // Diperbaiki: Karyawan cuma boleh edit data ORG_CODE-nya sendiri.
        if ($orgCode && $data->ORG_CODE !== $orgCode) {
            abort(403, 'Data ini bukan milik cabang (ORG_CODE) kamu, gak bisa diedit.');
        }

        return view('sales.edit', [
            'data' => $data,
            'dropdownData' => SalesRecord::dropdownOptions($orgCode),
            'lockedOrgCode' => $orgCode,
            'title' => 'Update Data',
        ]);
    }

    public function updatedata(Request $request, int $id): RedirectResponse
    {
        $data = SalesRecord::findOrFail($id);
        $orgCode = Auth::user()->scopedOrgCode();

        if ($orgCode && $data->ORG_CODE !== $orgCode) {
            abort(403, 'Data ini bukan milik cabang (ORG_CODE) kamu, gak bisa diedit.');
        }

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

        // Karyawan gak bisa ganti ORG_CODE data ke cabang lain lewat edit.
        if ($orgCode) {
            $validated['ORG_CODE'] = $orgCode;
        }

        $data->update($validated);

        ActivityLog::record('updated', $id, Auth::id(), "Update data #{$id} (customer: {$data->NAMA_CUSTOMER})");

        return redirect()->route('database')->with('success', 'Data berhasil diupdate');
    }
}
