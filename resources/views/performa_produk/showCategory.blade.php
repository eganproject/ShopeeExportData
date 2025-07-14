@extends('layouts.main')

{{-- Menambahkan style kustom dan library eksternal --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Font dan body */
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            /* Font modern yang mudah dibaca */
        }

        /* Kustomisasi Card */
        .custom-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
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
            padding: 1rem 0;
        }

        .dataTables_filter input {
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

        /* Loading Overlay */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            display: none;
            /* Awalnya disembunyikan */
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
@endpush


@section('content')
    {{-- Indikator Loading --}}
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Header Halaman -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="/performa-produk/kategori" class="btn btn-sm btn-outline-secondary btn-modern">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                    </svg>
                    Kembali
                </a>
                <h3 class="mt-2 mb-0 fw-bold">{{ $kategori->nama_kategori }}</h3>
                <small class="text-muted">Analisis performa produk untuk kategori ini.</small>
            </div>
        </div>

        <!-- Bagian Form dan Daftar SKU -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <h5 class="fw-bold">Tambah Produk</h5>
                        <p class="text-muted small">Masukkan SKU produk untuk ditambahkan ke dalam analisis.</p>

                        <form id="productForm">
                            @csrf
                            <input type="hidden" name="kategori_id" id="kategori_id" value="{{ $kategori->id }}">
                            <div class="mb-3">
                                <label for="product_code" class="form-label fw-semibold">Kode Produk (SKU)</label>
                                <input type="text" class="form-control" id="product_code" name="product_code"
                                    placeholder="Contoh: BAJU-001-XL" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-modern w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-plus-circle-fill me-1" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z" />
                                </svg>
                                Tambah
                            </button>
                        </form>

                        <hr class="my-4">

                        <h6 class="fw-bold">Import dari CSV</h6>
                        <p class="text-muted small">Atau import beberapa SKU sekaligus. Sistem hanya akan membaca data dari
                            **kolom A** file CSV Anda.</p>
                        <form id="importCsvForm">
                            <div class="mb-3">
                                <label for="csv_file" class="form-label fw-semibold">File CSV</label>
                                <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv"
                                    required>
                            </div>
                            <button type="button" id="importCsvBtn" class="btn btn-outline-primary btn-modern w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-file-earmark-arrow-up-fill me-1" viewBox="0 0 16 16">
                                    <path
                                        d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM6.354 9.854a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 8.707V12.5a.5.5 0 0 1-1 0V8.707L6.354 9.854z" />
                                </svg>
                                Import CSV
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <h5 class="fw-bold">Daftar Kode Produk</h5>
                        <div class="table-responsive">
                            {{-- button untuk trigger delete semua data --}}
                            <button onclick="deleteProductKategori({{$kategori->id}})" class="btn btn-danger btn-sm btn-modern">
                                <i class="bi bi-trash me-1"></i>Hapus Semua
                            </button>
                            <table id="productCodesTable" class="table table-hover align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card custom-card summary-card">
                    <div class="icon-bg" style="background-color: #e7f3ff; color: #0d6efd;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-12h2v4h-2zm0 6h2v2h-2z" />
                        </svg>
                    </div>
                    <div>
                        <h6 class="card-title">Total Penjualan Periode 1</h6>
                        <p class="card-text" id="totalPenjualanPeriode1">0</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card custom-card summary-card">
                    <div class="icon-bg" id="selisihIconBg" style="background-color: #e8f5e9; color: #198754;">
                        <svg id="selisihIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                        </svg>
                    </div>
                    <div>
                        <h6 class="card-title">Perubahan Penjualan</h6>
                        <p class="card-text" id="selisihPenjualan">0</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card custom-card summary-card">
                    <div class="icon-bg" style="background-color: #fff0e4; color: #fd7e14;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-12h2v4h-2zm0 6h2v2h-2z" />
                        </svg>
                    </div>
                    <div>
                        <h6 class="card-title">Total Penjualan Periode 2</h6>
                        <p class="card-text" id="totalPenjualanPeriode2">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card custom-card mb-4">
            <div class="card-body">
                <div style="height: 450px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabel Perbandingan Utama -->
        <div class="card custom-card">
            <div class="card-body">
                <h5 class="fw-bold">Detail Perbandingan Performa Produk</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="compareTable" style="width:100%;">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>SKU</th>
                                <th style="min-width: 250px;">Produk</th>
                                <th>Pesanan 1</th>
                                <th>Pesanan 2</th>
                                <th>Perubahan Pesanan (%)</th>
                                <th>Penjualan 1</th>
                                <th>Penjualan 2</th>
                                <th>Perubahan Penjualan (%)</th>
                                <th>AOV</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <!-- Data diisi via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Sertakan library eksternal --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- DataTables dan Ekstensi Tombol (untuk Export Excel) --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">


    <script>
        // --- VARIABEL GLOBAL ---
        let myChart;
        let productCodesTableInstance;
        let compareTableInstance;
        const kategoriId = $('#kategori_id').val();

        // --- FUNGSI HELPER ---
        const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
        const formatNumber = (angka) => new Intl.NumberFormat('id-ID').format(angka);
        const showLoading = () => $('#loading-overlay').css('display', 'flex');
        const hideLoading = () => $('#loading-overlay').hide();

        // --- FUNGSI UTAMA UNTUK UPDATE UI ---

        /**
         * Memperbarui 3 kartu ringkasan di bagian atas.
         */
        function updateSummaryCards(produkData) {
            let total1 = produkData.reduce((sum, item) => sum + (parseFloat(item.penjualan_periode_1) || 0), 0);
            let total2 = produkData.reduce((sum, item) => sum + (parseFloat(item.penjualan_periode_2) || 0), 0);
            let selisih = total2 - total1;
            let persentase = total1 !== 0 ? (selisih / total1) * 100 : (total2 > 0 ? 100 : 0);

            $('#totalPenjualanPeriode1').text(formatRupiah(total1));
            $('#totalPenjualanPeriode2').text(formatRupiah(total2));
            $('#selisihPenjualan').text(`${selisih > 0 ? '+' : ''}${formatRupiah(selisih)} (${persentase.toFixed(2)}%)`);

            // Ubah warna dan ikon berdasarkan kenaikan/penurunan
            const iconBg = $('#selisihIconBg');
            const icon = $('#selisihIcon');
            if (selisih >= 0) {
                iconBg.css({
                    'background-color': '#e8f5e9',
                    'color': '#198754'
                });
                icon.html(
                    '<path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>'); // Panah ke atas
            } else {
                iconBg.css({
                    'background-color': '#fde7e9',
                    'color': '#dc3545'
                });
                icon.html(
                    '<path d="M16 18l2.29-2.29-4.88-4.88-4 4L2 7.41 3.41 6l6 6 4-4 6.3 6.29L22 12v6z"/>'); // Panah ke bawah
            }
        }

        /**
         * Membuat atau memperbarui chart penjualan.
         */
        function createOrUpdateChart(produkData) {
            const top10Data = produkData.slice(0, 10);
            const labels = top10Data.map(p => p.produk.length > 30 ? p.produk.substring(0, 30) + '...' : p.produk);
            const data1 = top10Data.map(p => parseFloat(p.penjualan_periode_1));
            const data2 = top10Data.map(p => parseFloat(p.penjualan_periode_2));

            const ctx = document.getElementById('salesChart').getContext('2d');
            if (myChart) {
                myChart.destroy();
            }
            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Penjualan Periode 1',
                            data: data1,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            borderRadius: 5
                        },
                        {
                            label: 'Penjualan Periode 2',
                            data: data2,
                            backgroundColor: 'rgba(255, 159, 64, 0.7)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 1,
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => formatNumber(value)
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 10 Produk Berdasarkan Total Penjualan',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ${formatRupiah(context.raw)}`,
                                title: (tooltipItems) => top10Data[tooltipItems[0].dataIndex]
                                    .produk // Nama lengkap di tooltip
                            }
                        }
                    }
                }
            });
        }

        /**
         * Memuat ulang data ke dalam tabel #productCodesTable menggunakan API DataTables.
         */
        function updateProductCodesTable(data) {
            productCodesTableInstance.clear();
            const tableData = data.map((item, index) => [
                index + 1,
                `<span class="${item.status == 'tidak_ada' ? 'text-danger fw-bold' : ''}">${item.kode_produk}</span>`,
                item.nama_produk,
                `<button class="btn btn-danger btn-sm" onclick="deleteProductCode('${item.id}')">Hapus</button>`
            ]);
            productCodesTableInstance.rows.add(tableData).draw();
        }

        /**
         * Memuat ulang data ke dalam tabel #compareTable menggunakan API DataTables.
         */
        function updateCompareTable(data) {
            compareTableInstance.clear();
            const tableData = data.map((item, index) => {
                const perubahanPesanan = parseFloat(item.persentase_perubahan_pesanan) || 0;
                const perubahanPenjualan = parseFloat(item.persentase_perubahan_penjualan) || 0;

                const pesananClass = perubahanPesanan > 0 ? 'text-success' : (perubahanPesanan < 0 ? 'text-danger' :
                    '');
                const penjualanClass = perubahanPenjualan > 0 ? 'text-success' : (perubahanPenjualan < 0 ?
                    'text-danger' : '');

                return [
                    index + 1,
                    item.sku || '',
                    item.produk || '',
                    formatNumber(item.pesanan_siap_dikirim_1) || 0,
                    formatNumber(item.pesanan_siap_dikirim_2) || 0,
                    `<span class="${pesananClass}">${perubahanPesanan.toFixed(2)}%</span>`,
                    formatRupiah(item.penjualan_periode_1) || 'Rp 0',
                    formatRupiah(item.penjualan_periode_2) || 'Rp 0',
                    `<span class="${penjualanClass}">${perubahanPenjualan.toFixed(2)}%</span>`,
                    item.aov || '0'
                ];
            });
            compareTableInstance.rows.add(tableData).draw();
        }

        // --- FUNGSI AJAX ---

        /**
         * Fungsi utama untuk memuat semua data dari server.
         */
        function loadData() {
            showLoading();
            $.ajax({
                url: `/performa-produk/kategori/get-product-codes/${kategoriId}`,
                method: "GET",
                success: function(response) {
                    updateSummaryCards(response.produkData || []);
                    createOrUpdateChart(response.produkData || []);
                    updateProductCodesTable(response.data || []);
                    updateCompareTable(response.produkData || []);
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', 'Tidak dapat memuat data dari server.', 'error');
                },
                complete: function() {
                    hideLoading();
                }
            });
        }

        /**
         * Fungsi untuk menghapus kode produk.
         */
        function deleteProductCode(id) {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Kode produk ini akan dihapus dari analisis!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    $.ajax({
                        url: `/performa-produk/kategori/delete-product-code/${id}`,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        success: function() {
                            Swal.fire('Berhasil!', 'Kode produk telah dihapus.', 'success');
                            loadData();
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Tidak dapat menghapus kode produk.', 'error');
                            hideLoading();
                        }
                    });
                }
            });
        }

        

        function deleteProductKategori(id){
               Swal.fire({
                title: 'Anda yakin?',
                text: "Semua kode produk dalam kategori ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    $.ajax({
                        url: `/performa-produk/kategori/detail/delete/${id}`,
                        method: "POST", // Menggunakan POST karena ini adalah operasi penghapusan massal
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'), // Mengambil CSRF token dari meta tag
                            kategori_id: id
                        },
                        success: function(response) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            loadData(); // Muat ulang data setelah berhasil menghapus
                        },
                        error: function(xhr) {
                            const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan saat menghapus semua kode produk.';
                            Swal.fire('Gagal!', errorMsg, 'error');
                            hideLoading();
                        }
                    });
                }
            });
        }

        // --- INISIALISASI & EVENT LISTENERS ---
        $(document).ready(function() {
            // Inisialisasi DataTables saat dokumen siap
            productCodesTableInstance = $('#productCodesTable').DataTable({
                pageLength: 5,
                lengthChange: false,
                searching: false,
                info: false
            });
            compareTableInstance = $('#compareTable').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
                    "<'row'<'col-sm-12'B>>",
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    className: 'btn btn-success btn-sm'
                }]
            });

            // Panggil fungsi utama saat halaman pertama kali dimuat
            loadData();

            // Event listener untuk form submit
            $('#productForm').on('submit', function(e) {
                e.preventDefault();
                showLoading();
                $.ajax({
                    url: "{{ route('performa_produk.createProductCode') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Produk berhasil ditambahkan untuk dianalisis.',
                            'success');
                        $('#productForm')[0].reset();
                        loadData();
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Terjadi kesalahan.';
                        Swal.fire('Gagal!', errorMsg, 'error');
                        hideLoading();
                    }
                });
            });


            const importBtn = document.getElementById('importCsvBtn');
            const csvFileInput = document.getElementById('csv_file');
            const kategoriId = document.getElementById('kategori_id').value;
            const csrfToken = document.querySelector('input[name="_token"]').value;

            // Tambahkan event listener ke tombol import
            importBtn.addEventListener('click', async function() {
                // 1. Validasi Sederhana: Pastikan file sudah dipilih
                if (csvFileInput.files.length === 0) {
                    alert('Silakan pilih file CSV terlebih dahulu.');
                    return;
                }

                const file = csvFileInput.files[0];

                // 2. Buat objek FormData untuk mengirim file
                const formData = new FormData();
                formData.append('csv_file', file);
                formData.append('kategori_id', kategoriId);

                // Nonaktifkan tombol untuk mencegah klik ganda
                importBtn.disabled = true;
                importBtn.innerHTML = 'Mengimpor...';

                try {
                    // 3. Kirim data ke Controller Laravel menggunakan Fetch API
                    const response = await fetch(
                    '/performa-produk/kategori/detail/import-csv', { // Ganti URL ini jika perlu
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken, // Header CSRF penting untuk keamanan
                            'Accept': 'application/json', // Mengharapkan respons JSON
                        },
                        body: formData,
                    });

                    const result = await response.json();

                    if (response.ok) {
                        // Berhasil!
                        alert(result.message);
                        // Opsional: Muat ulang halaman atau perbarui daftar produk
                        window.location.reload();
                    } else {
                        // Gagal, tampilkan pesan error dari server
                        alert('Error: ' + (result.message || 'Terjadi kesalahan pada server.'));
                    }

                } catch (error) {
                    // Tangani error jaringan atau lainnya
                    console.error('Fetch error:', error);
                    alert('Tidak dapat terhubung ke server. Silakan coba lagi.');
                } finally {
                    // Aktifkan kembali tombol setelah proses selesai
                    importBtn.disabled = false;
                    importBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-up-fill me-1" viewBox="0 0 16 16">
                    <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM6.354 9.854a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 8.707V12.5a.5.5 0 0 1-1 0V8.707L6.354 9.854z"/>
                </svg>
                Import CSV`;
                }
            });
        });

    </script>
@endpush
