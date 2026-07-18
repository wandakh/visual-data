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
     * Karyawan cuma boleh lihat data ORG_CODE-nya sendiri. Admin (scopedOrgCode
     * balikin null) gak kefilter sama sekali — akses global.
     */
    public function scopeForUser($query, User $user)
    {
        $orgCode = $user->scopedOrgCode();

        return $orgCode ? $query->where('ORG_CODE', $orgCode) : $query;
    }

    /**
     * Filter dipakai bareng-bareng oleh Dashboard (tampilan) dan Export
     * Excel, supaya export SELALU ikut apa yang lagi difilter di layar
     * (sebelumnya export punya filter sendiri yang gak nyambung).
     */
    public static function applyFilters($query, array $filters, bool $useCreatedAtDefault = false)
    {
        $adaFilterTanggalEksplisit = !empty($filters['start_date']) || !empty($filters['end_date']);

        if ($useCreatedAtDefault && empty($filters['show_all']) && !$adaFilterTanggalEksplisit) {
            $query->whereDate('created_at', today());
        } else {
            if (!empty($filters['start_date'])) {
                $query->whereDate('Tanggal', '>=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $query->whereDate('Tanggal', '<=', $filters['end_date']);
            }
        }

        if (!empty($filters['customer_name'])) {
            $query->where('NAMA_CUSTOMER', 'LIKE', '%' . $filters['customer_name'] . '%');
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('NAMA_CUSTOMER', 'LIKE', '%' . $filters['search'] . '%')
                    ->orWhere('KODE_PRODUK', 'LIKE', '%' . $filters['search'] . '%');
            });
        }

        return $query;
    }

    /**
     * Kolom yang dropdown pilihannya diambil dari nilai unik yang sudah ada.
     * Kalau $orgCode diisi (Karyawan), pilihannya dipersempit cuma dari data
     * cabang itu aja.
     */
    public static function dropdownOptions(?string $orgCode = null): array
    {
        $columns = [
            'ORG_CODE', 'NAMA_CUSTOMER', 'KODE_PRODUK', 'AMMOUNT', 'HARGA_JUAL', 'TRX',
            'TYPE_MITRA', 'AMMOUNT_FIX', 'PRODUK_FIX', 'BUCKET_NAME', 'Type_Produk',
            'TYPE_BISNIS', 'REV_INPPN', 'PAJAK', 'REV_EXPPN', 'HPP', 'TOTAL_HPP_INPPN',
            'TOTAL_HPP_EXPPN', 'Margin_INPPN', 'Margin_EXPPN', 'Hari', 'Bulan', 'KET_PROD',
        ];

        $options = [];
        foreach ($columns as $column) {
            $options[$column] = static::query()
                ->when($orgCode, fn ($q) => $q->where('ORG_CODE', $orgCode))
                ->select($column)->distinct()->pluck($column, $column);
        }

        return $options;
    }

    /**
     * Sama seperti dropdownOptions(), tapi dipersempit cuma buat customer
     * tertentu — soalnya tiap perusahaan biasanya punya kode/pola isian
     * sendiri (ORG_CODE, KODE_PRODUK, dst beda-beda per customer).
     */
    public static function dropdownOptionsForCustomer(string $customerName, ?string $orgCode = null): array
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
                ->when($orgCode, fn ($q) => $q->where('ORG_CODE', $orgCode))
                ->select($column)->distinct()->pluck($column)->values();
        }

        return $options;
    }
}
