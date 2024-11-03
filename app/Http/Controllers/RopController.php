<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rop; // Pastikan Anda mengimpor model ROP
use App\Models\Produk; // Mengimpor model Produk
use App\Models\TransaksiPenjualan;

class RopController extends Controller
{
    public function create()
    {
        // Mengambil semua produk untuk ditampilkan di form
        $produk = Produk::all(); // Ambil semua produk

        return view('rop.calculate', compact('produk'));
    }

    // Menampilkan form perhitungan ROP
    public function showCalculateForm()
    {
        $produk = Produk::all(); // Mengambil semua data produk
        return view('rop.calculate', compact('produk')); // Mengirimkan data produk ke view
    }

    // Menghitung ROP dan menyimpan hasilnya
    public function calculate(Request $request)
    {
        // Validasi input
        $request->validate([
            'daily_usage' => 'required|numeric',
            'lead_time' => 'required|numeric',
            'safety_stock' => 'required|numeric',
            'product_id' => 'required|exists:produk,id_produk',
        ]);
    
        $productId = $request->input('product_id'); // Ambil product_id dari request
    
        // Cek apakah ROP sudah ada untuk produk ini
        $existingRop = Rop::where('product_id', $productId)->first();
    
        if ($existingRop) {
            return redirect()->route('rop.edit', $existingRop->id)->with('info', 'ROP sudah ada untuk produk ini. Silakan edit ROP yang ada.');
        }
    
        $dailyUsage = $request->input('daily_usage');
        $leadTime = $request->input('lead_time');
        $safetyStock = $request->input('safety_stock');
    
        // Menghitung ROP
        $rop = ($dailyUsage * $leadTime) + $safetyStock;
    
        // Menyimpan ROP ke dalam database
        Rop::create([
            'product_id' => $productId,
            'lead_time' => $leadTime,
            'daily_usage' => $dailyUsage,
            'safety_stock' => $safetyStock,
            'rop' => $rop,
        ]);
    
        return redirect()->route('rop.index')->with('success', 'ROP berhasil dihitung dan disimpan.');
    }
    

    // Menampilkan halaman index untuk ROP
    public function index()
    {
        $rops = Rop::with('produk')->get(); // Mengambil semua data ROP dari database beserta relasinya
        return view('rop.index', compact('rops'));
    }

    // Menampilkan form edit ROP
    public function edit($id)
    {
        $rop = Rop::findOrFail($id); // Ambil data ROP berdasarkan ID
        $produk = Produk::all(); // Ambil semua produk
    
        return view('rop.calculate', compact('rop', 'produk')); // Kirimkan kedua variabel
    }

    // Mengupdate data ROP
    public function update(Request $request, $id)
    {
        $rop = Rop::findOrFail($id); // Mengambil ROP yang ingin diupdate

        // Validasi input
        $request->validate([
            'daily_usage' => 'required|numeric',
            'lead_time' => 'required|numeric',
            'safety_stock' => 'required|numeric',
            'product_id' => 'required|exists:produk,id_produk', // Pastikan product_id valid
        ]);

        // Menghitung ROP baru
        $dailyUsage = $request->input('daily_usage');
        $leadTime = $request->input('lead_time');
        $safetyStock = $request->input('safety_stock');
        $productId = $request->input('product_id');

        $ropValue = ($dailyUsage * $leadTime) + $safetyStock;

        // Mengupdate data ROP
        $rop->update([
            'product_id' => $productId,
            'lead_time' => $leadTime,
            'daily_usage' => $dailyUsage,
            'safety_stock' => $safetyStock,
            'rop' => $ropValue,
        ]);

        return redirect()->route('rop.index')->with('success', 'ROP berhasil diupdate.');
    }

    // Menghapus ROP
    public function destroy($id)
    {
        $rop = Rop::findOrFail($id); // Mengambil ROP yang ingin dihapus
        $rop->delete(); // Menghapus ROP
        return redirect()->route('rop.index')->with('success', 'ROP berhasil dihapus.');
    }
    public function updateRop(Request $request)
    {
        $ropData = $request->input(); // Ambil data ROP yang dikirim

        // Proses data ROP sesuai kebutuhan, misalnya simpan ke database
        foreach ($ropData as $data) {
            // Contoh: Cari produk berdasarkan nama dan update ROP
            $produk = Produk::where('nama_obat', $data['namaObat'])->first();
            if ($produk) {
                $produk->rop = $data['rop']; // Update ROP
                $produk->save(); // Simpan perubahan
            }
        }

        return response()->json(['success' => true]);
    }


    public function data(Request $request)
    {
        // Mengambil parameter pencarian dan lead time jika ada
        $keyword = $request->input('keyword');
        $lead_time = (int)$request->input('lead_time', 1); // Default lead time 1 hari jika tidak ada input
    
        // Mengambil data transaksi dengan filter jika ada keyword
        $transaksi = TransaksiPenjualan::when($keyword, function ($query, $keyword) {
            return $query->where('nama_obat', 'like', '%' . $keyword . '%')
                         ->orWhere('tanggal_transaksi', 'like', '%' . $keyword . '%');
        })
        ->selectRaw('nama_obat, YEAR(tanggal_transaksi) as tahun, SUM(jumlah) as total_jumlah')
        ->groupBy('nama_obat', 'tahun') // Grup berdasarkan 'nama_obat' dan 'tahun'
        ->orderBy('tahun', 'asc') // Urutkan berdasarkan tahun
        ->paginate(10); // Pagination 10 data per halaman
    
        // Menghitung rata-rata penjualan bulanan, safety stock, permintaan harian, dan ROP
        foreach ($transaksi as $item) {
            // Menghitung rata-rata bulanan berdasarkan total jumlah dan jumlah bulan
            $item->rata_rata_bulanan = $item->total_jumlah / 12; // Atau sesuaikan sesuai logika bisnis Anda
    
            // Mengambil semua data penjualan untuk nama obat tertentu
            $penjualan = TransaksiPenjualan::where('nama_obat', $item->nama_obat)->get();
    
            // Menghitung total dan rata-rata
            $total = $penjualan->sum('jumlah');
            $count = $penjualan->count();
            $mean = $count > 0 ? $total / $count : 0;
    
            // Hitung deviasi standar
            if ($count > 1) { // Pastikan ada cukup data untuk menghitung deviasi standar
                $variance = $penjualan->reduce(function ($carry, $item) use ($mean) {
                    return $carry + pow($item->jumlah - $mean, 2);
                }, 0) / ($count - 1); // Menggunakan n-1 untuk sampel
                $sigma = sqrt($variance);
                // Hitung deviasi standar harian
                $item->sigma_d = $sigma / sqrt(30); // Sesuaikan jika periode bulan berbeda
            } else {
                $item->sigma_d = 0; // Jika tidak ada data, kembalikan 0
            }
    
            // Tentukan nilai Z (misalnya, 1.64 untuk tingkat layanan 95%)
            $Z = 1.64;
    
            // Hitung safety stock
            $item->safety_stock = $Z * $item->sigma_d * $lead_time;
    
            // Hitung permintaan harian
            $item->permintaan_harian = $item->rata_rata_bulanan / 30; // Rata-rata per hari
    
            // Hitung ROP
            $item->rop = ($item->permintaan_harian * $lead_time) + $item->safety_stock;
        }
    
        // Mengembalikan tampilan dengan data transaksi
        return view('rop.data', compact('transaksi'));
    }
    
   
 
    

}
