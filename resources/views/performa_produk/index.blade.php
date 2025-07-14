{{-- 
    File: resources/views/performa-produk/import.blade.php (Contoh Path)
    Deskripsi: Halaman untuk mengimpor dan menganalisis data performa produk dengan UI yang telah dimodernisasi.
--}}

@extends('layouts.main')

{{-- Menambahkan style kustom dan library eksternal --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Font dan body */
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }

    /* Kustomisasi Card */
    .custom-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }
    .custom-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    /* Summary Cards */
    .summary-card {
        display: flex;
        align-items: center;
        padding: 1.5rem;
    }
    .summary-card .icon-bg {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .summary-card .icon-bg svg {
        width: 24px;
        height: 24px;
    }
    .summary-card .card-title {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .summary-card .card-text {
        font-size: 1.5rem;
        font-weight: 700;
        color: #212529;
    }
    
    /* Tombol modern */
    .btn-modern {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        transition: all 0.2s ease;
    }
    .btn-modern:hover {
        transform: translateY(-2px);
    }

    /* Kustomisasi Tabel dengan DataTables */
    .dataTables_wrapper {
        padding-top: 1rem;
    }
    .dataTables_filter input, .dataTables_length select {
        border-radius: 8px !important;
        padding: 0.5rem 1rem !important;
        border: 1px solid #dee2e6 !important;
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }

    /* Kustomisasi File Input */
    .custom-file-upload {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .custom-file-upload:hover {
        background-color: #f1f3f5;
    }
    .custom-file-upload input[type="file"] {
        display: none;
    }

    /* Loading Overlay */
    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
</style>
@endpush


@section('content')
    {{-- Indikator Loading --}}
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Header Halaman -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mt-2 mb-0 fw-bold">Impor & Analisis Data Produk</h3>
                <small class="text-muted">Unggah file CSV untuk melihat ringkasan performa produk.</small>
            </div>
            <div>
                <a href="/performa-produk/compare" class="btn btn-outline-primary btn-modern">Bandingkan Periode</a>
            </div>
        </div>

        <!-- Bagian Impor dan Instruksi -->
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Impor Data Analisis</h5>
                        <label for="csv-upload" class="custom-file-upload mt-3">
                            <input type="file" id="csv-upload" accept=".csv" />
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-cloud-arrow-up-fill text-primary mb-2" viewBox="0 0 16 16"><path d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z"/></svg>
                            <p class="fw-semibold mb-0">Klik untuk memilih file CSV</p>
                            <small id="selected-file-name" class="text-muted d-block mt-1"></small>
                        </label>
                        <button id="upload-button" class="btn btn-primary btn-modern w-100 mt-3" disabled>Unggah & Proses</button>
                        <button id="reset-button" class="btn btn-outline-danger btn-modern w-100 mt-2">Reset Semua Data</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Petunjuk Format CSV</h5>
                        <p class="text-muted small">Pastikan file CSV Anda memiliki header kolom dengan urutan yang benar untuk impor yang sukses.</p>
                        <div class="bg-light p-3 rounded border small" style="font-family: monospace;">
                            Kode Produk, Produk, Status Produk Saat Ini, Kode Variasi, Nama Variasi, Status Variasi Saat Ini, Kode Variasi 2, SKU Induk, Pengunjung Produk (Kunjungan), Halaman Produk Dilihat, Pengunjung Melihat Tanpa Membeli, Tingkat Pengunjung Melihat Tanpa Membeli, Klik Pencarian, Suka, Pengunjung Produk (Menambahkan Produk ke Keranjang), Dimasukkan ke Keranjang (Produk), Tingkat Konversi Produk Dimasukkan ke Keranjang, Total Pembeli (Pesanan Dibuat), Produk (Pesanan Dibuat), Total Penjualan (Pesanan Dibuat) (IDR), Tingkat Konversi (Pesanan yang Dibuat), Total Pembeli (Pesanan Siap Dikirim), Produk (Pesanan Siap Dikirim), Penjualan (Pesanan Siap Dikirim) (IDR), Tingkat Konversi (Pesanan Siap Dikirim), Tingkat Konversi (Pesanan Siap Dikirim dibagi Pesanan Dibuat), % Pembelian Ulang (Pesanan Siap Dikirim), Rata-rata Hari Pembelian Terulang (Pesanan Siap Dikirim)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card custom-card summary-card">
                    <div class="icon-bg" style="background-color: #e7f3ff; color: #0d6efd;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg></div>
                    <div><h6 class="card-title">Jumlah Data Produk</h6><p class="card-text" id="data-count">0</p></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card custom-card summary-card">
                    <div class="icon-bg" style="background-color: #e8f5e9; color: #198754;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM13 7h-2v6h2V7zm0 8h-2v2h2v-2z"/></svg></div>
                    <div><h6 class="card-title">Total Penjualan (Siap Kirim)</h6><p class="card-text" id="totalPenjualan">Rp 0</p></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card custom-card summary-card">
                    <div class="icon-bg" style="background-color: #fff0e4; color: #fd7e14;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"/></svg></div>
                    <div><h6 class="card-title">Total Produk (Siap Kirim)</h6><p class="card-text" id="totalProdukSiapDikirim">0</p></div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card custom-card">
                    <div class="card-body p-4"><h5 class="fw-bold">10 Produk Teratas (Berdasarkan Penjualan)</h5><div style="height: 400px;"><canvas id="salesChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body p-4"><h5 class="fw-bold">5 Produk Teratas (Distribusi)</h5><div style="height: 400px;"><canvas id="topProductsPieChart"></canvas></div></div>
                </div>
            </div>
        </div>

        <!-- Tables -->
        <div class="row g-4">
             <div class="col-lg-12">
                <div class="card custom-card">
                     <div class="card-body p-4">
                        <h5 class="fw-bold">5 Produk Kurang Laris</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="produk-kurang-laris-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Produk</th>
                                        <th>Produk</th>
                                        <th>Total Pesanan</th>
                                        <th>Total Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Tabel Performa Semua Produk</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="performa-produk-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Produk</th>
                                        <th>Produk</th>
                                        <th>Total Penjualan</th>
                                        <th>% Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@push('scripts')
{{-- Sertakan library eksternal --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
    // --- VARIABEL GLOBAL & HELPER ---
    let mySalesChart, myPieChart;
    let performaProdukTable, produkKurangLarisTable;
    const showLoading = () => $('#loading-overlay').css('display', 'flex');
    const hideLoading = () => $('#loading-overlay').hide();
    const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    const formatNumber = (angka) => new Intl.NumberFormat('id-ID').format(angka);

    /**
     * Helper function to draw a message on a canvas.
     * @param {string} canvasId - The ID of the canvas element.
     * @param {string} message - The message to display.
     */
    function drawEmptyChartMessage(canvasId, message) {
        const canvas = document.getElementById(canvasId);
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height); // Clear previous content
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = "16px 'Inter', sans-serif";
            ctx.fillStyle = '#6c757d';
            ctx.fillText(message, canvas.width / 2, canvas.height / 2);
            ctx.restore();
        }
    }


    // --- FUNGSI UPDATE UI ---

    function updateSummaryCards(data) {
        $('#data-count').text(formatNumber(data.count || 0));
        $('#totalPenjualan').text(formatRupiah(data.totalPenjualan || 0));
        $('#totalProdukSiapDikirim').text(formatNumber(data.totalProdukSiapDikirim || 0));
    }

    function createOrUpdateSalesChart(produkData = []) {
        if (mySalesChart) mySalesChart.destroy();

        if (!produkData || produkData.length === 0) {
            drawEmptyChartMessage('salesChart', 'Data penjualan tidak tersedia.');
            return;
        }

        const top10Data = produkData.slice(0, 10);
        const labels = top10Data.map(p => p.nama_produk.length > 35 ? p.nama_produk.substring(0, 35) + '...' : p.nama_produk);
        const data = top10Data.map(p => parseFloat(p.total_penjualan));

        mySalesChart = new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Penjualan',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Membuat bar chart horizontal
                scales: { x: { beginAtZero: true, ticks: { callback: value => formatNumber(value) } } },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${formatRupiah(ctx.raw)}` } }
                }
            }
        });
    }

    function createOrUpdatePieChart(produkData = []) {
        if (myPieChart) myPieChart.destroy();

        if (!produkData || produkData.length === 0) {
            drawEmptyChartMessage('topProductsPieChart', 'Data produk teratas tidak tersedia.');
            return;
        }

        const top5Data = produkData.slice(0, 5);
        const labels = top5Data.map(p => p.nama_produk);
        const data = top5Data.map(p => parseFloat(p.total_penjualan_));

        myPieChart = new Chart(document.getElementById('topProductsPieChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: ctx => `${ctx.label}: ${formatRupiah(ctx.raw)}` } }
                }
            }
        });
    }

    function updateTables(allProducts = [], leastProducts = []) {
        performaProdukTable.clear().rows.add(allProducts.map((item, index) => [
            index + 1,
            item.kode_produk,
            item.nama_produk,
            formatRupiah(item.total_penjualan),
            item.persentase_penjualan
        ])).draw();

        produkKurangLarisTable.clear().rows.add(leastProducts.map((item, index) => [
            index + 1,
            item.kode_produk,
            item.nama_produk,
            formatNumber(item.total_pesanan),
            formatRupiah(item.total_penjualan)
        ])).draw();
    }

    // --- FUNGSI AJAX ---

    function loadAllData() {
        showLoading();
        $.ajax({
            url: '/performa-produk/getcountdata',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                updateSummaryCards(response);
                createOrUpdatePieChart(response.limaProdukLaris);
                
                // Ambil data performa terpisah
                $.ajax({
                    url: '/performa-produk/get-performa-produk',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(performaResponse) {
                        createOrUpdateSalesChart(performaResponse.produk_performances);
                        updateTables(performaResponse.produk_performances, response.limaProdukKurangLaris);
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Tidak dapat memuat data performa produk.', 'error');
                        // Still hide loading even if nested ajax fails
                        hideLoading();
                    },
                    complete: function() {
                        // This will only run after the nested ajax is complete
                        hideLoading();
                    }
                });
            },
            error: function() {
                Swal.fire('Gagal!', 'Tidak dapat memuat ringkasan data.', 'error');
                // Also draw empty charts on initial load failure
                createOrUpdateSalesChart([]);
                createOrUpdatePieChart([]);
                updateTables([], []);
                hideLoading();
            }
        });
    }

    // --- INISIALISASI & EVENT LISTENERS ---
    $(document).ready(function() {
        // Inisialisasi DataTables
        const dtOptions = {
            responsive: true,
            pageLength: 5,
            lengthMenu: [ [5, 10, 25, -1], [5, 10, 25, "Semua"] ],
            language: { search: "Cari:", zeroRecords: "Data tidak ditemukan" }
        };
        performaProdukTable = $('#performa-produk-table').DataTable({
            ...dtOptions,
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: [{ extend: 'excelHtml5', text: 'Export Excel', className: 'btn btn-success btn-sm' }]
        });
        produkKurangLarisTable = $('#produk-kurang-laris-table').DataTable(dtOptions);

        // Muat data awal
        loadAllData();

        // Event listener untuk input file
        $('#csv-upload').on('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                $('#selected-file-name').text(`File terpilih: ${file.name}`);
                $('#upload-button').prop('disabled', false);
            } else {
                $('#selected-file-name').text('');
                $('#upload-button').prop('disabled', true);
            }
        });

        // Event listener untuk tombol unggah
        $('#upload-button').on('click', function() {
            const fileInput = $('#csv-upload')[0];
            if (!fileInput.files.length) {
                Swal.fire('Perhatian!', 'Silakan pilih file CSV terlebih dahulu.', 'warning');
                return;
            }
            showLoading();
            const formData = new FormData();
            formData.append('csv_file', fileInput.files[0]);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '/performa-produk/import',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire('Berhasil!', response.message || 'File berhasil diimpor.', 'success');
                    $('#csv-upload').val('');
                    $('#selected-file-name').text('');
                    $('#upload-button').prop('disabled', true);
                    loadAllData();
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan saat mengimpor.';
                    Swal.fire('Gagal!', errorMsg, 'error');
                    hideLoading();
                },
            });
        });
        
        // Event listener untuk tombol reset
        $('#reset-button').on('click', function() {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Semua data performa produk akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, reset data!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    $.ajax({
                        url: '/performa-produk/reset-data',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            loadAllData();
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Tidak dapat mereset data.', 'error');
                            hideLoading();
                        },
                    });
                }
            });
        });
    });
</script>
@endpush
