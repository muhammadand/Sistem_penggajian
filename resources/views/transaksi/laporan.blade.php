@extends('layoutadmin.template')

@section('content')
<div class="container">
    

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Penjualan</h1>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

  
<!-- Header Laporan -->
<div class="header text-center mb-2">
    <img src="path/to/logo.png" alt="Logo Perusahaan" class="logo mb-3" style="max-width: 100px;"> <!-- Ganti dengan path logo Anda -->
    <h4>Apotek Cigadung</h4>
    <div class="marquee">
        <div class="marquee-content">
            <p>Alamat: Kuningan, Jawa Barat &nbsp; | &nbsp; Telepon: (021) 12345678 &nbsp; | &nbsp; Email: apotekcigadung@gmail.com</p>
        </div>
    </div>
</div>
  <!-- Form Pencarian -->
  <div class="d-flex justify-content-between mb-4">
    <form action="" method="GET" class="me-2">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control" placeholder="Cari produk..." value="{{ request('keyword') }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>
</div>
    <!-- Tabel Laporan Penjualan -->
    <div style="overflow-x: auto; white-space: nowrap;">
        <table class="table table-striped table-bordered " style="min-width: 900px;">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Bulan & Tahun</th>
                    <th>Nama Obat</th>
                    <th>Jumlah Terjual</th>
                    <th>Harga Satuan</th>
                    <th>Total Penjualan</th>
                    <th>Jumlah Transaksi</th>
                </tr>
            </thead>
            <tbody id="laporanBody">
                @php $no = 1; @endphp
                @foreach($laporan as $monthYear => $produkData)
                    @foreach($produkData as $namaObat => $data)
                        <tr>
                            <td>{{ $no++ }}</td> <!-- Menambahkan nomor urut -->
                            <td>{{ $monthYear }}</td>
                            <td>{{ $namaObat }}</td>
                            <td>{{ $data['jumlah_terjual'] }}</td>
                            <td>{{ number_format($data['harga_satuan'], 2, ',', '.') }}</td>
                            <td>{{ number_format($data['total_penjualan'], 2, ',', '.') }}</td>
                            <td>{{ $data['jumlah_transaksi'] }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .marquee {
        overflow: hidden;
        white-space: nowrap;
        width: 100%;
        background: #f8f9fa; /* Warna latar belakang bisa disesuaikan */
        border: 1px solid #dee2e6; /* Border bisa disesuaikan */
        padding: 10px 0; /* Padding atas dan bawah */
    }

    .marquee-content {
        display: inline-block;
        animation: marquee 15s linear infinite;
    }

    @keyframes marquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
</style>
@endsection
