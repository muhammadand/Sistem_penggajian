<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyStock extends Model
{
    use HasFactory;

    protected $table = 'safety_stock'; // Nama tabel

    protected $primaryKey = 'id'; // Primary key tabel

    protected $fillable = [
        'id_produk', // Foreign key ke produk
        'permintaan_harian', // Permintaan harian
        'waktu_pengiriman', // Waktu pengiriman dalam hari
        'safety_stock', // Safety stock yang dihitung
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
