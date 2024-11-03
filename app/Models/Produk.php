<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk'; // Nama tabel

    protected $primaryKey = 'id_produk'; // Primary key

    protected $fillable = [
        'nama_obat',
        'kode_obat',
        'kategori_obat',
        'stok_awal',
        'stok_sisa',
        'harga_beli',          // Menambahkan harga_beli
        'harga_jual',
        'satuan',              // Menambahkan satuan
        'total',               // Menambahkan total
        'tanggal_kadaluarsa',
        'keterangan'
    ];

    // Relasi ke tabel kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_obat', 'id_kategori');
    }

    // Relasi ke tabel TransaksiPenjualan
    public function transaksi()
    {
        return $this->hasMany(TransaksiPenjualan::class, 'produk_id', 'id_produk'); // Perbaiki menjadi 'produk_id'
    }

    // Relasi ke tabel ROP
    public function rop()
    {
        return $this->hasOne(Rop::class, 'product_id', 'id_produk'); // Sesuaikan dengan nama kolom foreign key
    }

    // Metode untuk menghitung total penjualan per produk
    public function totalPenjualan()
    {
        return $this->transaksi()->sum('jumlah'); // Menghitung total penjualan dari semua transaksi
    }
    
}
