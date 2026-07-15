<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Sebelumnya: App\Models\Database (nama model yang membingungkan, mirip nama
 * konsep bawaan PHP/Laravel). Sekarang jadi SalesRecord, tabel tetap `databases`
 * supaya kompatibel dengan data lama.
 *
 * Sekarang pakai SoftDeletes: hapus data gak lagi permanen, cuma ditandain
 * deleted_at, bisa dipulihin lewat halaman "Data Terhapus".
 */
class SalesRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'databases';

    public const ALASAN_HAPUS = [
        'salah_ketik' => 'Salah ketik/input',
        'data_duplikat' => 'Data duplikat',
        'data_tidak_valid' => 'Data tidak valid',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'Tanggal', 'ORG_CODE', 'NAMA_CUSTOMER', 'KODE_PRODUK', 'AMMOUNT', 'HARGA_JUAL',
        'TRX', 'TYPE_MITRA', 'AMMOUNT_FIX', 'PRODUK_FIX', 'BUCKET_NAME', 'Type_Produk',
        'TYPE_BISNIS', 'REV_INPPN', 'PAJAK', 'REV_EXPPN', 'HPP', 'TOTAL_HPP_INPPN',
        'TOTAL_HPP_EXPPN', 'Margin_INPPN', 'Margin_EXPPN', 'Hari', 'Bulan', 'KET_PROD',
    ];

    /**
     * Kolom yang dropdown pilihannya diambil dari nilai unik yang sudah ada.
     */
    public static function dropdownOptions(): array
    {
        $columns = [
            'ORG_CODE', 'NAMA_CUSTOMER', 'KODE_PRODUK', 'AMMOUNT', 'HARGA_JUAL', 'TRX',
            'TYPE_MITRA', 'AMMOUNT_FIX', 'PRODUK_FIX', 'BUCKET_NAME', 'Type_Produk',
            'TYPE_BISNIS', 'REV_INPPN', 'PAJAK', 'REV_EXPPN', 'HPP', 'TOTAL_HPP_INPPN',
            'TOTAL_HPP_EXPPN', 'Margin_INPPN', 'Margin_EXPPN', 'Hari', 'Bulan', 'KET_PROD',
        ];

        $options = [];
        foreach ($columns as $column) {
            $options[$column] = static::query()->select($column)->distinct()->pluck($column, $column);
        }

        return $options;
    }

    /**
     * Sama seperti dropdownOptions(), tapi dipersempit cuma buat customer
     * tertentu — soalnya tiap perusahaan biasanya punya kode/pola isian
     * sendiri (ORG_CODE, KODE_PRODUK, dst beda-beda per customer).
     */
    public static function dropdownOptionsForCustomer(string $customerName): array
    {
        $columns = [
            'ORG_CODE', 'KODE_PRODUK', 'AMMOUNT', 'HARGA_JUAL', 'TRX',
            'TYPE_MITRA', 'AMMOUNT_FIX', 'PRODUK_FIX', 'BUCKET_NAME', 'Type_Produk',
            'TYPE_BISNIS', 'REV_INPPN', 'PAJAK', 'REV_EXPPN', 'HPP', 'TOTAL_HPP_INPPN',
            'TOTAL_HPP_EXPPN', 'Margin_INPPN', 'Margin_EXPPN', 'Hari', 'Bulan', 'KET_PROD',
        ];

        $options = [];
        foreach ($columns as $column) {
            $options[$column] = static::query()
                ->where('NAMA_CUSTOMER', $customerName)
                ->select($column)->distinct()->pluck($column)->values();
        }

        return $options;
    }
}
