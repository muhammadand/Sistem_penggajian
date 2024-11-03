<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPenjualan extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model ini
    protected $table = 'transaksi_penjualan';
    protected $primaryKey = 'id_transaksi'; 

    // Definisikan atribut yang dapat diisi massal
    protected $fillable = [
        'nama_obat',
        'uang_masuk',
        'jumlah',
        'produk_id',
        'tanggal_transaksi'
    ];

    // Menggunakan timestamps
    public $timestamps = true;

    // Relasi ke model Keranjang
    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'id_keranjang');
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}




