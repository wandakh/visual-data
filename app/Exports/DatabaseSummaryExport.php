<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Beda dengan DatabaseExport (semua kolom mentah per baris transaksi),
 * export "Summary" ini isinya REKAP per customer: total transaksi, total
 * amount, total margin. Cocok buat laporan ringkas, bukan data mentah.
 */
class DatabaseSummaryExport implements FromCollection, WithHeadings
{
    public function __construct(protected Collection $data)
    {
    }

    public function collection(): Collection
    {
        return $this->data
            ->groupBy('NAMA_CUSTOMER')
            ->map(function (Collection $rows, string $customer) {
                return [
                    'NAMA_CUSTOMER' => $customer,
                    'JUMLAH_TRANSAKSI' => $rows->count(),
                    'TOTAL_AMMOUNT' => $rows->sum(fn ($r) => (float) $r->AMMOUNT),
                    'TOTAL_HARGA_JUAL' => $rows->sum(fn ($r) => (float) $r->HARGA_JUAL),
                    'TOTAL_MARGIN_INPPN' => $rows->sum(fn ($r) => (float) $r->Margin_INPPN),
                    'TOTAL_MARGIN_EXPPN' => $rows->sum(fn ($r) => (float) $r->Margin_EXPPN),
                ];
            })
            ->values();
    }

    public function headings(): array
    {
        return [
            'Nama Customer', 'Jumlah Transaksi', 'Total Amount',
            'Total Harga Jual', 'Total Margin In PPN', 'Total Margin Ex PPN',
        ];
    }
}
