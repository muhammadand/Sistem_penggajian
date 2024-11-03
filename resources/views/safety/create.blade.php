@extends('layoutadmin.template')

@section('content')
<div class="container">
    <h1 class="mb-4">Laporan Penjualan</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Pencarian -->
    <form action="" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control" placeholder="Cari produk..." value="{{ request('keyword') }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>
    
    <!-- Tabel Laporan dengan fitur scroll horizontal -->
    <div style="overflow-x: auto; white-space: nowrap;">
        <table class="table table-striped table-bordered" style="min-width: 900px;">
            <thead class="thead-light">
                <tr>
                    <th>Bulan & Tahun</th>
                    <th>Nama Obat</th>
                    <th>Jumlah Terjual</th>
                    <th>Harga Satuan</th>
                    <th>Total Penjualan</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Permintaan</th>
                    <th>Rata-Rata Penjualan</th>
                    <th>Rata-Rata Permintaan</th>
                </tr>
            </thead>
            <tbody id="laporanBody">
                @foreach($laporan as $monthYear => $produkData)
                    @foreach($produkData as $namaObat => $data)
                        <tr data-nama-obat="{{ $namaObat }}" data-ratarata-penjualan="{{ $data['rata_rata_penjualan'] }}">
                            <td>{{ $monthYear }}</td>
                            <td>{{ $namaObat }}</td>
                            <td>{{ $data['jumlah_terjual'] }}</td>
                            <td>{{ number_format($data['harga_satuan'], 2, ',', '.') }}</td>
                            <td>{{ number_format($data['total_penjualan'], 2, ',', '.') }}</td>
                            <td>{{ $data['jumlah_transaksi'] }}</td>
                            <td>{{ $data['total_permintaan'] }}</td>
                            <td>{{ number_format($data['rata_rata_penjualan'], 2, ',', '.') }}</td>
                            <td>{{ number_format($data['rata_rata_permintaan'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="container">
    <h1 class="my-4">Tambah Safety Stock</h1>

    <form action="{{ route('safety.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="id_produk">Pilih Produk</label>
            <select name="id_produk" id="id_produk" class="form-control" required>
                <option value="" disabled selected>Pilih Produk</option>
                @foreach ($produks as $produk)
                    <option value="{{ $produk->id_produk }}">{{ $produk->nama_obat }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="bulan">Pilih Bulan</label>
            <select name="bulan" id="bulan" class="form-control" required>
                <option value="" disabled selected>Pilih Bulan</option>
                @foreach($laporan as $monthYear => $produkData)
                    <option value="{{ $monthYear }}">{{ $monthYear }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="permintaan_harian">Permintaan Harian</label>
            <input type="number" name="permintaan_harian" id="permintaan_harian" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="waktu_pengiriman">Waktu Pengiriman (Hari)</label>
            <input type="number" name="waktu_pengiriman" id="waktu_pengiriman" class="form-control" required>
        </div>

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('safety.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const idProdukSelect = document.getElementById('id_produk');
        const bulanSelect = document.getElementById('bulan');

        // Fungsi untuk memperbarui permintaan harian
        function updatePermintaanHarian() {
            const selectedProduk = idProdukSelect.value;
            const selectedBulan = bulanSelect.value;
            const rows = document.querySelectorAll('#laporanBody tr');

            let rataRataPermintaan = 0; // Inisialisasi untuk rata-rata permintaan
            let found = false;

            rows.forEach((row) => {
                const namaObat = row.dataset.namaObat;
                const bulanTahun = row.children[0].innerText; // Ambil bulan & tahun dari tabel

                if (namaObat === selectedProduk && bulanTahun === selectedBulan) {
                    const rataRataPermintaanCell = parseFloat(row.children[8].innerText.replace('.', '').replace(',', '.')); // Ambil dari kolom Rata-Rata Permintaan
                    if (!isNaN(rataRataPermintaanCell)) {
                        rataRataPermintaan = rataRataPermintaanCell; // Ambil nilai rata-rata permintaan
                        found = true; // Menandakan bahwa produk dan bulan ditemukan
                    }
                }
            });

            // Jika produk dan bulan ditemukan, update input
            if (found) {
                document.getElementById('permintaan_harian').value = rataRataPermintaan; // Update input permintaan harian
            } else {
                document.getElementById('permintaan_harian').value = ''; // Kosongkan jika tidak ditemukan
            }
        }

        // Event listener untuk dropdown
        idProdukSelect.addEventListener('change', updatePermintaanHarian);
        bulanSelect.addEventListener('change', updatePermintaanHarian);
    });
</script>


@endsection
