<?php

namespace App\Http\Controllers;
use App\Imports\ProdukImport;
use App\Exports\ProdukExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Produk;
use App\Models\Rop;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */

     // Metode untuk menghitung rata-rata penjualan per bulan

     public function index(Request $request)
     {
         $query = Produk::with(['rop', 'transaksi']);
     
         // Pencarian berdasarkan nama atau kode obat
         if ($request->has('search')) {
             $search = $request->input('search');
             $query->where(function($q) use ($search) {
                 $q->where('nama_obat', 'LIKE', "%{$search}%")
                   ->orWhere('kode_obat', 'LIKE', "%{$search}%");
             });
         }
     
         $produk = $query->paginate(10);
     
         foreach ($produk as $item) {
             // Memeriksa status ROP
             if ($item->rop && $item->stok_sisa <= $item->rop->rop) {
                 $item->status_rop = 'Harus pesan ulang';
             } else {
                 $item->status_rop = 'Stok aman';
             }
     
             // Hitung rata-rata penjualan dan permintaan
             $totalPenjualan = $item->transaksi()->sum('jumlah'); // Hitung total penjualan
             $jumlahTransaksi = $item->transaksi()->count(); // Hitung jumlah transaksi
     
             $item->rata_rata_penjualan = $jumlahTransaksi > 0 ? $totalPenjualan / $jumlahTransaksi : 0; // Rata-rata penjualan
     

         }
     
         return view('produk.index', compact('produk'));
     }
     


    
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kategori = Kategori::all(); // Get all categories for dropdown
        return view('produk.create', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required',
            'kode_obat' => 'required|unique:produk,kode_obat',
            'kategori_obat' => 'required|exists:kategori,id_kategori',
            'stok_awal' => 'required|integer',
            'stok_sisa' => 'required|integer',
            'harga_beli' => 'required|numeric', // Validasi untuk harga beli
            'harga_jual' => 'required|numeric',
            'satuan' => 'required|string', // Validasi untuk satuan
            'tanggal_kadaluarsa' => 'required|date',
        ]);

        // Menghitung total
        $request->merge(['total' => $request->input('harga_beli') * $request->input('stok_awal')]);

        Produk::create($request->all());

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function edit(Produk $produk)
    {
        $kategori = Kategori::all(); // Get all categories for dropdown
        return view('produk.edit', compact('produk', 'kategori'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_produk)
    {
        $request->validate([
            'nama_obat' => 'required',
            'kode_obat' => 'required',
            'kategori_obat' => 'required',
            'stok_awal' => 'required|integer',
            'stok_sisa' => 'required|integer',
            'harga_jual' => 'required|numeric',
            'tanggal_kadaluarsa' => 'required|date',
        ]);
    
        $produk = Produk::findOrFail($id_produk);
        $produk->update($request->all());
    
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diupdate');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function show($id)
    {
        // Ambil produk berdasarkan ID
        $produk = Produk::findOrFail($id);

        // Tampilkan view dengan data produk
        return view('produk.show', compact('produk'));
    }



    // Metode untuk mengekspor data
    public function export()
    {
        return Excel::download(new ProdukExport, 'produk.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
    
        try {
            // Impor data produk
            Excel::import(new ProdukImport, $request->file('file'));
    
            return redirect()->route('produk.index')->with('success', 'Produk berhasil diimpor.');
        } catch (\Exception $e) {
            // Log kesalahan
            Log::error('Import produk gagal: ' . $e->getMessage());
    
            // Kembali ke halaman dengan pesan kesalahan
            return redirect()->back()->with('error', 'Import produk gagal. Silakan periksa format file dan coba lagi.');
        }
    }



    public function hitungRataRata($id_produk)
    {
        $produk = Produk::with('transaksi')->findOrFail($id_produk); // Ambil produk berdasarkan ID
    
        // Hitung rata-rata penjualan
        $totalPenjualan = $produk->transaksi->sum('jumlah');
        $jumlahTransaksi = $produk->transaksi->count();
        $rataRataPenjualan = $jumlahTransaksi > 0 ? $totalPenjualan / $jumlahTransaksi : 0;
    
        return view('produk.hitung-rata-rata', compact('produk', 'rataRataPenjualan'));
    }

    
}
