@extends('layouts.main')

@section('content')
    <div class="card shadow border-0 mx-auto">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-lg-4">
                    <h1 class="h3 fw-bold text-center mb-4">
                        Impor Data Analisis Produk
                    </h1>
                    <div class="mb-3">
                        <label for="csv-upload" class="form-label fw-semibold">
                            Pilih File CSV Anda:
                        </label>
                        <input type="file" id="csv-upload" accept=".csv" class="form-control" />
                        <div id="selected-file-name" class="form-text text-center mt-2 d-none">
                            File terpilih: <span class="fw-medium"></span>
                        </div>
                    </div>

                    <button id="upload-button" class="btn btn-primary w-100 fw-bold mb-2" disabled>
                        Unggah dan Impor Data
                    </button>
                    <button class="btn btn-danger w-100 fw-bold mb-2" type="button" onclick="resetData()">
                        Reset semua data
                    </button>

                    <div id="message-area" class="alert text-center fw-medium d-none mt-3" role="alert">
                        <!-- Pesan akan ditampilkan di sini -->
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="mt-4 small text-muted text-center">
                        <div class="fw-semibold mb-2">
                            Pastikan file CSV Anda memiliki header yang sesuai dengan urutan ini:
                        </div>
                        <div class="bg-light p-3 rounded border text-start" style="font-size: 0.85em;">
                            Kode Produk, Produk, Status Produk Saat Ini, Kode Variasi, Nama Variasi, Status Variasi Saat
                            Ini, Kode Variasi 2, SKU
                            Induk, Pengunjung Produk (Kunjungan), Halaman Produk Dilihat, Pengunjung Melihat Tanpa Membeli,
                            Tingkat
                            Pengunjung Melihat Tanpa Membeli, Klik Pencarian, Suka, Pengunjung Produk (Menambahkan Produk ke
                            Keranjang), Dimasukkan ke Keranjang (Produk), Tingkat Konversi Produk Dimasukkan ke Keranjang,
                            Total
                            Pembeli (Pesanan Dibuat), Produk (Pesanan Dibuat), Total Penjualan (Pesanan Dibuat) (IDR),
                            Tingkat
                            Konversi (Pesanan yang Dibuat), Total Pembeli (Pesanan Siap Dikirim), Produk (Pesanan Siap
                            Dikirim),
                            Penjualan (Pesanan Siap Dikirim) (IDR), Tingkat Konversi (Pesanan Siap Dikirim), Tingkat
                            Konversi
                            (Pesanan Siap Dikirim dibagi Pesanan Dibuat), % Pembelian Ulang (Pesanan Siap Dikirim),
                            Rata-rata Hari
                            Pembelian Terulang (Pesanan Siap Dikirim)
                        </div>
                    </div>
                    <div class="mt-2 text-end">
                        <a href="/performa-produk/compare">Compare data dari 2 periode ? Klik disini! </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row mb-4">
                        <div class="col-lg-4">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Jumlah Data
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="data-count">
                                        0
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <a href="/performa-produk/lists">List Data</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Total Penjualan (IDR)
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="totalPenjualan">
                                        0
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Data ini diambil dari kolom Penjualan (Pesanan Siap Dikirim) (IDR)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Total Produk Pesanan Siap Dikirim
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="totalProdukSiapDikirim">
                                        0
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Data ini diambil dari kolom Produk (Pesanan Siap Dikirim)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card shadow border-0 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                5 Produk Terlaris (Pie Chart)
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="topProductsPieChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow border-0 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                5 Produk Kurang Laris
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive text-sm">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Total Pesanan Siap Dikirim</th>
                                            <th>Total Penjualan (IDR)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="produk-kurang-laris">
                                        <!-- Data akan diisi melalui JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="mb-5" style="position: relative; height: 500px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="card-title
                                mb-0">
                            Performa Produk
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="performa-produk-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Produk</th>
                                        <th>Produk</th>
                                        <th>Total Penjualan</th>
                                        <th>Persentase Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- JSZip (untuk export excel) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- DataTables JS -->
    <script src="//cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- Buttons extension -->
    <script src="//cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        getCountData();


        function createOrUpdateChart(produkData) {
            // slice
            produkData = produkData.slice(0, 10);

            let myChart;
            // Ekstrak nama produk untuk menjadi label di sumbu X
            const labels = produkData.map(product => product.nama_produk);

            // Ekstrak data penjualan total saja
            const totalPenjualan = produkData.map(product => parseFloat(product.total_penjualan));

            const ctx = document.getElementById('salesChart').getContext('2d');

            // Hancurkan instance chart yang ada sebelum membuat yang baru
            if (myChart) {
                myChart.destroy();
            }

            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Penjualan',
                        data: totalPenjualan,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)', // Biru
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    // indexAxis: 'y', // Uncomment jika ingin grafik horizontal
                    scales: {
                        x: {
                            ticks: {
                                callback: function(value) {
                                    const label = this.getLabelForValue(value);
                                    if (label.length > 20) {
                                        return label.substring(0, 20) + '...';
                                    }
                                    return label;
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000) + 'jt';
                                    if (value >= 1000) return (value / 1000) + 'k';
                                    return value;
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: '10 Top Produk Berdasarkan Total Penjualan'
                        },
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    return labels[tooltipItems[0].dataIndex];
                                }
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }


        function showMessage(msg, isSuccess) {
            const messageArea = document.getElementById('message-area');
            messageArea.textContent = msg;
            messageArea.classList.remove('hidden');
            messageArea.classList.remove('d-none');
            if (isSuccess) {
                messageArea.classList.remove('bg-danger', 'text-white');
                messageArea.classList.add('bg-success', 'text-white');
            } else {
                messageArea.classList.remove('bg-success', 'text-white');
                messageArea.classList.add('bg-danger', 'text-white');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('csv-upload');
            const uploadButton = document.getElementById('upload-button');
            const messageArea = document.getElementById('message-area');
            const selectedFileNameDisplay = document.getElementById('selected-file-name');
            let selectedFile = null;

            // Event listener untuk perubahan file input
            fileInput.addEventListener('change', function(event) {
                selectedFile = event.target.files[0];
                if (selectedFile) {
                    selectedFileNameDisplay.querySelector('span').textContent = selectedFile.name;
                    selectedFileNameDisplay.classList.remove('hidden');
                    uploadButton.disabled = false; // Aktifkan tombol unggah
                    messageArea.classList.add('hidden'); // Sembunyikan pesan sebelumnya
                } else {
                    selectedFileNameDisplay.classList.add('hidden');
                    uploadButton.disabled = true; // Nonaktifkan tombol unggah
                }
            });

            // Event listener untuk tombol unggah
            uploadButton.addEventListener('click', async function() {
                if (!selectedFile) {
                    showMessage('Silakan pilih file CSV terlebih dahulu.', false);
                    return;
                }

                uploadButton.disabled = true;
                uploadButton.textContent = 'Mengunggah...';
                showMessage('Mengunggah dan memproses file...', true);

                const formData = new FormData();
                formData.append('csv_file',
                    selectedFile); // Pastikan nama 'csv_file' sesuai dengan validator Laravel

                try {
                    // Ambil CSRF token dari meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');
                    formData.append('_token', csrfToken);

                    const response = await fetch('/performa-produk/import', {
                        method: 'POST',
                        body: formData,
                        // Headers Content-Type tidak perlu diatur secara manual untuk FormData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        getCountData()
                        showMessage(data.message || 'File CSV berhasil diimpor!', true);
                        fileInput.value = ''; // Reset input file
                        selectedFile = null;
                        selectedFileNameDisplay.classList.add('hidden');
                    } else {
                        showMessage(data.message || 'Terjadi kesalahan saat mengimpor file.', false);
                    }
                } catch (error) {
                    console.error('Error uploading CSV:', error);
                    showMessage('Terjadi kesalahan jaringan atau server: ' + error.message, false);
                } finally {
                    uploadButton.disabled = false;
                    uploadButton.textContent = 'Unggah dan Impor Data';
                }
            });
        });

        function getCountData() {
            $.ajax({
                url: '/performa-produk/getcountdata',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    $('#data-count').text(response.count);
                    $('#totalProdukSiapDikirim').text(
                        new Intl.NumberFormat('id-ID', {
                            maximumFractionDigits: 0
                        }).format(response.totalProdukSiapDikirim)
                    );
                    // Format totalPenjualan ke format Rupiah
                    $('#totalPenjualan').text(
                        new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(response.totalPenjualan)
                    );
                    
                    loadTopProductsPieChart(response.limaProdukLaris)
                    const kurangLarisRows = response.limaProdukKurangLaris.map((item, idx) => {
                        return `<tr>
                            <td>${idx + 1}</td>
                            <td>${item.kode_produk}</td>
                            <td>${item.nama_produk}</td>
                            <td>${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(item.total_pesanan)}</td>
                            <td>${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.total_penjualan)}</td>
                        </tr>`;
                    }).join('');
                    $('#produk-kurang-laris').html(kurangLarisRows);

                    getPerformaProduk()

                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengambil jumlah data:', error);
                }
            });
        }

        function resetData() {
            if (!confirm('Apakah Anda yakin ingin mereset semua data? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
            $.ajax({
                url: '/performa-produk/reset-data',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    getCountData()
                    showMessage(response.message || 'Database berhasil direset!', true);
                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengambil jumlah data:', error);
                    alert('Terjadi kesalahan saat mereset data.');
                }
            });
        }

        function loadTopProductsPieChart(response) {
            // Urutkan data dari terbesar ke terkecil berdasarkan total_penjualan_
            const sorted = response.slice().sort((a, b) => b.total_penjualan_ - a.total_penjualan_);
            // Tambahkan penomoran pada label dan total_penjualan_ dalam ()
            const labels = sorted.map((item, idx) =>
                `${idx + 1}. ${item.nama_produk} (Rp${item.total_penjualan_.toLocaleString('id-ID')})`
            );
            const data = sorted.map(item => item.total_penjualan_);

            const ctx = document.getElementById('topProductsPieChart').getContext('2d');
            if (window.topProductsPieChartInstance) {
                window.topProductsPieChartInstance.destroy();
            }
            window.topProductsPieChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                        ],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    return label + ': ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }

        function getPerformaProduk() {
            $.ajax({
                url: '/performa-produk/get-performa-produk',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    createOrUpdateChart(response.produk_performances)
                    var tableBody = '';

                    $('#performa-produk-table tbody').empty(); // Kosongkan tabel sebelum mengisi data baru
                    response.produk_performances.forEach((item, index) => {
                        tableBody += `<tr>
                            <td>${index + 1}</td>
                            <td>${item.kode_produk}</td>
                            <td>${item.nama_produk}</td>
                            <td>${Number(item.total_penjualan).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })}</td>
                            <td>${item.persentase_penjualan}</td>
                        </tr>`;
                    });
                    $('#performa-produk-table tbody').html(tableBody);
                    // Inisialisasi DataTable dengan tombol export Excel
                    if ($.fn.DataTable.isDataTable('#performa-produk-table')) {
                        $('#performa-produk-table').DataTable().destroy();
                    }
                    $('#performa-produk-table').DataTable({
                        responsive: true,
                        columnDefs: [{
                                orderable: false,
                                targets: 0
                            } // Nonaktifkan pengurutan pada kolom No
                        ],
                        dom: 'Bfrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            title: 'Performa Produk'
                        }]
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengambil performa produk:', error);
                }
            });
        }
    </script>
@endpush
