<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TransaksiPenjualan;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Support\Facades\Log;

class TransaksiPenjualanController extends Controller
{
    public function index()
    {
        $transaksis = TransaksiPenjualan::with('produk')->get();
        
        return view('transaksi.index', compact('transaksis'));
    }
    



    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'uang_masuk' => 'required|numeric|min:0',
            'tanggal_transaksi' => 'required|date',
        ]);
    
        // Cek jika keranjang tidak ada atau kosong
        if (!$request->has('keranjang') || empty($request->keranjang)) {
            return redirect()->back()->with('error', 'Keranjang kosong, tidak ada item yang bisa disimpan.');
        }
    
        // Simpan transaksi untuk setiap item di keranjang
        foreach ($request->keranjang as $item) {
            // Simpan transaksi
            TransaksiPenjualan::create([
                'uang_masuk' => $request->uang_masuk,
                'jumlah' => $item['jumlah'],
                'produk_id' => $item['produk_id'],
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'nama_obat' => $item['nama_obat'],
            ]);

            // Kurangi stok produk secara otomatis
            $produk = Produk::find($item['produk_id']);
            if ($produk) {
                $produk->stok_sisa -= $item['jumlah']; // Kurangi stok
                $produk->save(); // Simpan perubahan stok
            }
        }
    
        // Mengosongkan keranjang setelah transaksi berhasil
        Keranjang::truncate();
    
        // Redirect dengan pesan sukses
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan dan keranjang telah dikosongkan!');
    }
    
    
    

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        $transaksis = TransaksiPenjualan::query()
            ->when($keyword, function ($query, $keyword) {
                return $query->where('nama_obat', 'like', "%{$keyword}%");
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })
            ->get();
    
        return view('transaksi.index', compact('transaksis'));
    }
    
    public function edit($id_transaksi)
    {
        $transaksi = TransaksiPenjualan::findOrFail($id_transaksi);
    
        // Pastikan tanggal_transaksi adalah objek Carbon
        $transaksi->tanggal_transaksi = \Carbon\Carbon::parse($transaksi->tanggal_transaksi);
    
        return view('transaksi.edit', compact('transaksi'));
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'total_harga' => 'required|numeric',
            'uang_masuk' => 'required|numeric',
            'nama_obat' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
        ]);

        // Temukan transaksi berdasarkan ID
        $transaksi = TransaksiPenjualan::findOrFail($id);

        // Update transaksi dengan data baru
        $transaksi->update([
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'total_harga' => $request->total_harga,
            'uang_masuk' => $request->uang_masuk,
            'kembalian' => $request->uang_masuk - $request->total_harga,
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy($id_transaksi)
    {
        // Hapus transaksi berdasarkan ID
        $transaksi = TransaksiPenjualan::findOrFail($id_transaksi);
        $transaksi->delete();
    
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus!');
    }

    
    public function laporan(Request $request)
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

    // Mengolah laporan
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
    return view('transaksi.laporan', compact('laporan'));
}


}
