@extends('layoutadmin.template')

@section('content')
             <!-- Begin Page Content -->
             <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                </div>

                <!-- Content Row -->
                <div class="row">

                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Jumlah Produk</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProduk }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-pills fa-2x text-gray-300"></i>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Pendapatan</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            rata-rata penjualan</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"> Rp.{{ number_format($rataRataPenjualan, 2) }} </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Requests Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Jumlah Transaksi</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"> {{ $jumlahTransaksi }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-receipt fa-2x text-gray-300"></i>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Row -->

                <div class="row">


                    <div class="container-fluid">
                        <!-- Area chart -->
                        <div class="col-xl-12"> <!-- Menggunakan satu kolom penuh untuk tabel penjualan -->
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Laporan Penjualan</h6>
                                    <p><i>ditambah dengan metode ARIMA sebagai prediksi</i></p>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <!-- Elemen Canvas untuk Grafik Penjualan -->
                                        <canvas id="penjualanChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Area chart ARIMA -->
                        <div class="col-xl-12"> <!-- Menggunakan satu kolom penuh untuk grafik ARIMA -->
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Prediksi Penjualan dengan ARIMA</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <!-- Elemen Canvas untuk Grafik ARIMA -->
                                        <canvas id="arimaChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/simple-statistics@7.7.0/simple-statistics.min.js"></script>
                    <script>
                        // Data Penjualan
                        const penjualanData = @json($penjualanPerTanggal);
                        
                        // Menyiapkan data untuk grafik penjualan
                        const labels = Object.keys(penjualanData);
                        const data = Object.values(penjualanData);
                        
                        // Membuat grafik penjualan
                        const ctx = document.getElementById('penjualanChart').getContext('2d');
                        const myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total Penjualan',
                                    data: data,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 2,
                                    tension: 0.1 // Menambahkan kelancaran pada grafik
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                        titleColor: '#fff',
                                        bodyColor: '#fff'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Jumlah Penjualan'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Bulan'
                                        }
                                    }
                                }
                            }
                        });
                    
                        // Fungsi untuk menghitung prediksi ARIMA sederhana
                        function arima(data, p, d, q) {
                            let predictions = [];
                            let prevPrediction = data[0];
                    
                            for (let i = 0; i < data.length; i++) {
                                if (i > 0) {
                                    // Contoh sederhana: menggunakan nilai sebelumnya sebagai prediksi
                                    let prediction = prevPrediction + (data[i] - prevPrediction) * 0.5; // Adjustment factor
                                    predictions.push(prediction);
                                    prevPrediction = prediction;
                                }
                            }
                            return predictions;
                        }
                    
                        // Melakukan prediksi menggunakan model ARIMA
                        const p = 1, d = 1, q = 1; // Parameter ARIMA
                        const arimaPredictions = arima(data, p, d, q);
                    
                        // Membuat grafik untuk prediksi ARIMA
                        const arimaCtx = document.getElementById('arimaChart').getContext('2d');
                        const arimaChart = new Chart(arimaCtx, {
                            type: 'line',
                            data: {
                                labels: labels.concat(labels.slice(-1).map((_, i) => `Bulan ${parseInt(labels.length) + i + 1}`)), // Menghitung label untuk prediksi
                                datasets: [{
                                    label: 'Prediksi ARIMA',
                                    data: data.concat(arimaPredictions), // Menggabungkan data asli dan prediksi
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderWidth: 2,
                                    tension: 0.1 // Menambahkan kelancaran pada grafik
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                        titleColor: '#fff',
                                        bodyColor: '#fff'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Jumlah Penjualan'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Bulan'
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                    

                <!-- Content Row -->
                <div class="row">

                    <!-- Content Column -->
                    <div class="col-lg-6 mb-4">

                        <!-- Project Card Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Penjualan tertinggi</h6>
                            </div>
                            <div class="card-body">
                                <h4 class="small font-weight-bold">Server Migration <span
                                        class="float-right">20%</span></h4>
                                <div class="progress mb-4">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 20%"
                                        aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <h4 class="small font-weight-bold">Sales Tracking <span
                                        class="float-right">40%</span></h4>
                                <div class="progress mb-4">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                                        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                              
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Produk yang Perlu Dipesan Ulang</h6>
                            </div>
                            <div class="card-body"> 
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Obat</th>
                                                <th>Kode Obat</th>
                                                <th>Stok Sisa</th>
                                                <th>ROP</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                            
                                    </table>
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


@endsection
