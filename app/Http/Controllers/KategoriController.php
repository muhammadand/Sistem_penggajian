<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk; // Pastikan untuk mengimpor model Produk
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // Menampilkan daftar kategori
    public function index()
    {
        $kategoris = Kategori::all(); // Ambil semua kategori
        $notifications = $this->generateNotifications(); // Ambil notifikasi berdasarkan produk
        return view('kategori.index', compact('kategoris', 'notifications')); // Kembalikan tampilan dengan data kategori dan notifikasi
    }

    // Menampilkan formulir untuk membuat kategori baru
    public function create()
    {
        $notifications = $this->generateNotifications(); // Ambil notifikasi berdasarkan produk
        return view('kategori.create', compact('notifications')); // Kembalikan tampilan untuk formulir pembuatan kategori dan notifikasi
    }

    // Menyimpan kategori baru ke dalam database
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create($request->all()); // Simpan kategori baru
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.')->with('notifications', $this->generateNotifications());
    }

    // Menampilkan formulir untuk mengedit kategori yang ada
    public function edit(Kategori $kategori)
    {
        $notifications = $this->generateNotifications(); // Ambil notifikasi berdasarkan produk
        return view('kategori.edit', compact('kategori', 'notifications')); // Kembalikan tampilan untuk formulir edit kategori dan notifikasi
    }

    // Mengupdate kategori yang ada
    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all()); // Update kategori yang ada
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate.')->with('notifications', $this->generateNotifications());
    }

    // Menghapus kategori yang ada
    public function destroy(Kategori $kategori)
    {
        $kategori->delete(); // Hapus kategori
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.')->with('notifications', $this->generateNotifications());
    }

    // Fungsi untuk mengambil notifikasi produk
    private function generateNotifications()
    {
        // Ambil semua produk
        $produk = Produk::with('rop')->get(); 
        $notifications = []; // Inisialisasi array notifikasi

        // Implementasi logika notifikasi sesuai kebutuhan
        foreach ($produk as $item) {
            // Logika notifikasi seperti sebelumnya
            // Contoh: if ($item->stok < $item->rop) { $notifications[] = "Stok untuk {$item->nama_obat} kurang."; }
        }

        return $notifications; // Kembalikan array notifikasi
    }
}
