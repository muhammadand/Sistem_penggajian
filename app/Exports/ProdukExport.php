<?php
namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProdukExport implements FromCollection
{
    public function collection()
    {
        return Produk::all();
    }
}

