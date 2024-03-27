<?php

namespace App\Imports;

use App\Models\Database;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DatabaseImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $excelDate = Date::excelToDateTimeObject($row[0]);

        return new Database([
            'Tanggal' => $excelDate,
            'ORG_CODE' => $row[1],
            'NAMA_CUSTOMER' => $row[2],
            'KODE_PRODUK' => $row[3],
            'AMMOUNT' => $row[4],
            'HARGA_JUAL' => $row[5],
            'TRX' => $row[6],
            'TYPE_MITRA' => $row[7],
            'AMMOUNT_FIX' => $row[8],
            'PRODUK_FIX' => $row[9],
            'BUCKET_NAME' => $row[10],
            'Type_Produk' => $row[11],
            'TYPE_BISNIS' => $row[12],
            'REV_INPPN' => $row[13],
            'PAJAK' => $row[14],
            'REV_EXPPN' => $row[15],
            'HPP' => $row[16],
            'TOTAL_HPP_INPPN' => $row[17],
            'TOTAL_HPP_EXPPN' => $row[18],
            'Margin_INPPN' => $row[19],
            'Margin_EXPPN' => $row[20],
            'Hari' => $row[21],                                                 
            'Bulan' => $row[22],
            'KET_PROD' => $row[23],


        ]);
    }
}
