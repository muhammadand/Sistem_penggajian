@extends('layoutadmin.template')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">KASIR APOTEK CIGADUNG</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Pencarian Produk -->
    <form action="{{ route('keranjang.cari') }}" method="POST" class="mb-3">
        @csrf
        <div class="input-group mb-4" style="max-width: 400px;">
            <input type="text" name="keyword" class="form-control" placeholder="Cari produk..." value="{{ old('keyword', $keyword ?? '') }}">
            <button type="submit" class="btn btn-primary ml-2">Cari</button>
        </div>
    </form>

    <!-- Tabel Hasil Pencarian Produk -->
    @if(isset($produk) && $produk->count())
    <h5>Hasil Pencarian:</h5>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <ul class="list-group mb-4">
                @foreach($produk as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-1">
                        <span style="font-size: 14px;">{{ $item->nama_obat }} - {{ number_format($item->harga_jual, 2, ',', '.') }}</span>
                        <form action="{{ route('keranjang.store') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="id_produk" value="{{ $item->id_produk }}">
                            <input type="number" name="jumlah" min="1" value="1" style="width: 60px; font-size: 14px;" class="form-control form-control-sm d-inline" required>
                            <button type="submit" class="btn btn-success btn-sm ml-2">Tambah</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Tabel Keranjang -->
    <table class="table table-bordered table-responsive-sm">
        <thead class="thead-light">
            <tr>
                <th>ID Produk</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="keranjang-table-body">
            @foreach($keranjang as $item)
                <tr>
                    <td>{{ $item->produk->id_produk }}</td>
                    <td>{{ $item->produk->nama_obat }}</td>
                    <td>
                        <form action="{{ route('keranjang.update', $item->id_keranjang) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <div class="input-group" style="max-width: 150px;">
                                <input type="number" name="jumlah" value="{{ $item->jumlah }}" min="1" class="form-control text-center jumlah-produk" data-harga="{{ $item->produk->harga_jual }}">
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </div>
                        </form>
                    </td>
                    <td class="harga-satuan">{{ number_format($item->produk->harga_jual, 2, ',', '.') }}</td>
                    <td class="total-harga">{{ number_format($item->produk->harga_jual * $item->jumlah, 2, ',', '.') }}</td>
                    <td>
                        <form action="{{ route('keranjang.destroy', $item->id_keranjang) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total, Uang Masuk, Kembalian, dan Simpan Transaksi -->
    <div class="d-flex justify-content-between mt-4">
        <h5>Total Harga: <span id="total-harga-all" class="text-success">{{ number_format($total_harga, 2, ',', '.') }}</span></h5> 

        <div>
            <!-- Form untuk uang masuk dan hitung kembalian -->
            <div class="input-group mb-3" style="max-width: 400px;">
                <input type="number" id="uang-masuk" class="form-control" placeholder="Uang Masuk" required>
                <span class="input-group-text">Rp</span>
            </div>
            <h5>Kembalian: <span id="kembalian" class="text-success">0,00</span></h5>
        </div>

        <!-- Form Simpan Transaksi -->
        <form action="{{ route('transaksi.store') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" id="total-harga-input" name="total_harga" value="{{ $total_harga }}">
            <input type="hidden" name="uang_masuk" id="uang-masuk-hidden">
            <input type="hidden" name="tanggal_transaksi" value="{{ now() }}">
            @foreach($keranjang as $item)
                <input type="hidden" name="keranjang[{{ $loop->index }}][produk_id]" value="{{ $item->produk->id_produk }}">
                <input type="hidden" name="keranjang[{{ $loop->index }}][nama_obat]" value="{{ $item->produk->nama_obat }}">
                <input type="hidden" name="keranjang[{{ $loop->index }}][jumlah]" value="{{ $item->jumlah }}">
            @endforeach
            <button type="submit" class="btn btn-primary ml-1">Simpan Transaksi</button>
        </form>
    </div>
</div>

<!-- JavaScript untuk menghitung total harga dan kembalian -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function hitungTotalHarga() {
            let totalHargaSemua = 0;
            
            document.querySelectorAll('.jumlah-produk').forEach(function (input) {
                const hargaSatuan = parseFloat(input.getAttribute('data-harga'));
                const jumlah = parseInt(input.value);
                const totalHarga = hargaSatuan * jumlah;
                
                const totalHargaTd = input.closest('tr').querySelector('.total-harga');
                totalHargaTd.textContent = totalHarga.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                totalHargaSemua += totalHarga;
            });

            const totalHargaAllSpan = document.getElementById('total-harga-all');
            totalHargaAllSpan.textContent = totalHargaSemua.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const totalHargaInput = document.getElementById('total-harga-input');
            totalHargaInput.value = totalHargaSemua;
        }

        function hitungKembalian() {
            const totalHarga = parseFloat(document.getElementById('total-harga-input').value);
            const uangMasuk = parseFloat(document.getElementById('uang-masuk').value);

            const kembalian = uangMasuk - totalHarga;
            document.getElementById('kembalian').textContent = kembalian.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            document.getElementById('uang-masuk-hidden').value = uangMasuk;
        }

        document.querySelectorAll('.jumlah-produk').forEach(function (input) {
            input.addEventListener('input', hitungTotalHarga);
        });

        document.getElementById('uang-masuk').addEventListener('input', hitungKembalian);

        hitungTotalHarga();
    });
</script>
@endsection
