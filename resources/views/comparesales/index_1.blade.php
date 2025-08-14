@extends('layouts.main')

{{-- Menambahkan style kustom dan library eksternal --}}
@push('styles')
    {{-- Font Awesome & Google Fonts --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* =================================================================
        * 1. PENGATURAN DASAR & TIPOGRAFI
        * ================================================================= */
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --danger-color: #ef4444;
            --light-gray: #f9fafb;
            --dark-text: #1f2937;
            --light-text: #6b7280;
        }

        body {
            background: linear-gradient(180deg, var(--light-gray) 0%, #ffffff 100%);
            font-family: 'Poppins', sans-serif;
            color: var(--light-text);
        }

        .fw-semibold { font-weight: 600 !important; }

        /* =================================================================
        * 2. KUSTOMISASI KARTU (CARD)
        * ================================================================= */
        .custom-card {
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
            background-color: #ffffff;
            overflow: hidden;
        }

        .card-header.custom-header {
            background-color: transparent;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-header.custom-header .card-title-icon {
            color: var(--primary-color);
        }

        .card-header.custom-header .card-title {
            margin-bottom: 0;
            font-weight: 600;
            color: var(--dark-text);
        }

        /* =================================================================
        * 3. KARTU METRIK KHUSUS
        * ================================================================= */
        .metric-card {
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            color: white;
            border-radius: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .metric-card .metric-icon {
            position: absolute;
            right: -1rem;
            bottom: -1rem;
            font-size: 5rem;
            opacity: 0.2;
            transform: rotate(-15deg);
            transition: transform 0.4s ease;
        }
        .metric-card:hover .metric-icon {
            transform: rotate(0deg) scale(1.1);
        }
        .metric-card h6 {
            font-weight: 400;
            opacity: 0.9;
        }
        .metric-card .metric-value {
            font-size: 1.75rem;
            font-weight: 700;
        }
        .metric-card .metric-prev-value {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        .metric-card .reset-buttons {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transform: translateX(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .metric-card:hover .reset-buttons {
            opacity: 1;
            transform: translateX(0);
        }
        .btn-reset {
            background-color: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            padding: 0;
            font-size: 0.8rem;
        }
        .btn-reset:hover {
            background-color: rgba(255,255,255,0.4);
        }
        .bg-gradient-primary { background: linear-gradient(45deg, #4f46e5, #818cf8); }
        .bg-gradient-success { background: linear-gradient(45deg, #10b981, #6ee7b7); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f59e0b, #fbbf24); }
        .bg-gradient-info { background: linear-gradient(45deg, #3b82f6, #93c5fd); }

        /* =================================================================
        * 4. PANEL KONTROL & FORMULIR
        * ================================================================= */
        .form-label { font-weight: 500; color: var(--dark-text); }
        .form-control, .form-select {
            border-radius: 0.75rem;
            border: 1px solid #d1d5db;
            padding: 0.75rem 1rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            background-color: var(--light-gray);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .2);
            background-color: #fff;
        }
        .custom-file-upload {
            border: 2px dashed #a5b4fc;
            border-radius: 1rem;
            padding: 2.5rem;
            background-color: var(--light-gray);
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .custom-file-upload:hover {
            background-color: #eef2ff;
            border-color: var(--primary-color);
        }
        #csv-upload { display: none; }

        /* =================================================================
        * 5. TOMBOL (BUTTON)
        * ================================================================= */
        .btn-modern {
            border-radius: 50px;
            font-weight: 600;
            padding: 0.8rem 1.75rem;
            transition: all 0.3s ease;
            border: none;
        }
        .btn-modern.btn-primary {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.2);
        }
        .btn-modern.btn-primary:hover {
            transform: translateY(-3px);
            background-color: #4338ca;
            box-shadow: 0 7px 20px rgba(79, 70, 229, 0.3);
        }
        .btn-modern.btn-outline-danger {
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
        }
        .btn-modern.btn-outline-danger:hover {
            background-color: var(--danger-color);
            color: white;
            transform: translateY(-3px);
        }
        .btn-modern.btn-outline-warning {
            border: 2px solid var(--warning-color);
            color: var(--warning-color);
        }
        .btn-modern.btn-outline-warning:hover {
            background-color: var(--warning-color);
            color: white;
            transform: translateY(-3px);
        }

        /* =================================================================
        * 6. INDIKATOR & ANIMASI
        * ================================================================= */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out forwards; }

        /* =================================================================
        * 7. KUSTOMISASI TAB & CHART
        * ================================================================= */
        .nav-tabs { border-bottom: none; }
        .nav-tabs .nav-link {
            border: none;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            color: var(--light-text);
            transition: all 0.3s ease;
            border-radius: 0.75rem;
            margin-right: 0.5rem;
        }
        .nav-tabs .nav-link.active, .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            background-color: #eef2ff;
        }
        .chart-container {
            position: relative;
            height: 280px; /* Memberi tinggi yang konsisten untuk chart */
        }
    </style>
@endpush

@section('content')
    {{-- Indikator Loading --}}
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status" style="width: 3.5rem; height: 3.5rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-fluid">
        <!-- HEADER -->
        <div class="text-center my-5 fade-in">
            <h1 class="fw-bold display-5 text-dark">Import & Analisis Penjualan</h1>
            <p class="fs-5 text-muted">Unggah data penjualan untuk wawasan secara visual.</p>
        </div>

        <div class="row g-4">
            <!-- KOLOM UTAMA (KIRI) -->
            <div class="col-xl-8">
                <!-- KEY METRICS ROW -->
                <div class="row g-4 mb-4">
                    <!-- Periode 1 -->
                    <div class="col-md-6 col-xl-3 fade-in" style="animation-delay: 0.1s;">
                        <div class="metric-card bg-gradient-primary h-100">
                            <h6>Total Periode 1</h6>
                            <p class="metric-value mb-1" id="totalPeriode1">Rp 0</p>
                            <p class="metric-prev-value" id="prev_totalPeriode1">Sebelumnya: Rp 0</p>
                            <i class="fas fa-chart-line metric-icon"></i>
                            <div class="reset-buttons">
                                <button type="button" onclick="resetDataPeriode('sales', 'current')" class="btn-reset" title="Reset Periode Ini"><i class="fas fa-sync-alt"></i></button>
                                <button type="button" onclick="resetDataPeriode('sales', 'previous')" class="btn-reset" title="Reset Periode Lalu"><i class="fas fa-history"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- Periode 2 -->
                    <div class="col-md-6 col-xl-3 fade-in" style="animation-delay: 0.2s;">
                        <div class="metric-card bg-gradient-success h-100">
                            <h6>Total Periode 2</h6>
                            <p class="metric-value mb-1" id="totalPeriode2">Rp 0</p>
                            <p class="metric-prev-value" id="prev_totalPeriode2">Sebelumnya: Rp 0</p>
                            <i class="fas fa-chart-bar metric-icon"></i>
                            <div class="reset-buttons">
                                <button type="button" onclick="resetDataPeriode('sales_twos', 'current')" class="btn-reset" title="Reset Periode Ini"><i class="fas fa-sync-alt"></i></button>
                                <button type="button" onclick="resetDataPeriode('sales_twos', 'previous')" class="btn-reset" title="Reset Periode Lalu"><i class="fas fa-history"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- Periode 3 -->
                    <div class="col-md-6 col-xl-3 fade-in" style="animation-delay: 0.3s;">
                        <div class="metric-card bg-gradient-warning h-100">
                            <h6>Total Periode 3</h6>
                            <p class="metric-value mb-1" id="totalPeriode3">Rp 0</p>
                            <p class="metric-prev-value" id="prev_totalPeriode3">Sebelumnya: Rp 0</p>
                            <i class="fas fa-chart-pie metric-icon"></i>
                            <div class="reset-buttons">
                                <button type="button" onclick="resetDataPeriode('sales_threes', 'current')" class="btn-reset" title="Reset Periode Ini"><i class="fas fa-sync-alt"></i></button>
                                <button type="button" onclick="resetDataPeriode('sales_threes', 'previous')" class="btn-reset" title="Reset Periode Lalu"><i class="fas fa-history"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- Periode 4 -->
                    <div class="col-md-6 col-xl-3 fade-in" style="animation-delay: 0.4s;">
                        <div class="metric-card bg-gradient-info h-100">
                            <h6>Total Periode 4</h6>
                            <p class="metric-value mb-1" id="totalPeriode4">Rp 0</p>
                            <p class="metric-prev-value" id="prev_totalPeriode4">Sebelumnya: Rp 0</p>
                            <i class="fas fa-signal metric-icon"></i>
                            <div class="reset-buttons">
                                <button type="button" onclick="resetDataPeriode('sales_fours', 'current')" class="btn-reset" title="Reset Periode Ini"><i class="fas fa-sync-alt"></i></button>
                                <button type="button" onclick="resetDataPeriode('sales_fours', 'previous')" class="btn-reset" title="Reset Periode Lalu"><i class="fas fa-history"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABS UNTUK CHART LAINNYA -->
                <div class="custom-card fade-in" style="animation-delay: 0.5s;">
                    <div class="card-header custom-header">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pie-tab" data-bs-toggle="tab" data-bs-target="#pie-charts" type="button" role="tab" aria-controls="pie-charts" aria-selected="true">
                                    <i class="fas fa-chart-pie me-2"></i>Distribusi Penjualan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="bar-tab" data-bs-toggle="tab" data-bs-target="#bar-charts" type="button" role="tab" aria-controls="bar-charts" aria-selected="false">
                                    <i class="fas fa-chart-bar me-2"></i>Produk Teratas
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content" id="myTabContent">
                            <!-- Konten Pie Charts -->
                            <div class="tab-pane fade show active" id="pie-charts" role="tabpanel" aria-labelledby="pie-tab">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="fw-semibold text-center text-dark mb-3">Distribusi Periode 1</h6>
                                                <div class="chart-container flex-grow-1">
                                                    <canvas id="piePeriodOne"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="fw-semibold text-center text-dark mb-3">Distribusi Periode 2</h6>
                                                <div class="chart-container flex-grow-1">
                                                    <canvas id="piePeriodTwo"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="fw-semibold text-center text-dark mb-3">Distribusi Periode 3</h6>
                                                <div class="chart-container flex-grow-1">
                                                    <canvas id="piePeriodThree"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="fw-semibold text-center text-dark mb-3">Distribusi Periode 4</h6>
                                                <div class="chart-container flex-grow-1">
                                                    <canvas id="piePeriodFour"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Konten Bar Charts -->
                            <div class="tab-pane fade" id="bar-charts" role="tabpanel" aria-labelledby="bar-tab">
                                 <div class="row g-4">
                                    <div class="col-lg-6"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><h6 class="fw-semibold text-center text-dark">10 Teratas Periode 1</h6><canvas id="top10SalesChartP1"></canvas></div></div></div>
                                    <div class="col-lg-6"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><h6 class="fw-semibold text-center text-dark">10 Teratas Periode 2</h6><canvas id="top10SalesChartP2"></canvas></div></div></div>
                                    <div class="col-lg-6"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><h6 class="fw-semibold text-center text-dark">10 Teratas Periode 3</h6><canvas id="top10SalesChartP3"></canvas></div></div></div>
                                    <div class="col-lg-6"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><h6 class="fw-semibold text-center text-dark">10 Teratas Periode 4</h6><canvas id="top10SalesChartP4"></canvas></div></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KONTROL (KANAN) -->
            <div class="col-xl-4">
                <div class="custom-card fade-in" style="animation-delay: 0.6s;">
                     <div class="card-header custom-header">
                        <i class="fas fa-cogs card-title-icon fs-5"></i>
                        <h5 class="card-title">Panel Kontrol</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="importForm" action="/performa-produk/compare-sales/import" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="platform" class="form-label">Platform</label>
                                <select class="form-select" id="platform" name="platform" required>
                                    <option value="" disabled selected>-- Pilih Platform --</option>
                                    <option value="Shopee" {{ old('platform') == 'Shopee' ? 'selected' : '' }}>Shopee</option>
                                    <option value="Tiktok" {{ old('platform') == 'Tiktok' ? 'selected' : '' }}>Tiktok</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="shop_id" class="form-label">Toko</label>
                                <select class="form-select" id="shop_id" name="shop_id" required>
                                    <option value="" disabled selected>-- Pilih Toko --</option>
                                    @foreach ($shop as $item)
                                        <option value="{{ $item->id }}" {{ old('shop_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label for="periode_ke" class="form-label">Periode Ke</label>
                                    <select class="form-select" id="periode_ke" name="periode_ke" required>
                                        <option value="" disabled selected>-- Pilih --</option>
                                        <option value="1" {{ old('periode_ke') == '1' ? 'selected' : '' }}>Periode 1</option>
                                        <option value="2" {{ old('periode_ke') == '2' ? 'selected' : '' }}>Periode 2</option>
                                        <option value="3" {{ old('periode_ke') == '3' ? 'selected' : '' }}>Periode 3</option>
                                        <option value="4" {{ old('periode_ke') == '4' ? 'selected' : '' }}>Periode 4</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="month_status" class="form-label">Status Periode</label>
                                    <select class="form-select" id="month_status" name="month_status" required>
                                        <option value="current">Saat Ini</option>
                                        <option value="previous">Sebelumnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="csv-upload" class="custom-file-upload w-100" style="cursor: pointer;">
                                    <input type="file" id="csv-upload" name="file[]" accept=".csv" required multiple />
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                    <p class="fw-semibold mb-0 text-dark">Klik untuk Unggah File CSV</p>
                                    <small class="text-muted">(Bisa pilih lebih dari satu)</small>
                                    <small id="selected-file-name" class="text-primary d-block mt-2 fw-bold"></small>
                                </label>
                            </div>

                            <button type="button" id="upload-button" class="btn btn-primary btn-modern w-100" disabled>
                                <i class="fas fa-upload"></i> Unggah & Proses
                            </button>
                        </form>
                        
                        <hr class="my-4">

                        <h6 class="text-center text-muted fw-semibold mb-3">Aksi & Navigasi</h6>
                        <div class="d-grid gap-2">
                             <a href="/performa-produk/compare-sales/kategori" class="btn btn-light btn-modern w-100 border">
                                <i class="fas fa-sitemap text-primary"></i> Lihat Per Kategori
                            </a>
                            <a href="/performa-produk/compare-sales/twoperiod" class="btn btn-light btn-modern w-100 border">
                                <i class="fas fa-balance-scale text-success"></i> Bandingkan 2 Periode
                            </a>
                            <button type="button" id="shuffle-button" class="btn btn-outline-warning btn-modern w-100">
                                <i class="fas fa-solid fa-shuffle"></i> Pindah Periode.
                            </button>
                            <button type="button" id="reset-button" class="btn btn-outline-danger btn-modern w-100">
                                <i class="fas fa-sync-alt"></i> Reset Semua Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Menambahkan plugin datalabels untuk Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script>
        // Fungsi global untuk format Rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        // Fungsi untuk mereset data spesifik
        function resetDataPeriode(periode, month_status) {
            // Mapping nama periode untuk ditampilkan di SweetAlert
            const periodeMap = {
                'sales': '1',
                'sales_twos': '2',
                'sales_threes': '3',
                'sales_fours': '4'
            };
            const periodeDisplay = periodeMap[periode] || periode;
            const statusDisplay = month_status === 'current' ? 'Saat Ini' : 'Sebelumnya';

            Swal.fire({
                title: 'Konfirmasi Reset',
                html: `Anda yakin ingin mereset data untuk <strong>Periode ${periodeDisplay}</strong> status <strong>${statusDisplay}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loading-overlay').css('display', 'flex');
                    $.ajax({
                        url: '/performa-produk/compare-sales/reset',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            periode: periode,
                            month_status: month_status
                        },
                        success: function(response) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            setTimeout(() => window.location.reload(), 1500);
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat mereset data.', 'error');
                            $('#loading-overlay').css('display', 'none');
                        }
                    });
                }
            });
        }

        $(function() {
            // Mendaftarkan plugin datalabels secara global
            Chart.register(ChartDataLabels);

            // =================================================================
            // MANAJEMEN FORMULIR & UNGGAH
            // =================================================================
            function validateForm() {
                const files = $('#csv-upload').prop('files');
                const fileSelected = files && files.length > 0;
                const platformSelected = $('#platform').val() !== null;
                const periodeSelected = $('#periode_ke').val() !== null;
                const shopSelected = $('#shop_id').val() !== null;

                $('#upload-button').prop('disabled', !(fileSelected && platformSelected && periodeSelected && shopSelected));
            }

            $('#csv-upload').on('change', function() {
                const files = $(this).prop('files');
                $('#selected-file-name').text(files.length > 0 ? files.length + ' file dipilih' : '');
                validateForm();
            });

            $('#platform, #periode_ke, #shop_id, #month_status').on('change', validateForm);

            $('#upload-button').on('click', function() {
                const platform = $('#platform').val();
                const periode_ke = $('#periode_ke').val();
                const shop = $('#shop_id option:selected').text();
                const month_status = $('#month_status option:selected').text();

                Swal.fire({
                    title: 'Konfirmasi Unggah',
                    html: `<p class="mb-3">Apakah Anda yakin data yang akan diunggah sudah benar?</p>
                           <ul class="list-group text-start">
                               <li class="list-group-item"><strong>Toko:</strong> ${shop}</li>
                               <li class="list-group-item"><strong>Platform:</strong> ${platform}</li>
                               <li class="list-group-item"><strong>Periode Ke:</strong> ${periode_ke}</li>
                               <li class="list-group-item"><strong>Status Bulan:</strong> ${month_status}</li>
                               <li class="list-group-item"><strong>Jumlah File:</strong> ${$('#csv-upload').prop('files').length}</li>
                           </ul>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Unggah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loading-overlay').css('display', 'flex');
                        $('#importForm').submit();
                    }
                });
            });

            $('#reset-button').on('click', function() {
                Swal.fire({
                    title: 'Anda Yakin?',
                    text: "Tindakan ini akan menghapus SEMUA data penjualan yang telah diimpor. Aksi ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loading-overlay').css('display', 'flex');
                        $.ajax({
                            url: '/performa-produk/compare-sales/reset',
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat mereset data.', 'error');
                                $('#loading-overlay').css('display', 'none');
                            }
                        });
                    }
                });
            });
            
            // =================================================================
            // NOTIFIKASI DARI SESSION
            // =================================================================
            @if (session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil', text: {!! json_encode(session('success')) !!} });
            @endif
            @if (session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal', text: {!! json_encode(session('error')) !!} });
            @endif
            @if ($errors->any())
                Swal.fire({ icon: 'error', title: 'Validasi Error', html: '{!! implode('<br>', $errors->all()) !!}' });
            @endif


            // =================================================================
            // FUNGSI RENDER CHART
            // =================================================================
            let chartInstances = {}; // Untuk menyimpan instance chart
            const chartColors = ['#4f46e5', '#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#6b7280', '#8b5cf6', '#ec4899'];

            function destroyChart(canvasId) {
                if (chartInstances[canvasId]) {
                    chartInstances[canvasId].destroy();
                }
            }
            
            // RENDER DOUGHNUT CHART
            function renderPie(canvasId, periode) {
                destroyChart(canvasId);
                $.ajax({
                    url: '/performa-produk/compare-sales/chart',
                    method: 'POST',
                    data: { periode: periode },
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        const ctx = document.getElementById(canvasId).getContext('2d');
                        const periode_current = res.jumlah_penjualan_current.reduce((a, b) => Number(a) + Number(b), 0);
                        const periode_previous = res.jumlah_penjualan_previous.reduce((a, b) => Number(a) + Number(b), 0);
                        
                        const elementMapping = {
                            'periode_1': { total: '#totalPeriode1', prev: '#prev_totalPeriode1' },
                            'periode_2': { total: '#totalPeriode2', prev: '#prev_totalPeriode2' },
                            'periode_3': { total: '#totalPeriode3', prev: '#prev_totalPeriode3' },
                            'periode_4': { total: '#totalPeriode4', prev: '#prev_totalPeriode4' },
                        };
                        
                        if(elementMapping[periode]){
                            $(elementMapping[periode].total).text(formatRupiah(periode_current));
                            $(elementMapping[periode].prev).text('Sebelumnya: ' + formatRupiah(periode_previous));
                        }
                        
                        // Override dataset colors
                        res.datasets.forEach(dataset => {
                            dataset.backgroundColor = chartColors;
                            dataset.borderColor = '#ffffff';
                            dataset.borderWidth = 2;
                        });

                        chartInstances[canvasId] = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: res.labels,
                                datasets: res.datasets
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: { 
                                    legend: { 
                                        position: 'bottom', 
                                        labels: { 
                                            padding: 20, 
                                            font: { size: 12 },
                                            usePointStyle: true,
                                            pointStyle: 'circle'
                                        } 
                                    },
                                    datalabels: {
                                        formatter: (value, ctx) => {
                                            const datapoints = ctx.chart.data.datasets[0].data;
                                            const total = datapoints.reduce((total, datapoint) => total + parseFloat(datapoint), 0);
                                            const percentage = (value / total) * 100;
                                            // Hanya tampilkan label jika persentase > 3%
                                            return percentage > 3 ? percentage.toFixed(1) + '%' : '';
                                        },
                                        color: '#ffffff',
                                        font: {
                                            weight: 'bold',
                                            size: 12
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            }

            // RENDER TOP 10 BAR CHART
            function getTop10Sales(canvasId, periode) {
                destroyChart(canvasId);
                $.ajax({
                    url: '/performa-produk/compare-sales/top-sales',
                    method: 'POST',
                    data: { periode: periode },
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        const ctx = document.getElementById(canvasId).getContext('2d');
                        chartInstances[canvasId] = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: res.labels,
                                datasets: [{
                                    label: 'Total Pendapatan',
                                    data: res.data,
                                    backgroundColor: 'rgba(79, 70, 229, 0.7)',
                                    borderColor: 'rgba(79, 70, 229, 1)',
                                    borderWidth: 1,
                                    borderRadius: 5
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                scales: { x: { beginAtZero: true } },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { callbacks: { label: (ctx) => formatRupiah(ctx.parsed.x) } }
                                }
                            }
                        });
                    }
                });
            }

            // =================================================================
            // INISIALISASI SEMUA CHART
            // =================================================================
            renderPie('piePeriodOne', 'periode_1');
            renderPie('piePeriodTwo', 'periode_2');
            renderPie('piePeriodThree', 'periode_3');
            renderPie('piePeriodFour', 'periode_4');
            
            getTop10Sales('top10SalesChartP1', 'periode_1');
            getTop10Sales('top10SalesChartP2', 'periode_2');
            getTop10Sales('top10SalesChartP3', 'periode_3');
            getTop10Sales('top10SalesChartP4', 'periode_4');


            $('#shuffle-button').on('click', function() {
                Swal.fire({
                    title: 'Anda Yakin?',
                    text: "Ini akan menghapus data previous(sebelumnya) kemudian memindahkan semua data current pada semua periode ke data sebelumnya. Aksi ini tidak dapat dibatalkan !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, pindahkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loading-overlay').css('display', 'flex');
                        $.ajax({
                            url: '/performa-produk/compare-sales/switch-data',
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat memindahkan data.', 'error');
                                $('#loading-overlay').css('display', 'none');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
