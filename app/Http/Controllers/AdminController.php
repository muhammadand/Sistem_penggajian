<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\TransaksiPenjualan;
use App\Traits\NotifikasiTrait;

class AdminController extends Controller
{
    use NotifikasiTrait; // Menggunakan trait NotifikasiTrait

    public function index(Request $request)
{
    // Mengambil jumlah total produk
    $totalProduk = Produk::count();

    // Mengambil semua transaksi penjualan dan mengelompokkan berdasarkan tanggal
    $transaksis = TransaksiPenjualan::with('produk')->get();

    // Menghitung total pendapatan: harga_jual * jumlah untuk setiap transaksi
    $totalPendapatan = $transaksis->sum(function ($transaksi) {
        return $transaksi->produk->harga_jual * $transaksi->jumlah;
    });

    // Menghitung jumlah transaksi yang berhasil
    $jumlahTransaksi = $transaksis->count();

    // Menghitung rata-rata penjualan
    $rataRataPenjualan = $jumlahTransaksi > 0 ? $totalPendapatan / $jumlahTransaksi : 0;

    // Mengelompokkan transaksi berdasarkan tanggal dan menghitung total penjualan per tanggal
    $penjualanPerTanggal = $transaksis->groupBy(function ($transaksi) {
        return \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('Y-m-d'); // Format tanggal
    })->map(function ($group) {
        return $group->sum(function ($transaksi) {
            return $transaksi->produk->harga_jual * $transaksi->jumlah; // Total penjualan per tanggal
        });
    });

    // Menggunakan fungsi generateNotifications dari NotifikasiTrait
    $notifications = $this->generateNotifications($transaksis);

    // Mengirimkan data ke tampilan
    return view('admin.index', compact(
        'totalProduk', 'totalPendapatan', 'jumlahTransaksi', 'rataRataPenjualan', 'penjualanPerTanggal', 'notifications'
    ));
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
}
