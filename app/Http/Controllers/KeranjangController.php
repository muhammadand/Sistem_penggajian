<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Produk;
use App\Traits\NotifikasiTrait;

class KeranjangController extends Controller
{
    use NotifikasiTrait; 
    // Menampilkan keranjang belanja
    public function index()
    { $produk = Produk::with('rop')->get();
        $notifications = $this->generateNotifications($produk);
        $keranjang = Keranjang::with('produk')->get(); // Ambil semua data keranjang
        $total_harga = $keranjang->sum(function ($item) {
            return $item->produk->harga_jual * $item->jumlah;
        });

        return view('keranjang.index', compact('keranjang', 'total_harga', 'notifications'));
    }
    public function cari(Request $request)
    {
        $produks = Produk::with('rop')->get();
        $notifications = $this->generateNotifications($produks);
        $keyword = $request->input('keyword');
        $produk = Produk::where('nama_obat', 'LIKE', "%$keyword%")->get();
        
        // Ambil data keranjang
        $keranjang = Keranjang::with('produk')->get();
        $total_harga = $keranjang->sum(function ($item) {
            return $item->jumlah * $item->produk->harga_jual;
        });

        return view('keranjang.index', compact('keranjang', 'total_harga', 'produk', 'keyword', 'notifications'));
    }

    // Menambahkan produk ke keranjang
    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'jumlah' => 'required|integer|min:1',
        ]);
    
        // UPDATE: Menyimpan produk_id di keranjang
        $keranjang = Keranjang::updateOrCreate(
            [
                'id_produk' => $request->id_produk,
            ],
            [
                'jumlah' => DB::raw('jumlah + ' . $request->jumlah),
                // 'produk_id' => $request->id_produk, // Ini bisa ditambahkan jika Anda menyimpan produk_id di keranjang
            ]
        );
    
        return redirect()->route('keranjang.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }
    
    // Mengupdate jumlah produk di keranjang
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $keranjang = Keranjang::findOrFail($id);
        $keranjang->update([
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('keranjang.index')->with('success', 'Keranjang berhasil diperbarui.');
    }

    // Menghapus produk dari keranjang
    public function destroy($id)
    {
        $keranjang = Keranjang::findOrFail($id);
        $keranjang->delete();

        return redirect()->route('keranjang.index')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

   

    public function tambahProduk(Request $request)
    {
        $produk = Produk::find($request->input('id_produk'));

        // Cek apakah produk sudah ada di keranjang
        $itemKeranjang = Keranjang::where('id_produk', $produk->id_produk)->first();

        if ($itemKeranjang) {
            // Jika produk sudah ada, tambahkan jumlahnya
            $itemKeranjang->jumlah += $request->input('jumlah', 1);
            $itemKeranjang->save();
        } else {
            // Jika belum ada, buat item baru di keranjang
            // UPDATE: Menyimpan produk_id saat menambah produk baru ke keranjang
            Keranjang::create([
                'id_produk' => $produk->id_produk,
                'jumlah' => $request->input('jumlah', 1),
                // 'produk_id' => $produk->id_produk, // Ini bisa ditambahkan jika Anda menyimpan produk_id di keranjang
            ]);
        }

        return redirect()->route('keranjang.index')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }
}
