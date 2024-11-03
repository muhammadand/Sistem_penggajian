@extends('layoutadmin.template')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Transaksi Penjualan</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form untuk Edit Transaksi -->
    <form action="{{ route('transaksi.update', $transaksi->id_transaksi) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="tanggal_transaksi" class="form-label">Tanggal Transaksi</label>
            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" value="{{ $transaksi->tanggal_transaksi->format('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label for="nama_obat" class="form-label">Nama Obat</label>
            <input type="text" class="form-control" id="nama_obat" name="nama_obat" value="{{ $transaksi->nama_obat }}" required>
        </div>

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" value="{{ $transaksi->jumlah }}" required>
        </div>

        <div class="mb-3">
            <label for="harga_jual" class="form-label">Harga Satuan</label>
            <input type="text" class="form-control" id="harga_jual" name="harga_jual" value="{{ number_format($transaksi->produk->harga_jual, 2, ',', '.') }}" readonly>
        </div>

        <div class="mb-3">
            <label for="total_harga" class="form-label">Total Harga</label>
            <input type="text" class="form-control" id="total_harga" name="total_harga" value="{{ number_format($total_harga, 2, ',', '.') }}" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Update Transaksi</button>
    </form>
</div>
@endsection
