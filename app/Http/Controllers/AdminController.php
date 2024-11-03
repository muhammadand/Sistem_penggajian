<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\TransaksiPenjualan;

class AdminController extends Controller
{
    public function index(Request $request)
{


    // Mengambil jumlah total produk
    $totalProduk = Produk::count();

    // Inisialisasi query untuk mendapatkan data transaksi penjualan yang berhasil
    $transaksis = TransaksiPenjualan::with('produk'); // Asumsi status 'berhasil' menunjukkan transaksi berhasil


    // Menghitung total pendapatan: harga_jual * jumlah untuk setiap transaksi
    $totalPendapatan = $transaksis->get()->sum(function ($transaksi) {
        return $transaksi->produk->harga_jual * $transaksi->jumlah;
    });

    // Menghitung jumlah transaksi yang berhasil
    $jumlahTransaksi = $transaksis->count();

    // Menghitung rata-rata penjualan
    $rataRataPenjualan = $jumlahTransaksi > 0 ? $totalPendapatan / $jumlahTransaksi : 0;

    // Mengirimkan data ke tampilan
    return view('admin.index', compact( 'totalProduk', 'totalPendapatan', 'jumlahTransaksi', 'rataRataPenjualan', 'transaksis'));
}







    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function orders()
    {
        $username = Auth::user()->name;
        return view('admin.orders', compact('username'));
    }

    public function rop(Request $request)
    {
        // Inisialisasi query untuk mendapatkan data transaksi
        $transaksis = TransaksiPenjualan::with('produk');
    
        // Pencarian berdasarkan kata kunci
        if ($request->has('keyword') && $request->keyword != '') {
            $transaksis = $transaksis->whereHas('produk', function ($query) use ($request) {
                $query->where('nama_obat', 'like', '%' . $request->keyword . '%');
            });
        }
    
        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $transaksis = $transaksis->whereBetween('tanggal_transaksi', [$request->start_date, $request->end_date]);
        }
    
        // Ambil data transaksi
        $transaksis = $transaksis->get();
    
       
        $laporan = [];
        foreach ($transaksis as $transaksi) {
            $monthYear = Carbon::parse($transaksi->tanggal_transaksi)->format('F Y'); // Mengambil bulan dan tahun
    
            // Jika bulan dan tahun belum ada di laporan, inisialisasi data
            if (!isset($laporan[$monthYear][$transaksi->produk->nama_obat])) {
                $stokSisa = $transaksi->produk->stok_sisa; // Ambil stok sisa dari produk
                $laporan[$monthYear][$transaksi->produk->nama_obat] = [
                    'jumlah_terjual' => 0,
                    'harga_satuan' => $transaksi->produk->harga_jual,
                    'total_penjualan' => 0,
                    'jumlah_transaksi' => 0,
                    'total_permintaan' => 0,
                    'jumlah_permintaan' => 0,
                    'stok_sisa' => $stokSisa, // Tambahkan stok sisa
                ];
            }
    
            // Update jumlah terjual dan total penjualan
            $laporan[$monthYear][$transaksi->produk->nama_obat]['jumlah_terjual'] += $transaksi->jumlah;
            $laporan[$monthYear][$transaksi->produk->nama_obat]['total_penjualan'] += ($transaksi->jumlah * $transaksi->produk->harga_jual);
            $laporan[$monthYear][$transaksi->produk->nama_obat]['jumlah_transaksi']++; // Hitung jumlah transaksi
    
            // Menghitung total permintaan
            $laporan[$monthYear][$transaksi->produk->nama_obat]['total_permintaan'] += $transaksi->jumlah; // Sesuaikan jika perlu
            $laporan[$monthYear][$transaksi->produk->nama_obat]['jumlah_permintaan']++; // Hitung jumlah permintaan
        }
    
        // Menghitung rata-rata penjualan dan rata-rata permintaan per produk
        foreach ($laporan as $monthYear => $produkData) {
            foreach ($produkData as $namaObat => $data) {
                // Menghitung rata-rata penjualan
                $data['rata_rata_penjualan'] = $data['total_penjualan'] / ($data['jumlah_transaksi'] ?: 1);
    
                // Menghitung rata-rata permintaan
                $data['rata_rata_permintaan'] = $data['total_permintaan'] / ($data['jumlah_permintaan'] ?: 1);
    
                $laporan[$monthYear][$namaObat] = $data; // Menyimpan data yang sudah dihitung
            }
        }
        
    
        // Mengirim data ke view
        return view('admin.ROP', compact('laporan'));
    }
    
}
