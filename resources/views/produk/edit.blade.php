@extends('layoutadmin.template')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Produk: {{ $produk->nama_obat }}</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('produk.update', $produk->id_produk) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nama_obat">Nama Obat</label>
                <input type="text" class="form-control @error('nama_obat') is-invalid @enderror" id="nama_obat" name="nama_obat" value="{{ old('nama_obat', $produk->nama_obat) }}" required>
                @error('nama_obat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="kode_obat">Kode Obat</label>
                <input type="text" class="form-control @error('kode_obat') is-invalid @enderror" id="kode_obat" name="kode_obat" value="{{ old('kode_obat', $produk->kode_obat) }}" required>
                @error('kode_obat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="kategori_obat">Kategori</label>
                <select class="form-control @error('kategori_obat') is-invalid @enderror" id="kategori_obat" name="kategori_obat" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategori as $item)
                        <option value="{{ $item->id_kategori }}" {{ (old('kategori_obat', $produk->kategori_obat) == $item->id_kategori) ? 'selected' : '' }}>{{ $item->nama_kategori }}</option>
                    @endforeach
                </select>
                @error('kategori_obat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="stok_awal">Stok Awal</label>
                <input type="number" class="form-control @error('stok_awal') is-invalid @enderror" id="stok_awal" name="stok_awal" value="{{ old('stok_awal', $produk->stok_awal) }}" required>
                @error('stok_awal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="stok_sisa">Stok Sisa</label>
                <input type="number" class="form-control @error('stok_sisa') is-invalid @enderror" id="stok_sisa" name="stok_sisa" value="{{ old('stok_sisa', $produk->stok_sisa) }}" required>
                @error('stok_sisa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="harga_jual">Harga Jual</label>
                <input type="number" step="0.01" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual" name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}" required>
                @error('harga_jual')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="tanggal_kadaluarsa">Tanggal Kadaluarsa</label>
                <input type="date" class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa', \Carbon\Carbon::parse($produk->tanggal_kadaluarsa)->format('Y-m-d')) }}" required>
                @error('tanggal_kadaluarsa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Produk</button>
        <a href="{{ route('produk.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
