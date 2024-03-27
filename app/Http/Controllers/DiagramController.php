<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Database;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiagramController extends Controller
{
    public function diagram(Request $request)
    {
        // Ambil tanggal yang dipilih dari form
        $selectedDate = $request->input('selectedDate');
        
        try {
            // Jubah formatnya menjadi "d/m/Y"
            $formattedDate = Carbon::createFromFormat('Y-m-d', $selectedDate)->format('d/m/Y');
        } catch (\Exception $e) {
            // kalo ada kesalahan pas proses ubah formatnnya, kasih nilai null buat $formatteddate
            $formattedDate = null;
        }
        
        // Ambil data dari database buat diagram tahunan
        $diagramData = Database::select('Tanggal', 'NAMA_CUSTOMER')->get();
        $customerCounts = [];
        foreach ($diagramData as $data) {
            $namaCustomer = $data->NAMA_CUSTOMER;
            if (!isset($customerCounts[$namaCustomer])) {
                $customerCounts[$namaCustomer] = 1;
            } else {
                $customerCounts[$namaCustomer]++;
            }
        }
        arsort($customerCounts);
        
        // Ambil data dari database buat diagram perhari kalo ada tanggal yang dipilih
        $customerCountsPerhari = null;
        if ($formattedDate) {
            $customerCountsPerhari = Database::whereDate('Tanggal', $formattedDate)
                ->select('NAMA_CUSTOMER')
                ->get()
                ->countBy('NAMA_CUSTOMER');
        }
        
        // Kirim data ke view
        return view('database.diagram', [
            'customerCountsPerhari' => $customerCountsPerhari,
            'customerCounts' => $customerCounts,
            'selectedDate' => $selectedDate,
            'title' => 'Diagram',
        ]);
    }
}
