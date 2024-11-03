<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // Menampilkan daftar kategori
    public function index()
    {
        $kategoris = Kategori::all(); // Ambil semua kategori
        return view('kategori.index', compact('kategoris')); // Kembalikan tampilan dengan data kategori
    }

    // Menampilkan formulir untuk membuat kategori baru
    public function create()
    {
        return view('kategori.create'); // Kembalikan tampilan untuk formulir pembuatan kategori
    }

    // Menyimpan kategori baru ke dalam database
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create($request->all()); // Simpan kategori baru
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Menampilkan formulir untuk mengedit kategori yang ada
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori')); // Kembalikan tampilan untuk formulir edit kategori
    }

    // Mengupdate kategori yang ada
    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all()); // Update kategori yang ada
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate.');
    }

    // Menghapus kategori yang ada
    public function destroy(Kategori $kategori)
    {
        $kategori->delete(); // Hapus kategori
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
