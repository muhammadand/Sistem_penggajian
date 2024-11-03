<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi Laravel
    protected $table = 'kategori';

    // Tentukan primary key jika tidak sesuai dengan konvensi Laravel
    protected $primaryKey = 'id_kategori';

    // Jika Anda tidak ingin mengelola timestamps (created_at, updated_at), set false
    public $timestamps = true;

    // Tentukan atribut yang dapat diisi
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];
}
