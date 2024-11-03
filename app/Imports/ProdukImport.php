<?php

namespace App\Imports;

use App\Models\Produk;
use App\Models\Kategori; // Pastikan model Kategori diimport
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ProdukImport implements ToModel
{
    public function model(array $row)
    {
        $tanggalFormat = 'd/m/Y'; // Format tanggal sesuai data Excel

        // Validasi dan parsing tanggal_kadaluarsa
        try {
            $tanggalKadaluarsa = isset($row[6]) ? Carbon::createFromFormat($tanggalFormat, $row[6]) : null;
        } catch (Exception $e) {
            Log::error('Error parsing tanggal_kadaluarsa: ' . $e->getMessage());
            return null; // Kembalikan null jika ada kesalahan
        }

        // Cari kategori berdasarkan nama (atau ID) jika kolom kategori_obat adalah nama
        $kategori = Kategori::where('nama_kategori', $row[2])->first(); // Ganti sesuai nama kolom kategori yang benar

        // Pastikan kategori ditemukan
        if (!$kategori) {
            Log::error('Kategori tidak ditemukan: ' . $row[2]);
            return null; // Jika kategori tidak ditemukan, kembalikan null
        }

        // Log data yang akan disimpan
        Log::info('Menyimpan data produk: ', $row);

        // Simpan data ke tabel produk
        return new Produk([
            'nama_obat' => $row[0],
            'kode_obat' => $row[1],
            'kategori_obat' => $kategori->id_kategori, // Ambil ID kategori
            'stok_awal' => $row[3],
            'stok_sisa' => $row[4],
            'harga_beli' => str_replace('.', '', $row[5]),
            'harga_jual' => str_replace('.', '', $row[6]),
            'satuan' => $row[7],
            'total' => str_replace('.', '', $row[8]),
            'tanggal_kadaluarsa' => $tanggalKadaluarsa,
            'keterangan' => $row[9] 
        ]);
    }
}
