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
use App\Traits\NotifikasiTrait;
class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */

     // Metode untuk menghitung rata-rata penjualan per bulan

     use NotifikasiTrait; // Menyertakan Trait Notifikasi

     /**
      * Display a listing of the resource.
      *
      * @param  Request  $request
      * @return \Illuminate\Http\Response
      */
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
 
         // Menghitung notifikasi ROP
         $notifications = $this->generateNotifications($produk); // Menggunakan NotifikasiTrait
 
        
 
         // Menampilkan notifikasi di halaman produk.index
         return view('produk.index', compact('produk', 'notifications'));
     }
     


    
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
{
    $produk = Produk::with('rop')->get(); // Ambil semua produk dengan ROP
    $notifications = $this->generateNotifications($produk); // Ambil notifikasi berdasarkan produk
    $kategori = Kategori::all(); // Ambil semua kategori untuk dropdown

    return view('produk.create', compact('kategori', 'notifications')); // Kirim notifikasi ke view
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
        // Mengambil semua kategori untuk dropdown
        $kategori = Kategori::all(); 
    
        // Mengambil notifikasi berdasarkan produk
        $notifications = $this->generateNotifications([$produk]); // Masukkan produk ke dalam array
    
        // Menampilkan view dengan data produk, kategori, dan notifikasi
        return view('produk.edit', compact('produk', 'kategori', 'notifications'));
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
    // Ambil produk berdasarkan ID dengan eager loading untuk relasi 'rop'
    $produk = Produk::with('rop')->findOrFail($id);
    
    // Ambil semua produk untuk notifikasi
    $produks = Produk::all(); // Mengambil semua produk tanpa ROP jika tidak diperlukan
    $notifications = $this->generateNotifications($produks); // Ambil notifikasi berdasarkan produk

    // Tampilkan view dengan data produk
    return view('produk.show', compact('produk', 'notifications')); // Perbaiki penamaan variabel di sini
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
