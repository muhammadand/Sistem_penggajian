<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\TransaksiPenjualan;
use App\Models\SafetyStock;
use App\Models\Produk;
use Illuminate\Http\Request;

class SafetyStockController extends Controller
{
    public function index()
    {
        // Ambil semua data safety stock beserta produk
        $safetyStocks = SafetyStock::with('produk')->get();
        return view('safety.index', compact('safetyStocks'));
    }
        public function create(Request $request)
        {
            // Ambil semua produk untuk ditampilkan dalam dropdown
            $produks = Produk::all();
    
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
                    $laporan[$monthYear][$transaksi->produk->nama_obat] = [
                        'jumlah_terjual' => 0,
                        'harga_satuan' => $transaksi->produk->harga_jual,
                        'total_penjualan' => 0,
                        'jumlah_transaksi' => 0, // Tambahkan untuk menghitung rata-rata penjualan
                        'total_permintaan' => 0, // Tambahkan untuk menghitung total permintaan
                        'jumlah_permintaan' => 0, // Tambahkan untuk menghitung jumlah permintaan
                    ];
                }
    
                // Update jumlah terjual dan total penjualan
                $laporan[$monthYear][$transaksi->produk->nama_obat]['jumlah_terjual'] += $transaksi->jumlah;
                $laporan[$monthYear][$transaksi->produk->nama_obat]['total_penjualan'] += ($transaksi->jumlah * $transaksi->produk->harga_jual);
                $laporan[$monthYear][$transaksi->produk->nama_obat]['jumlah_transaksi']++; // Hitung jumlah transaksi
    
                // Menghitung total permintaan
                $laporan[$monthYear][$transaksi->produk->nama_obat]['total_permintaan'] += $transaksi->jumlah; 
                $laporan[$monthYear][$transaksi->produk->nama_obat]['jumlah_permintaan']++; 
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
            return view('safety.create', compact('produks', 'laporan'));
        }
    

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'permintaan_harian' => 'required|integer',
            'waktu_pengiriman' => 'required|integer',
        ]);

        // Menghitung Safety Stock dengan tingkat layanan 95% (Z = 1.65)
        $safetyStockValue = $this->calculateSafetyStock(
            $request->permintaan_harian,
            $request->waktu_pengiriman
        );

        // Buat entry baru untuk Safety Stock
        $safetyStock = SafetyStock::create([
            'id_produk' => $request->id_produk,
            'permintaan_harian' => $request->permintaan_harian,
            'waktu_pengiriman' => $request->waktu_pengiriman,
            'safety_stock' => $safetyStockValue,
        ]);

        // Update stok sisa di produk
        $produk = Produk::findOrFail($request->id_produk);
        $produk->stok_sisa += $safetyStockValue; // Tambahkan safety stock ke stok sisa produk
        $produk->save();

        return redirect()->route('safety.index')->with('success', 'Safety stock berhasil ditambahkan.');
    }

    /**
     * Menghitung Safety Stock berdasarkan permintaan harian dan lead time.
     * Tingkat layanan diatur ke 95% (Z = 1.65)
     */
    private function calculateSafetyStock($permintaanHarian, $leadTime)
    {
        // Nilai Z untuk tingkat layanan 95% (1.65)
        $zScore = 1.65;

        // Menghitung Standar Deviasi selama Lead Time (σₗₜ)
        $standarDeviasiLeadTime = $permintaanHarian * sqrt($leadTime);

        // Menghitung Safety Stock
        $safetyStock = $zScore * $standarDeviasiLeadTime;

        return round($safetyStock); // Membulatkan hasil ke angka terdekat
    }

    public function show($id)
    {
        $safetyStock = SafetyStock::with('produk')->findOrFail($id);
        return view('safety.show', compact('safetyStock'));
    }

    public function edit($id)
    {
        $safetyStock = SafetyStock::findOrFail($id);
        $produks = Produk::all(); // Ambil semua produk untuk ditampilkan dalam dropdown
        return view('safety.edit', compact('safetyStock', 'produks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'permintaan_harian' => 'required|integer',
            'waktu_pengiriman' => 'required|integer',
        ]);

        $safetyStock = SafetyStock::findOrFail($id);
        $produk = Produk::findOrFail($safetyStock->id_produk);

        // Kurangi stok sisa sebelum diperbarui
        $produk->stok_sisa -= $safetyStock->safety_stock;

        // Update nilai safety stock baru
        $safetyStock->permintaan_harian = $request->permintaan_harian;
        $safetyStock->waktu_pengiriman = $request->waktu_pengiriman;

        // Hitung ulang safety stock
        $safetyStock->safety_stock = $this->calculateSafetyStock(
            $safetyStock->permintaan_harian,
            $safetyStock->waktu_pengiriman
        );

        // Simpan safety stock baru
        $safetyStock->save();

        // Update stok sisa dengan nilai safety stock baru
        $produk->stok_sisa += $safetyStock->safety_stock;
        $produk->save();

        return redirect()->route('safety.index')->with('success', 'Safety stock berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $safetyStock = SafetyStock::findOrFail($id);
        $produk = Produk::findOrFail($safetyStock->id_produk);

        // Kurangi stok sisa dengan safety stock yang akan dihapus
        $produk->stok_sisa -= $safetyStock->safety_stock;
        $produk->save();

        $safetyStock->delete();

        return redirect()->route('safety.index')->with('success', 'Safety stock berhasil dihapus.');
    }
}
