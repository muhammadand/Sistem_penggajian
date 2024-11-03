@extends('layoutadmin.template')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Transaksi Penjualan</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form pencarian dan filter periode -->
    <form action="{{ route('transaksi.search') }}" method="GET" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari transaksi..." value="{{ old('keyword') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                    <button type="submit" class="btn btn-info">Filter Periode</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Tabel transaksi -->
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Tanggal Transaksi</th>
                <th>Nama Obat</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>harga satuan</th>
                <th>total harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if($transaksis->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">Tidak ada transaksi ditemukan.</td>
                </tr>
            @else
                @foreach($transaksis as $transaksi)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d-m-Y') }}</td>
                        <td>{{ $transaksi->nama_obat }}</td> <!-- Menampilkan Nama Obat -->
                        <td>{{ $transaksi->jumlah }}</td> <!-- Menampilkan Jumlah -->
                        <td>{{ $transaksi->produk->satuan ?? 'N/A' }}</td> <!-- Menampilkan Satuan -->
                        <td>{{ number_format($transaksi->produk->harga_jual, 2, ',', '.') }}</td>
                        <td> {{ number_format($transaksi->jumlah * $transaksi->produk->harga_jual, 2, ',', '.') }} <!-- Menghitung Total Harga --></td>
                        <td>
                            <a href="{{ route('transaksi.edit', $transaksi->id_transaksi) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('transaksi.destroy', $transaksi->id_transaksi) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

   
</div>
@endsection
