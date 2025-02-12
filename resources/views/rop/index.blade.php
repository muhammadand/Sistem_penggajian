@extends('layoutadmin.template')

@section('content')
<div class="container">
     <!-- Page Heading -->
     <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Persediaan Bulanan</h1>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Pencarian dan Input Lead Time -->
    <div class="d-flex justify-content-between mb-4">
        <form action="" method="GET" class="me-2">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Cari produk..." value="{{ request('keyword') }}">
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </form>
        <form id="leadTimeForm">
            <div class="input-group">
                <input type="number" id="leadTime" class="form-control" placeholder="Lead Time (hari)" value="2">
                <button class="btn btn-secondary" type="button" id="setLeadTime">Atur Lead Time</button>
            </div>
        </form>
    </div>

    <!-- Status Highlight -->
    <div id="statusHighlight" class="alert d-none"></div> <!-- Elemen untuk status -->
    <ul id="productList" class="list-group mt-3 d-none"></ul> <!-- Daftar produk yang perlu dipesan ulang -->

    <!-- Tabel Laporan dengan fitur scroll horizontal -->
    <div class="container mt-3">
        <h2 class="text-center mb-2">Laporan Penjualan Bulanan</h2>
        <hr>
    <div style="overflow-x: auto; white-space: nowrap;">
        <table class="table table-striped table-bordered mt-1" style="min-width: 900px;">
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
                    <th>Stok Sisa</th> <!-- Kolom Stok Sisa -->
                    <th>Safety Stock</th>
                    <th>Reorder Point</th>
                </tr>
            </thead>
            <tbody id="laporanBody">
                @foreach($laporan as $monthYear => $produkData)
                    @foreach($produkData as $namaObat => $data)
                        @php
                            $stokSisa = $data['stok_sisa'];
                            $reorderPoint = 0; // Variabel untuk menyimpan nilai ROP
                        @endphp
                        <tr>
                            <td>{{ $monthYear }}</td>
                            <td>{{ $namaObat }}</td>
                            <td>{{ $data['jumlah_terjual'] }}</td>
                            <td>{{ number_format($data['harga_satuan'], 2, ',', '.') }}</td>
                            <td>{{ number_format($data['total_penjualan'], 2, ',', '.') }}</td>
                            <td>{{ $data['jumlah_transaksi'] }}</td>
                            <td>{{ $data['total_permintaan'] }}</td>
                            <td>{{ number_format($data['rata_rata_penjualan'], 2, ',', '.') }}</td>
                            <td>{{ number_format($data['rata_rata_permintaan'], 2, ',', '.') }}</td>
                            <td>{{ $stokSisa }}</td> <!-- Menampilkan Stok Sisa -->
                            <td class="safety-stock"></td> <!-- Kolom untuk safety stock -->
                            <td class="reorder-point"></td> <!-- Kolom untuk ROP -->
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
    </div>

    <div class="mb-3 mt-3 d-flex justify-content-between">
        <a href="{{ route('rop.data') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-calendar-alt me-2 mr-2"></i> Data Tahunan
        </a>
        
    </div>

    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Diagram Chart Penjualan</h6>
        </div>
        <!-- Card Body -->
        <div class="card-body">
             <!-- Area untuk Chart -->
    <div class="my-4">
        <canvas id="salesChart"></canvas>
    </div>
            
        </div>
    </div>
   
</div>

<script>
    
    let produkIndexUrl = "{{ route('produk.index') }}"; 
    let leadTime = 2; // Nilai default lead time

    document.getElementById('setLeadTime').addEventListener('click', function() {
        const inputLeadTime = document.getElementById('leadTime').value;
        leadTime = parseInt(inputLeadTime) || 2; // Jika tidak valid, gunakan 2
        calculateSafetyStockAndROP();
        updateChart();
    });

    function calculateSafetyStockAndROP() {
    const safetyStockCells = document.querySelectorAll('.safety-stock');
    const reorderPointCells = document.querySelectorAll('.reorder-point');
    const zValue = 1.65; // Z-value for a 95% service level
    const productList = document.getElementById('productList');
    productList.innerHTML = ''; // Clear previous product list
    let needReorder = false; // Flag to track if any products need reordering

    safetyStockCells.forEach((cell, index) => {
        const jumlahTerjual = parseFloat(cell.closest('tr').children[2].innerText);
        const rataRataPermintaan = parseFloat(cell.closest('tr').children[8].innerText.replace('.', '').replace(',', '.'));
        const namaObat = cell.closest('tr').children[1].innerText;
        const monthYear = cell.closest('tr').children[0].innerText; // Get month & year

        if (!isNaN(jumlahTerjual) && !isNaN(rataRataPermintaan)) {
            const sigma = Math.sqrt(rataRataPermintaan);
            const safetyStock = (zValue * sigma * Math.sqrt(leadTime)).toFixed(2);
            cell.innerText = safetyStock;

            const reorderPoint = (rataRataPermintaan * leadTime + parseFloat(safetyStock)).toFixed(2);
            reorderPointCells[index].innerText = reorderPoint;

            const stokSisa = parseFloat(cell.closest('tr').children[9].innerText);

            // Check if stock is below reorder point
            if (stokSisa <= reorderPoint) {
                needReorder = true;
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                listItem.innerHTML = `${namaObat} - ${monthYear} <button class="btn btn-sm btn-primary" onclick="window.location.href='${produkIndexUrl}'">Pesan Ulang</button>`;
                productList.appendChild(listItem);
            }
        } else {
            cell.innerText = 'N/A';
            reorderPointCells[index].innerText = 'N/A';
        }
    });

    // Update reorder status
    const statusHighlight = document.getElementById('statusHighlight');
    if (needReorder) {
        statusHighlight.classList.remove('d-none');
        statusHighlight.classList.add('alert-warning');
        statusHighlight.innerText = "Peringatan: Beberapa produk perlu dipesan ulang!";
        productList.classList.remove('d-none'); // Show product list if needed
    } else {
        statusHighlight.classList.add('d-none');
        productList.classList.add('d-none'); // Hide if nothing needs reordering
    }
}

    function orderProduct(namaObat) {
        alert(`Pesanan ulang untuk produk: ${namaObat}`);
        // Implementasi pemesanan ulang bisa ditambahkan di sini
    }

    function updateChart() {
        const labels = [];
        const salesData = [];

        const laporanBody = document.querySelectorAll('#laporanBody tr');
        laporanBody.forEach(row => {
            labels.push(row.children[0].innerText); // Ambil bulan & tahun
            salesData.push(parseFloat(row.children[2].innerText)); // Ambil jumlah terjual
        });

        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: salesData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        calculateSafetyStockAndROP();
        updateChart();
    });
</script>
@endsection
