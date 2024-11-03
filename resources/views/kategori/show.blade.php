@extends('layoutadmin.template')

@section('content')
    <div class="container">
        <h1>Detail Produk Obat</h1>

        <table class="table table-bordered">
            <tr>
                <th>Nama Obat</th>
                <td>{{ $productObat->nama_obat }}</td>
            </tr>
            <tr>
                <th>Stock</th>
                <td>{{ $productObat->stock }}</td>
            </tr>
            <tr>
                <th>Satuan</th>
                <td>{{ $productObat->satuan }}</td>
            </tr>
            <tr>
                <th>Harga</th>
                <td>{{ $productObat->harga }}</td>
            </tr>
            <tr>
                <th>Tanggal Kadaluarsa</th>
                <td>{{ $productObat->tanggal_kadaluarsa->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $productObat->keterangan }}</td>
            </tr>
        </table>

        <a href="{{ route('product_obat.index') }}" class="btn btn-primary">Kembali</a>
    </div>
@endsection
