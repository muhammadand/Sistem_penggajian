<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TransaksiPenjualan;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Support\Facades\Log;
use App\Traits\NotifikasiTrait;
class TransaksiPenjualanController extends Controller
{
    use NotifikasiTrait; // Menyertakan Trait Notifikasi
    public function index()
    {
        $produk = Produk::with('rop')->get(); 
        $transaksis = TransaksiPenjualan::with('produk')->get();
        $notifications = $this->generateNotifications($produk);
        return view('transaksi.index', compact('transaksis', 'notifications'));
    }
    public function edit($id_transaksi)
{
    // Ambil transaksi berdasarkan ID
    $transaksi = TransaksiPenjualan::findOrFail($id_transaksi);
    
    // Mengambil produk terkait
    $produk = Produk::with('rop')->get(); 
    $notifications = $this->generateNotifications($produk);

    // Parsing tanggal
    $transaksi->tanggal_transaksi = \Carbon\Carbon::parse($transaksi->tanggal_transaksi);

    // Hitung total harga
    $total_harga = $transaksi->jumlah * $transaksi->produk->harga_jual;

    // Kirim data ke view
    return view('transaksi.edit', compact('transaksi', 'notifications', 'total_harga'));
}

    
    public function search(Request $request)
    {
        $produk = Produk::with('rop')->get(); 
        $notifications = $this->generateNotifications($produk);
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
    
        return view('transaksi.index', compact('transaksis', 'notifications'));
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
        $produk = Produk::with('rop')->get();
        $notifications = $this->generateNotifications($produk);
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
                $laporan[$monthYear][$transaksi->produk->nama_obat] = [
                    'jumlah_terjual' => 0,
                    'harga_satuan' => $transaksi->produk->harga_jual,
                    'total_penjualan' => 0,
                    'jumlah_transaksi' => 0,
                ];
            }
    
            // Update jumlah terjual dan total penjualan
            $laporan[$monthYear][$transaksi->produk->nama_obat]['jumlah_terjual'] += $transaksi->jumlah;
            $laporan[$monthYear][$transaksi->produk->nama_obat]['total_penjualan'] += ($transaksi->jumlah * $transaksi->produk->harga_jual);
            $laporan[$monthYear][$transaksi->produk->nama_obat]['jumlah_transaksi']++; // Hitung jumlah transaksi
        }
    
        // Mengirim data ke view
        return view('transaksi.laporan', compact('laporan', 'notifications'));
    }
    



}
