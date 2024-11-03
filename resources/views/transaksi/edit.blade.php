@extends('layoutadmin.template')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Transaksi Penjualan</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('transaksi.update', $transaksi->id_transaksi) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="uang_masuk" class="form-label">Uang Masuk</label>
            <input type="number" class="form-control" id="uang_masuk" name="uang_masuk" value="{{ old('uang_masuk', $transaksi->uang_masuk) }}" required>
        </div>

        <div class="mb-3">
            <label for="total_harga" class="form-label">Total Harga</label>
            <input type="number" class="form-control" id="total_harga" name="total_harga" value="{{ old('total_harga', $transaksi->total_harga) }}" required>
        </div>

        <div class="mb-3">
            <label for="tanggal_transaksi" class="form-label">Tanggal Transaksi</label>
            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', $transaksi->tanggal_transaksi->format('Y-m-d')) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Transaksi</button>
        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
