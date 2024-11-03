<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    protected $table = 'keranjang'; // Nama tabel keranjang

    protected $primaryKey = 'id_keranjang'; // Primary key

    protected $fillable = [
        'id_produk',
        'jumlah',
    ];

    // Relasi ke model Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
    public function transaksi()
{
    return $this->belongsTo(TransaksiPenjualan::class, 'id_transaksi');
}


}
