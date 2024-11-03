@extends('layoutadmin.template')

@section('content')
<div class="container">
    <h1 class="mb-4">Detail Produk</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $produk->nama_obat }}</h5>
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $produk->id_produk }}</p>
            <p><strong>Kode Obat:</strong> {{ $produk->kode_obat }}</p>
            <p><strong>Kategori:</strong> {{ $produk->kategori->nama_kategori ?? 'Tidak ada kategori' }}</p>
            <p><strong>Stok Awal:</strong> {{ $produk->stok_awal }}</p>
            <p><strong>Stok Sisa:</strong> {{ $produk->stok_sisa }}</p>
            <p><strong>Harga Beli:</strong> {{ number_format($produk->harga_beli, 2, ',', '.') }}</p>
            <p><strong>Harga Jual:</strong> {{ number_format($produk->harga_jual, 2, ',', '.') }}</p>
            <p><strong>Tanggal Kadaluarsa:</strong> {{ \Carbon\Carbon::parse($produk->tanggal_kadaluarsa)->format('d/m/Y') }}</p>
            <p><strong>Total Harga Beli:</strong> {{ number_format($produk->harga_beli * $produk->stok_awal, 2, ',', '.') }}</p>
        </div>
    </div>
    <form action="{{ route('produk.destroy', $produk->id_produk) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
    </form>
    
    <a href="{{ route('produk.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
