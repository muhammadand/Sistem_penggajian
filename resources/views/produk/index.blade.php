@extends('layoutadmin.template')

@section('content')
<div class="container">
    <h1 class="mb-4">Daftar Produk</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Pencarian -->
    <form action="{{ route('produk.index') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <!-- Tambah Produk dan Export -->
    <div class="mb-3 d-flex justify-content-between">
        <a href="{{ route('produk.create') }}" class="btn btn-success">Tambah Produk</a>
        <div>
            <a href="{{ route('produk.export') }}" class="btn btn-secondary">Export Excel</a>
        </div>
    </div>

    <!-- Tabel Produk dengan fitur scroll horizontal -->
    <div style="overflow-x: auto; white-space: nowrap;">
        <table class="table table-striped table-bordered" style="min-width: 800px;"> <!-- Atur lebar minimum tabel -->
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Nama Obat</th>
                    <th>Kode Obat</th>
                    <th>Stok Awal</th>
                    <th>Stok Sisa</th>
                    {{-- <th>ROP (Reorder Point)</th> <!-- Kolom ROP -->
                    <th>Status</th> <!-- Kolom Status --> --}}
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Tanggal Kadaluarsa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produk as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_obat }}</td>
                    <td>{{ $item->kode_obat }}</td>
                    <td>{{ $item->stok_awal }}</td>
                    <td>{{ $item->stok_sisa }}</td>
            
                    <!-- Akses nilai rop dari relasi -->
                    {{-- <td>{{ $item->rop ? $item->rop->rop : 'N/A' }}</td>
            
                    <td>
                        <!-- Cek apakah stok sisa di bawah atau sama dengan nilai ROP -->
                        @if($item->rop && $item->stok_sisa <= $item->rop->rop)
                            <span class="text-danger">Harus Pesan Ulang</span>
                        @else
                            <span class="text-success">Stok Aman</span>
                        @endif
                    </td> --}}
            
                    <td>{{ number_format($item->harga_beli, 2, ',', '.') }}</td>
                    <td>{{ number_format($item->harga_jual, 2, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') }}</td>
                    <td style="display: flex; gap: 5px; justify-content: center;">
                        <a href="{{ route('produk.show', $item->id_produk) }}" class="btn btn-info btn-sm">Show</a>
                        <a href="{{ route('produk.edit', $item->id_produk) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('produk.destroy', $item->id_produk) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $produk->links() }}
    </div>
</div>

@endsection
