<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rop; // Pastikan Anda mengimpor model ROP
use App\Models\Produk; // Mengimpor model Produk
use App\Models\TransaksiPenjualan;
use App\Traits\NotifikasiTrait;
use Carbon\Carbon;
class RopController extends Controller
{
    use NotifikasiTrait; // Menyertakan Trait Notifikasi
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
    

    public function index(Request $request)
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
        return view('rop.index', compact('laporan','notifications'));
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
    $lead_time = (int)$request->input('lead_time', 2); // Default lead time 2 hari jika tidak ada input

    // Mengambil data transaksi dengan filter jika ada keyword
    $transaksi = TransaksiPenjualan::when($keyword, function ($query, $keyword) {
        return $query->where('nama_obat', 'like', '%' . $keyword . '%')
                     ->orWhere('tanggal_transaksi', 'like', '%' . $keyword . '%');
    })
    ->selectRaw('nama_obat, YEAR(tanggal_transaksi) as tahun, SUM(jumlah) as total_jumlah')
    ->groupBy('nama_obat', 'tahun') // Grup berdasarkan 'nama_obat' dan 'tahun'
    ->orderBy('tahun', 'asc') // Urutkan berdasarkan tahun
    ->paginate(10); // Pagination 10 data per halaman

    // Notifikasi untuk admin
    $notifications = [];
    $notifiedItems = []; // Array untuk melacak nama_obat yang sudah di-notifikasi

    // Menghitung rata-rata penjualan bulanan, safety stock, permintaan harian, dan ROP
    foreach ($transaksi as $item) {
        // Menghitung rata-rata bulanan berdasarkan total jumlah dan jumlah bulan
        $item->rata_rata_harian = $item->total_jumlah / 365; // Sesuaikan sesuai logika bisnis Anda

        // Mengambil semua data penjualan untuk nama obat tertentu
        $penjualan = TransaksiPenjualan::where('nama_obat', $item->nama_obat)->get();

        // Menghitung total dan rata-rata
        $total = $penjualan->sum('jumlah');
        $count = $penjualan->count();
        $mean = $count > 0 ? $total / $count : 0;

        // Hitung deviasi standar
        if ($count > 1) {
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

        // Hitung ROP
        $item->rop = ($item->rata_rata_harian * $lead_time) + $item->safety_stock;

        // Ambil data stok_sisa untuk item ini
        $stok_sisa = Produk::where('nama_obat', $item->nama_obat)->value('stok_sisa'); // Sesuaikan dengan nama tabel dan kolom yang benar

        // Cek apakah stok_sisa kurang dari atau sama dengan ROP
        if ($stok_sisa <= $item->rop) {
            // Pastikan notifikasi untuk nama_obat ini belum ditambahkan
            if (!in_array($item->nama_obat, $notifiedItems)) {
                $notifications[] = "Stok untuk {$item->nama_obat} tinggal {$stok_sisa}. ROP adalah {$item->rop}.";
                $notifiedItems[] = $item->nama_obat; // Tandai nama_obat ini sebagai sudah di-notifikasi
            }
        }
    }

    // Mengembalikan tampilan dengan data transaksi dan notifikasi
    return view('rop.data', compact('transaksi', 'notifications'));
}

   
 
    

}
