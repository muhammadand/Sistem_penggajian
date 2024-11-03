@extends('layoutadmin.template')

@section('content')
<div class="container mt-4">
    <h1 class="text-2xl font-bold mb-4">Daftar Reorder Point (ROP)</h1>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Daftar ROP</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Nilai ROP</th>
                        <th>Tanggal Dihitung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rops as $key => $rop)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $rop->produk->nama_obat }}</td>
                            <td>{{ $rop->rop }}</td>
                            <td>{{ $rop->created_at->format('d-m-Y H:i:s') }}</td>
                            <td>
                                <a href="{{ route('rop.edit', $rop->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('rop.destroy', $rop->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('rop.create') }}" class="btn btn-primary mt-4">Tambah ROP Baru</a>
        </div>
    </div>
</div>
@endsection
