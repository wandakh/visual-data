<?php

namespace App\Imports;

use App\Models\SalesRecord;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Baca kolom berdasarkan NAMA header (bukan urutan posisi), jadi urutan
 * kolom di file Excel bebas asal nama headernya cocok/mirip.
 *
 * PENTING: implement WithChunkReading + WithBatchInserts. File data asli
 * ternyata bisa berisi ratusan ribu baris — tanpa ini, PHP bakal mencoba
 * muat semua baris ke memori sekaligus DAN nge-insert satu-satu ke database
 * (211.000 query terpisah), yang hampir pasti timeout/kehabisan memori
 * sebelum selesai. Dengan chunking, file dibaca sedikit-sedikit (per 1000
 * baris), dan dengan batch insert, ke database juga masuk 1000 baris
 * sekaligus per query, jauh lebih cepat & ringan.
 */
class DatabaseImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /** Berapa baris yang berhasil di-mapping & disimpan. */
    public int $imported = 0;

    /** Berapa baris yang dilewati (kosong / gak ada header yang cocok sama sekali). */
    public int $dilewati = 0;

    /** Tanggal paling awal & paling akhir dari data yang diimport (buat log). */
    public ?string $tanggalTerawal = null;
    public ?string $tanggalTerakhir = null;

    private const ALIAS = [
        'Tanggal' => ['tanggal', 'tgl', 'date'],
        'ORG_CODE' => ['org_code', 'orgcode', 'kode_org'],
        'NAMA_CUSTOMER' => ['nama_customer', 'customer', 'nama_perusahaan', 'perusahaan'],
        'KODE_PRODUK' => ['kode_produk', 'kodeproduk', 'produk'],
        'AMMOUNT' => ['ammount', 'amount', 'jumlah', 'qty'],
        'HARGA_JUAL' => ['harga_jual', 'harga'],
        'TRX' => ['trx', 'transaksi'],
        'TYPE_MITRA' => ['type_mitra', 'tipe_mitra'],
        'AMMOUNT_FIX' => ['ammount_fix', 'amount_fix'],
        'PRODUK_FIX' => ['produk_fix'],
        'BUCKET_NAME' => ['bucket_name', 'bucket'],
        'Type_Produk' => ['type_produk', 'tipe_produk'],
        'TYPE_BISNIS' => ['type_bisnis', 'tipe_bisnis'],
        'REV_INPPN' => ['rev_inppn'],
        'PAJAK' => ['pajak', 'tax'],
        'REV_EXPPN' => ['rev_exppn'],
        'HPP' => ['hpp'],
        'TOTAL_HPP_INPPN' => ['total_hpp_inppn'],
        'TOTAL_HPP_EXPPN' => ['total_hpp_exppn'],
        'Margin_INPPN' => ['margin_inppn'],
        'Margin_EXPPN' => ['margin_exppn'],
        'Hari' => ['hari', 'day'],
        'Bulan' => ['bulan', 'month'],
        'KET_PROD' => ['ket_prod', 'keterangan_produk', 'keterangan'],
    ];

    public function model(array $row)
    {
        $tanggalMentah = $this->cari($row, self::ALIAS['Tanggal']);
        $namaCustomer = $this->cari($row, self::ALIAS['NAMA_CUSTOMER']);

        if (!$tanggalMentah && !$namaCustomer) {
            $this->dilewati++;

            return null;
        }

        $this->imported++;

        $tanggal = $this->parseTanggal($tanggalMentah);

        if ($tanggal) {
            if (!$this->tanggalTerawal || $tanggal < $this->tanggalTerawal) {
                $this->tanggalTerawal = $tanggal;
            }
            if (!$this->tanggalTerakhir || $tanggal > $this->tanggalTerakhir) {
                $this->tanggalTerakhir = $tanggal;
            }
        }

        $data = [
            'Tanggal' => $tanggal,
            'NAMA_CUSTOMER' => $namaCustomer,
        ];

        foreach (self::ALIAS as $kolom => $aliasList) {
            if ($kolom === 'Tanggal' || $kolom === 'NAMA_CUSTOMER') {
                continue;
            }

            $data[$kolom] = $this->cari($row, $aliasList) ?? '';
        }

        return new SalesRecord($data);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    private function parseTanggal(?string $mentah): ?string
    {
        if (!$mentah) {
            return null;
        }

        if (is_numeric($mentah)) {
            try {
                return Date::excelToDateTimeObject((float) $mentah)->format('Y-m-d');
            } catch (\Throwable) {
                return (string) $mentah;
            }
        }

        // Beberapa file Excel udah nyimpen tanggal sebagai teks (bukan serial
        // number) dengan format kayak "2023-01-01 00:00:00" atau "01/01/2023"
        // -> coba parse pakai Carbon, kalau gagal simpan apa adanya.
        try {
            return \Carbon\Carbon::parse($mentah)->format('Y-m-d');
        } catch (\Throwable) {
            return $mentah;
        }
    }

    private function cari(array $row, array $kemungkinanNama): ?string
    {
        foreach ($kemungkinanNama as $nama) {
            if (array_key_exists($nama, $row) && $row[$nama] !== null && $row[$nama] !== '') {
                return (string) $row[$nama];
            }
        }

        return null;
    }
}
