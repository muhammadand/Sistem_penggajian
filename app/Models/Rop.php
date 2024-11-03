<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rop extends Model
{
    use HasFactory;

    protected $table = 'rop'; // Menentukan tabel terkait dengan model ini

    protected $fillable = [
        'product_id', 'lead_time', 'daily_usage', 'safety_stock', 'rop'
    ];

    // Relasi ke tabel Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'id_produk');
    }
}
