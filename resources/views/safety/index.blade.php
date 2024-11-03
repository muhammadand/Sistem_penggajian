<!-- resources/views/safety/index.blade.php -->

@extends('layoutadmin.template')

@section('content')
<div class="container">
    <h1>Safety Stock</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID Produk</th>
                <th>Nama Produk</th> <!-- Menambahkan kolom Nama Produk -->
                <th>Permintaan Harian</th>
                <th>Waktu Pengiriman</th>
                <th>Safety Stock</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($safetyStocks as $safetyStock)
            <tr>
                <td>{{ $safetyStock->produk->id_produk }}</td>
                <td>{{ $safetyStock->produk->nama_obat }}</td> <!-- Menampilkan Nama Produk -->
                <td>{{ $safetyStock->permintaan_harian }}</td>
                <td>{{ $safetyStock->waktu_pengiriman }}</td>
                <td>{{ $safetyStock->safety_stock }}</td>
                <td>
                    <a href="{{ route('safety.show', $safetyStock->id) }}" class="btn btn-info">Detail</a>
                    <a href="{{ route('safety.edit', $safetyStock->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('safety.destroy', $safetyStock->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('safety.create') }}" class="btn btn-primary">Tambah Safety Stock</a>
</div>
@endsection
