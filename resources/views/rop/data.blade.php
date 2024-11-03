@extends('layoutadmin.template')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Data Transaksi Penjualan</h1>
    </div>

    <form method="GET" action="{{ route('rop.data') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control" placeholder="Cari berdasarkan nama obat atau tanggal..." value="{{ request('keyword') }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </div>
        </div>
        <div class="input-group mt-2">
            <input type="number" name="lead_time" class="form-control" placeholder="Lead Time (hari)" value="{{ request('lead_time') }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Terapkan</button>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Data Transaksi -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Tahun</th>
                    <th>Total Jumlah</th>
                    <th>Rata-rata Penjualan Harian</th> <!-- Kolom baru untuk rata-rata bulanan -->
                    <th>Safety Stock</th> <!-- Kolom baru untuk safety stock -->
                    <th>ROP</th> <!-- Kolom baru untuk ROP -->
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $index => $item)
                    <tr>
                        <td>{{ $transaksi->firstItem() + $index }}</td>
                        <td>{{ $item->nama_obat }}</td>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ $item->total_jumlah }}</td>
                        <td>{{ number_format($item->rata_rata_harian, 2) }}</td> <!-- Menampilkan rata-rata bulanan -->
                        <td>{{ number_format($item->safety_stock, 2) }}</td> <!-- Menampilkan safety stock -->
                        <td>{{ number_format($item->rop, 2) }}</td> <!-- Menampilkan ROP -->
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    {{ $transaksi->links() }}
</div>
@endsection
