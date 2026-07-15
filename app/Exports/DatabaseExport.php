<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Diperbaiki: sebelumnya header kolom ditambahin manual di controller dengan
 * cara nge-prepend array biasa ke Collection Eloquent (rawan berantakan kalau
 * kolom berubah). Sekarang pakai WithHeadings + WithMapping yang lebih standar.
 */
class DatabaseExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected Collection $data)
    {
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Tanggal', 'ORG_CODE', 'NAMA_CUSTOMER', 'KODE_PRODUK', 'AMMOUNT', 'HARGA_JUAL',
            'TRX', 'TYPE_MITRA', 'AMMOUNT_FIX', 'PRODUK_FIX', 'BUCKET_NAME', 'Type_Produk',
            'TYPE_BISNIS', 'REV_INPPN', 'PAJAK', 'REV_EXPPN', 'HPP', 'TOTAL_HPP_INPPN',
            'TOTAL_HPP_EXPPN', 'Margin_INPPN', 'Margin_EXPPN', 'Hari', 'Bulan', 'KET_PROD',
        ];
    }

    public function map($record): array
    {
        return [
            $record->Tanggal, $record->ORG_CODE, $record->NAMA_CUSTOMER, $record->KODE_PRODUK,
            $record->AMMOUNT, $record->HARGA_JUAL, $record->TRX, $record->TYPE_MITRA,
            $record->AMMOUNT_FIX, $record->PRODUK_FIX, $record->BUCKET_NAME, $record->Type_Produk,
            $record->TYPE_BISNIS, $record->REV_INPPN, $record->PAJAK, $record->REV_EXPPN,
            $record->HPP, $record->TOTAL_HPP_INPPN, $record->TOTAL_HPP_EXPPN,
            $record->Margin_INPPN, $record->Margin_EXPPN, $record->Hari, $record->Bulan, $record->KET_PROD,
        ];
    }
}
