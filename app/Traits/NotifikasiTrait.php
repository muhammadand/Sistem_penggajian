<?php

namespace App\Traits;

use App\Models\Produk;
use App\Models\TransaksiPenjualan;

trait NotifikasiTrait
{
    public function generateNotifications($transaksi, $lead_time = 2)
    {
        $notifications = [];
        $notifiedItems = []; // Array untuk melacak nama_obat yang sudah di-notifikasi

        foreach ($transaksi as $item) {
            // Hitung rata-rata harian
            $item->rata_rata_harian = $item->total_jumlah / 365;

            // Mengambil semua data penjualan untuk nama obat tertentu
            $penjualan = TransaksiPenjualan::where('nama_obat', $item->nama_obat)->get();

            $total = $penjualan->sum('jumlah');
            $count = $penjualan->count();
            $mean = $count > 0 ? $total / $count : 0;

            // Hitung deviasi standar
            if ($count > 1) {
                $variance = $penjualan->reduce(function ($carry, $item) use ($mean) {
                    return $carry + pow($item->jumlah - $mean, 2);
                }, 0) / ($count - 1);
                $sigma = sqrt($variance);
                $item->sigma_d = $sigma / sqrt(30);
            } else {
                $item->sigma_d = 0;
            }

            // Tentukan nilai Z (1.64 untuk 95% tingkat layanan)
            $Z = 1.64;

            // Hitung safety stock dan ROP
            $item->safety_stock = $Z * $item->sigma_d * $lead_time;
            $item->rop = ($item->rata_rata_harian * $lead_time) + $item->safety_stock;

            // Ambil stok sisa
            $stok_sisa = Produk::where('nama_obat', $item->nama_obat)->value('stok_sisa');

            // Cek apakah stok_sisa kurang dari atau sama dengan ROP dan belum ada notifikasi untuk nama_obat ini
            if ($stok_sisa <= $item->rop && !in_array($item->nama_obat, $notifiedItems)) {
                $notifications[] = "Stok untuk {$item->nama_obat} tinggal {$stok_sisa}. ROP adalah {$item->rop}.";
                $notifiedItems[] = $item->nama_obat; // Tandai nama_obat ini sebagai sudah di-notifikasi
            }
        }

        return $notifications;
    }
}
