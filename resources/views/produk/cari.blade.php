@extends('layoutadmin.template')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Cari Produk</h1>

    <form action="{{ route('produk.cari') }}" method="GET" class="mb-4">
        <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Cari produk..." class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Cari</button>
    </form>

    @if($produk->isNotEmpty())
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produk as $item)
                    <tr>
                        <td>{{ $item->nama_obat }}</td>
                        <td>{{ number_format($item->harga_jual, 2, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('keranjang.tambah') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_produk" value="{{ $item->id_produk }}">
                                <button type="submit" class="btn btn-success">Tambah ke Keranjang</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Produk tidak ditemukan.</p>
    @endif
</div>
@endsection
