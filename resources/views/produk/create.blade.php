@extends('layoutadmin.template')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Produk Baru</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('produk.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <!-- Nama Obat -->
            <div class="col-md-6">
                <label for="nama_obat" class="form-label">Nama Obat</label>
                <input type="text" class="form-control" id="nama_obat" name="nama_obat" value="{{ old('nama_obat') }}" required>
            </div>

            <!-- Kode Obat -->
            <div class="col-md-6">
                <label for="kode_obat" class="form-label">Kode Obat</label>
                <input type="text" class="form-control" id="kode_obat" name="kode_obat" value="{{ old('kode_obat') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Kategori Obat -->
            <div class="col-md-6">
                <label for="kategori_obat" class="form-label">Kategori</label>
                <select class="form-select" id="kategori_obat" name="kategori_obat" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Stok Awal -->
            <div class="col-md-6">
                <label for="stok_awal" class="form-label">Stok Awal</label>
                <input type="number" class="form-control" id="stok_awal" name="stok_awal" value="{{ old('stok_awal') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Stok Sisa -->
            <div class="col-md-6">
                <label for="stok_sisa" class="form-label">Stok Sisa</label>
                <input type="number" class="form-control" id="stok_sisa" name="stok_sisa" value="{{ old('stok_sisa') }}" required>
            </div>

            <!-- Harga Beli -->
            <div class="col-md-6">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="number" class="form-control" id="harga_beli" name="harga_beli" value="{{ old('harga_beli') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Harga Jual -->
            <div class="col-md-6">
                <label for="harga_jual" class="form-label">Harga Jual</label>
                <input type="number" class="form-control" id="harga_jual" name="harga_jual" value="{{ old('harga_jual') }}" required>
            </div>

            <!-- Satuan -->
            <div class="col-md-6">
                <label for="satuan" class="form-label">Satuan</label>
                <input type="text" class="form-control" id="satuan" name="satuan" value="{{ old('satuan') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Tanggal Kadaluarsa -->
            <div class="col-md-6">
                <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                <input type="date" class="form-control" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa') }}" required>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('produk.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
