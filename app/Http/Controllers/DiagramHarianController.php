<?php

namespace App\Http\Controllers;
use App\Models\Database;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiagramHarianController extends Controller
{
    
        public function perhari(Request $request)
        {
            // Ambil tanggal yang dipilih dari form
            $selectedDate = $request->input('selectedDate');
        
            // Ambil data dari database untuk diagram perhari
            $customerCountsPerhari = Database::whereDate('Tanggal', $selectedDate)
                ->select('NAMA_CUSTOMER')
                ->get()
                ->countBy('NAMA_CUSTOMER');
        
            // Kirim data untuk diagram perhari ke view, termasuk variabel $selectedDate
            return view('database.diagram',[
                'customerCountsPerhari' => $customerCountsPerhari,
                'selectedDate' => $selectedDate,
                'title' => 'Diagram ',
            ]);
        }
    }

    class DiagramController extends Controller
    {
        public function diagram(Request $request)
        {
            // Ambil data dari database
            $diagramData = Database::select('Tanggal', 'NAMA_CUSTOMER')->get();
        
            // Array untuk menyimpan jumlah kemunculan setiap nama pelanggan
                    $customerCounts = [];
        
            // Hitung kemunculan setiap nama pelanggan
            foreach ($diagramData as $data) {
                $namaCustomer = $data->NAMA_CUSTOMER;
        
                // Tambahkan jumlah kemunculan nama pelanggan ke dalam array
                if (!isset($customerCounts[$namaCustomer])) {
                    $customerCounts[$namaCustomer] = 1;
                } else {
                    $customerCounts[$namaCustomer]++;
                }
            }
        
            // Urutkan array berdasarkan jumlah kemunculan pelanggan (descending)
            arsort($customerCounts);
        
            // Kirim data 
            return view('database.diagram')->with([
                'customerCounts' => $customerCounts,
                'title' => 'Diagram',
            ]);
        }    
    }
    
