@extends('layouts.main')

@push('styles')
    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    <style>
        /* General Animation for cards */
        .animated-card {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .custom-card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
            border-radius: 15px;
            /* Slightly more rounded */
            border: none;
        }

        .custom-card:hover {
            box-shadow: 0 8px 25px 0 rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .btn-modern {
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .kpi-card {
            color: white;
            border-radius: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease-in-out;
            min-height: 160px;
        }

        .kpi-card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .kpi-icon {
            font-size: 3rem;
            opacity: 0.8;
        }

        .kpi-content h5 {
            margin-bottom: 0.5rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #0d6efd, #6f42c1);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #198754, #28a745);
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #0dcaf0, #20c997);
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 40vh;
            width: 100%;
        }

        .card-title-icon {
            margin-right: 10px;
            color: #0d6efd;
        }

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

        #subkategori-table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #f8f9fa;
        }

        #subkategori-table th:nth-child(1),
        #subkategori-table td:nth-child(1) {
            position: sticky;
            left: 0;
            z-index: 1;
            background-color: #f8f9fa;
        }

        #subkategori-table th:nth-child(2),
        #subkategori-table td:nth-child(2) {
            position: sticky;
            left: 50px;
            /* Adjust as needed */
            z-index: 1;
            background-color: #f8f9fa;
        }

        #subkategori-table thead th:nth-child(1),
        #subkategori-table thead th:nth-child(2) {
            z-index: 2;
        }

        /* Sub Kategori Summary table styling */
        #sub-summary { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; }
        #subkategori-summary-table thead th { position: sticky; top: 0; z-index: 2; background-color: #f8f9fa; }
        #subkategori-summary-table th, #subkategori-summary-table td { white-space: nowrap; vertical-align: middle; }
        #subkategori-summary-table th:nth-child(2), #subkategori-summary-table td:nth-child(2) { position: sticky; left: 0; z-index: 1; background-color: #fff; box-shadow: 4px 0 6px rgba(16,24,40,.05); }
        #subkategori-summary-table thead th:nth-child(2) { z-index: 3; }
    </style>
@endpush

@section('content')
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status" style="width: 3.5rem; height: 3.5rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div class="container-fluid">

        <div class="row g-4 mb-4">
            <div class="col-12">
                <a href="/performa-produk/compare-sales/kategori" class="btn btn-outline-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <div class="col-12 text-center">
                <h1 class="display-5 fw-bold text-primary mb-2">{{ $kategori->nama_kategori }}</h1>
                <p class="text-muted">Analisis Performa Penjualan Berdasarkan Kategori</p>
            </div>
            <div class="col-12">
                <div class="card custom-card animated-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-4">
                            <div class="col-md-4">
                                <label for="toko" class="form-label"><i class="fas fa-store me-2"></i>Toko</label>
                                <select class="form-select" id="toko" onchange="getData()">
                                    @if(optional(auth()->user())->shop_id == 0)
                                        <option value="semua">Semua Toko</option>
                                    @endif
                                    @foreach ($shops as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="channel" class="form-label"><i
                                        class="fas fa-broadcast-tower me-2"></i>Channel</label>
                                <select class="form-select" id="channel" onchange="changeChannel()">
                                    <option value="semua">Semua Channel</option>
                                    <option value="shopee">Shopee</option>
                                    <option value="tiktok">Tiktok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 1: KPIs --}}
        <div class="row g-4 mb-4 align-items-stretch">
            <div class="col-md-4 d-flex">
                <div class="kpi-card bg-gradient-primary animated-card text-white w-100 h-100" style="animation-delay: 0.1s;">
                    <i class="fas fa-sack-dollar kpi-icon"></i>
                    <div class="kpi-content w-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-uppercase small opacity-75">Total Pendapatan (Bulan Ini)</div>
                            <span id="kpi-rev-diff-pill" class="badge rounded-pill bg-secondary d-flex align-items-center gap-1">
                                <i class="fas fa-minus"></i>
                                <span id="kpi-rev-diff-pct">0%</span>
                            </span>
                        </div>
                        <div class="display-6 fw-bold lh-1" id="kpi-total-revenue">Rp 0</div>
                        <div class="small opacity-75">Bulan lalu: <span id="kpi-prev-total-revenue">Rp 0</span></div>
                        <div class="small opacity-75">Δ <span id="diff-total-revenue">Rp 0</span> vs bulan lalu</div>
                        <div class="progress mt-2" style="height: 6px; background: rgba(255,255,255,0.25);">
                            <div id="kpi-rev-progress" class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex">
                <div class="kpi-card bg-gradient-success animated-card w-100 h-100" style="animation-delay: 0.2s;">
                    <i class="fas fa-chart-line kpi-icon"></i>
                    <div class="kpi-content w-100">
                        <h5 class="fw-bold">Pertumbuhan P1 → P2 → P3</h5>
                        <div class="d-flex justify-content-between"><div class="small opacity-75">P1 → P2</div><div class="h4 mb-0" id="kpi-p12-pct">0%</div></div>
                        <div class="small opacity-75" id="kpi-p12-amt">(Rp 0)</div>
                        <div class="d-flex justify-content-between mt-2"><div class="small opacity-75">P2 → P3</div><div class="h4 mb-0" id="kpi-p23-pct">0%</div></div>
                        <div class="small opacity-75" id="kpi-p23-amt">(Rp 0)</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex">
                <div class="kpi-card bg-gradient-info animated-card w-100 h-100" style="animation-delay: 0.3s;">
                    <i class="fas fa-trophy kpi-icon"></i>
                    <div class="kpi-content">
                        <h5 class="fw-bold">Channel Terbaik (Bulan Ini)</h5>
                        <h2 class="display-6" id="kpi-best-channel">-</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Sales Performance & Platform Distribution --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card custom-card h-100 animated-card" style="animation-delay: 0.4s;">
                    <div class="card-body">
                        <h5 class="fw-bold text-center"><i class="fas fa-chart-bar card-title-icon"></i>Grafik Penjualan
                            (Bulan Ini)</h5>
                        <p class="fw-bold mb-3 text-muted text-center">Perbandingan Antar Periode</p>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card h-100 animated-card" style="animation-delay: 0.5s;">
                    <div class="card-body">
                        <h5 class="fw-semibold text-center"><i class="fas fa-pie-chart card-title-icon"></i>Distribusi
                            Platform (Bulan Ini)</h5>
                        <p class="fw-bold mb-3 text-muted text-center">Berdasarkan Total Pendapatan</p>
                        <div class="chart-container">
                            <canvas id="piePlatform"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Product Performance & Monthly Comparison --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card custom-card h-100 animated-card" style="animation-delay: 0.6s;">
                    <div class="card-body">
                        <h5 class="fw-semibold text-center"><i class="fas fa-star card-title-icon"></i>Top 10 SKU (Bulan Ini
                            vs Bulan Lalu)</h5>
                        <p class="fw-bold mb-3 text-muted text-center">Berdasarkan Total Pendapatan</p>
                        <div class="chart-container">
                            <canvas id="barTop10"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card h-100 animated-card" style="animation-delay: 0.7s;">
                    <div class="card-body">
                        <h5 class="fw-semibold text-center"><i
                                class="fas fa-balance-scale card-title-icon"></i>Perbandingan Pendapatan Bulanan</h5>
                        <p class="fw-bold mb-3 text-muted text-center">Bulan Ini vs Bulan Lalu</p>
                        <div class="chart-container">
                            <canvas id="monthlyComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- NEW Row: Growth Chart --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card custom-card h-100 animated-card" style="animation-delay: 0.8s;">
                    <div class="card-body">
                        <h5 class="fw-semibold text-center"><i class="fas fa-percentage card-title-icon"></i>Pertumbuhan
                            Pendapatan per Periode</h5>
                        <p class="fw-bold mb-3 text-muted text-center">Perubahan Persentase Antar Periode</p>
                        <div class="chart-container">
                            <canvas id="periodGrowthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: Sub Kategori Table --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card custom-card animated-card" style="animation-delay: 0.9s;">
                    <div class="card-body">
                        <h2 class="fw-semibold mb-3 text-center"><i class="fas fa-tags card-title-icon"></i>Sub Kategori
                        </h2>
                        <h4 class="fw-bold mb-0 text-center" id="sub_namaToko">Nama Toko</h4>
                        <h5 class="text-muted text-center" id="sub_namaChannel">Channel</h5>

                        <div class="d-flex justify-content-end align-items-center mt-3">
                            <div class="ms-auto w-100" style="max-width:320px;">
                                <label for="subkategoriId" class="form-label mb-1"><i class="fas fa-filter me-2"></i>Filter Sub Kategori</label>
                                <select name="subkategoriId" id="subkategoriId" class="form-select">
                                    <option value="semua">Semua</option>
                                    @foreach ($subkategori as $item)
                                        <option value="{{ $item->nama_kategori }}">{{ $item->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            {{-- Summary Table (Prev total vs Current total) --}}
                            <div class="mb-3" id="sub-summary">
                                <div class="d-flex justify-content-between align-items-center px-2 pt-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-table text-primary"></i>
                                        <div>
                                            <h6 class="fw-bold mb-0">Ringkasan Total per Sub Kategori</h6>
                                            <small class="text-muted">Perbandingan jumlah semua periode previous vs current</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive px-2 pb-2">
                                    <table class="table table-hover table-sm align-middle" id="subkategori-summary-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width:60px">No</th>
                                                <th class="text-center" style="min-width: 180px;">Sub Kategori</th>
                                                <th class="text-center" style="min-width: 140px;">Total Previous</th>
                                                <th class="text-center" style="min-width: 140px;">Total Current</th>
                                                <th class="text-center" style="min-width: 140px;">Perubahan</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>

                            
                            <table class="table table-hover align-middle" id="subkategori-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center" style="min-width: 150px;">Kategori</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p4-P1</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p1-P1</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">P1-P2</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p2-P2</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">P2-P3</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p3-P3</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 5: Detail Table --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card custom-card animated-card" style="animation-delay: 1.0s;">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center"><i class="fas fa-list-alt card-title-icon"></i>Detail
                            Pendapatan per SKU</h5>
                        <h4 class="fw-bold mb-0 text-center" id="kat_namaToko">Nama Toko</h4>
                        <h5 class="text-muted text-center" id="kat_namaChannel">Channel</h5>
                        <div class="table-responsive mt-4">
                            <table class="table table-hover align-middle" id="kategori-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">SKU</th>
                                        <th class="text-center" style="min-width: 200px;">Nama</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p4-P1</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p1-P1</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">P1-P2</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p2-P2</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">P2-P3</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p3-P3</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script sources remain the same --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // All other JS functions (getData, getSubKategori, changeChannel, updateDashboard, updateKPIsAndCharts, putTable) remain the same

        // Helper to format currency
        function formatRupiah(value) {
            return 'Rp ' + (value || 0).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Initial data fetch
        function getData() {
            $('#loading-overlay').css('display', 'flex');
            $.ajax({
                url: '/performa-produk/compare-sales/kategori/detail-kategori/' + {{ $kategori->id }},
                method: 'GET',
                data: {
                    id: {{ $kategori->id }},
                    shop_id: $('#toko').val()
                },
                success: function(response) {
                    window.cachedKategoriData = response.kategori;
                    getSubKategori(); // Fetch sub-category data after main data is loaded
                    changeChannel(); // This will trigger all updates

                    $('#loading-overlay').css('display', 'none');
                },
                error: function(xhr) {
                    console.error('Error fetching main data:', xhr);
                    $('#loading-overlay').css('display', 'none');
                }
            });
        }

        function getSubKategori() {
            $.ajax({
                url: '/performa-produk/compare-sales/kategori/get-sub-kategori/' + {{ $kategori->id }},
                method: 'GET',
                data: {
                    id: {{ $kategori->id }},
                    shop_id: $('#toko').val()
                },
                async: false, // Ensure this completes before proceeding
                success: function(response) {
                    window.cachedSubKategoriData = response.subkategori;
                },
                error: function(xhr) {
                    console.error('Error fetching sub-kategori data:', xhr);
                }
            });
        }

        function changeChannel() {
            var tokoName = $('#toko option:selected').text();
            var channelName = $('#channel option:selected').text();

            $('#sub_namaToko, #kat_namaToko').text(tokoName);
            $('#sub_namaChannel, #kat_namaChannel').text(channelName);

            updateDashboard(window.cachedKategoriData, window.cachedSubKategoriData);
        }

        function updateDashboard(kategoriData, subKategoriData) {
            if (!kategoriData) return;

            updateKPIsAndCharts(kategoriData);
            putTable(kategoriData, 'kategori-table');
            if (subKategoriData) {
                const selectedSubKategori = $('#subkategoriId').val();
                let filteredSubKategoriData = subKategoriData;
                if (selectedSubKategori && selectedSubKategori !== 'semua') {
                    filteredSubKategoriData = subKategoriData.filter(item => item.nama_kategori === selectedSubKategori);
                }
                putTable(filteredSubKategoriData, 'subkategori-table');
                generateSubSummaryTable(filteredSubKategoriData);
            }
        }

        function updateKPIsAndCharts(data) {
            const channel = $('#channel').val();

            let totalCurrentMonthRevenue = 0;
            let totalPrevMonthRevenue = 0;
            let totalCurrentMonthShopee = 0;
            let totalCurrentMonthTiktok = 0;

            const processedData = data.map(item => {
                const p = {},
                    prev_p = {};
                for (let i = 1; i <= 4; i++) {
                    p[i] = parseFloat(channel === 'semua' ? item[`pendapatan_per_${i}`] : (channel === 'shopee' ?
                        item[`pendapatan_shopee_per_${i}`] : item[`pendapatan_tiktok_per_${i}`])) || 0;
                    prev_p[i] = parseFloat(channel === 'semua' ? item[`prev_pendapatan_per_${i}`] : (channel ===
                        'shopee' ? item[`prev_pendapatan_shopee_per_${i}`] : item[
                            `prev_pendapatan_tiktok_per_${i}`])) || 0;
                }
                const itemCurrentMonthTotal = p[1] + p[2] + p[3] + p[4];
                const itemPrevMonthTotal = prev_p[1] + prev_p[2] + prev_p[3] + prev_p[4];

                totalCurrentMonthRevenue += itemCurrentMonthTotal;
                totalPrevMonthRevenue += itemPrevMonthTotal;

                for (let i = 1; i <= 4; i++) {
                    totalCurrentMonthShopee += parseFloat(item[`pendapatan_shopee_per_${i}`]) || 0;
                    totalCurrentMonthTiktok += parseFloat(item[`pendapatan_tiktok_per_${i}`]) || 0;
                }

                return {
                    ...item,
                    itemCurrentMonthTotal,
                    itemPrevMonthTotal
                };
            });

            $('#kpi-total-revenue').text(formatRupiah(totalCurrentMonthRevenue));
            $('#kpi-prev-total-revenue').text(formatRupiah(totalPrevMonthRevenue));
            const diffAmt = totalCurrentMonthRevenue - totalPrevMonthRevenue;
            $('#diff-total-revenue').text((diffAmt>=0?'+':'') + formatRupiah(Math.abs(diffAmt))).toggleClass('text-warning', diffAmt<0);


            const growth = (totalPrevMonthRevenue > 0) ? ((totalCurrentMonthRevenue - totalPrevMonthRevenue) /
                totalPrevMonthRevenue) * 100 : (totalCurrentMonthRevenue > 0 ? 100 : 0);
            // Growth P1 -> P2 -> P3 (totals for current month)
            // Aggregate totals by reading item fields per channel
            let totalP1 = 0, totalP2 = 0, totalP3 = 0;
            processedData.forEach(it => {
                let p1=0,p2=0,p3=0;
                if (channel === 'semua') {
                    p1 = parseFloat(it.pendapatan_per_1) || 0;
                    p2 = parseFloat(it.pendapatan_per_2) || 0;
                    p3 = parseFloat(it.pendapatan_per_3) || 0;
                } else if (channel === 'shopee') {
                    p1 = parseFloat(it.pendapatan_shopee_per_1) || 0;
                    p2 = parseFloat(it.pendapatan_shopee_per_2) || 0;
                    p3 = parseFloat(it.pendapatan_shopee_per_3) || 0;
                } else {
                    p1 = parseFloat(it.pendapatan_tiktok_per_1) || 0;
                    p2 = parseFloat(it.pendapatan_tiktok_per_2) || 0;
                    p3 = parseFloat(it.pendapatan_tiktok_per_3) || 0;
                }
                totalP1 += p1; totalP2 += p2; totalP3 += p3;
            });
            const d12 = totalP2 - totalP1;
            const d23 = totalP3 - totalP2;
            const pct12 = totalP1 > 0 ? (d12 / totalP1) * 100 : (d12 !== 0 ? 100 : 0);
            const pct23 = totalP2 > 0 ? (d23 / totalP2) * 100 : (d23 !== 0 ? 100 : 0);
            $('#kpi-p12-pct').text(`${pct12.toFixed(1)}%`);
            $('#kpi-p12-amt').text(`(${(d12>=0?'+':'')}${formatRupiah(Math.abs(d12))})`);
            $('#kpi-p23-pct').text(`${pct23.toFixed(1)}%`);
            $('#kpi-p23-amt').text(`(${(d23>=0?'+':'')}${formatRupiah(Math.abs(d23))})`);

            // Update diff pill and progress bar in the revenue card
            $('#kpi-rev-diff-pct').text(`${growth.toFixed(1)}%`);
            const pill = $('#kpi-rev-diff-pill');
            pill.removeClass('bg-success bg-danger bg-secondary');
            pill.addClass(growth>0 ? 'bg-success' : growth<0 ? 'bg-danger' : 'bg-secondary');
            pill.find('i').attr('class', growth>0 ? 'fas fa-arrow-up' : growth<0 ? 'fas fa-arrow-down' : 'fas fa-minus');
            const pctComplete = (totalPrevMonthRevenue>0) ? (totalCurrentMonthRevenue/totalPrevMonthRevenue)*100 : (totalCurrentMonthRevenue>0 ? 100 : 0);
            const pctClamped = Math.max(0, Math.min(100, pctComplete));
            $('#kpi-rev-progress').css('width', pctClamped.toFixed(0) + '%').attr('aria-valuenow', pctClamped.toFixed(0));

            let bestChannel = '-';
            if (totalCurrentMonthShopee > totalCurrentMonthTiktok) bestChannel = 'Shopee';
            else if (totalCurrentMonthTiktok > totalCurrentMonthShopee) bestChannel = 'Tiktok';
            else if (totalCurrentMonthShopee > 0) bestChannel = 'Equal';
            $('#kpi-best-channel').text(bestChannel);

            ['revenueChart', 'piePlatform', 'barTop10', 'monthlyComparisonChart', 'periodGrowthChart'].forEach(id => {
                if (window[id] instanceof Chart) {
                    window[id].destroy();
                }
            });

            const currentMonthPeriodTotals = [0, 0, 0];
            processedData.forEach(item => {
                for (let i = 1; i <= 3; i++) {
                    let periodValue = 0;
                    if (channel === 'semua') periodValue = parseFloat(item[`pendapatan_per_${i}`]) || 0;
                    else if (channel === 'shopee') periodValue = parseFloat(item[`pendapatan_shopee_per_${i}`]) ||
                        0;
                    else periodValue = parseFloat(item[`pendapatan_tiktok_per_${i}`]) || 0;
                    currentMonthPeriodTotals[i - 1] += periodValue;
                }
            });

            window.revenueChart = new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: ['Periode 1', 'Periode 2', 'Periode 3'],
                    datasets: [{
                        label: 'Pendapatan Bulan Ini',
                        data: currentMonthPeriodTotals,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            window.piePlatform = new Chart(document.getElementById('piePlatform'), {
                type: 'pie',
                data: {
                    labels: [`Shopee`, `Tiktok`],
                    datasets: [{
                        data: [totalCurrentMonthShopee, totalCurrentMonthTiktok],
                        backgroundColor: ['#f5552dff', '#1b1b1bff']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            const top10Data = processedData.sort((a, b) => b.itemCurrentMonthTotal - a.itemCurrentMonthTotal).slice(0, 10);

            window.barTop10 = new Chart(document.getElementById('barTop10'), {
                type: 'bar',
                data: {
                    labels: top10Data.map(i => i.sku),
                    datasets: [{
                            label: 'Pendapatan Bulan Ini',
                            data: top10Data.map(i => i.itemCurrentMonthTotal),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        },
                        {
                            label: 'Pendapatan Bulan Lalu',
                            data: top10Data.map(i => i.itemPrevMonthTotal),
                            backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            window.monthlyComparisonChart = new Chart(document.getElementById('monthlyComparisonChart'), {
                type: 'bar',
                data: {
                    labels: ['Bulan Lalu', 'Bulan Ini'],
                    datasets: [{
                        label: 'Total Pendapatan',
                        data: [totalPrevMonthRevenue, totalCurrentMonthRevenue],
                        backgroundColor: ['rgba(255, 159, 64, 0.8)', 'rgba(75, 192, 192, 0.8)']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // New Period Growth Chart Logic
            const periodGrowth = [];
            for (let i = 1; i < currentMonthPeriodTotals.length; i++) {
                const prevPeriod = currentMonthPeriodTotals[i - 1];
                const currentPeriod = currentMonthPeriodTotals[i];
                const growthPercentage = prevPeriod > 0 ? ((currentPeriod - prevPeriod) / prevPeriod) * 100 : (
                    currentPeriod > 0 ? 100 : 0);
                periodGrowth.push(growthPercentage);
            }

            window.periodGrowthChart = new Chart(document.getElementById('periodGrowthChart'), {
                type: 'bar',
                data: {
                    labels: ['P1 ke P2', 'P2 ke P3'],
                    datasets: [{
                        label: 'Pertumbuhan Pendapatan (%)',
                        data: periodGrowth,
                        backgroundColor: periodGrowth.map(g => g >= 0 ? 'rgba(25, 135, 84, 0.8)' :
                            'rgba(220, 53, 69, 0.8)'),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y.toFixed(2)}%`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function putTable(data, tableId) {
            if (!data) return;
            if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
                $(`#${tableId}`).DataTable().destroy();
            }
            $(`#${tableId} tbody`).empty();
            $(`#${tableId} tfoot`).empty();

            function renderSelisih(diff, pct) {
                let icon = diff > 0 ? '<i class="fas fa-caret-up"></i>' : diff < 0 ? '<i class="fas fa-caret-down"></i>' :
                    '';
                let color = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-muted';
                return `<span class="${color}">
                    ${icon} ${pct !== null ? pct.toFixed(2) : '0.00'}%
                    <br>(${formatRupiah(Math.abs(diff))})
                </span>`;
            }

            let channel = $('#channel').val();
            let totals = {
                p: {
                    1: 0,
                    2: 0,
                    3: 0,
                    4: 0
                },
                prev_p: {
                    1: 0,
                    2: 0,
                    3: 0,
                    4: 0
                },
            };

            data.forEach((item, index) => {
                let p = {},
                    prev_p = {};

                for (let i = 1; i <= 4; i++) {
                    p[i] = parseFloat(channel === 'semua' ? item[`pendapatan_per_${i}`] : (channel === 'shopee' ?
                        item[`pendapatan_shopee_per_${i}`] : item[`pendapatan_tiktok_per_${i}`])) || 0;
                    prev_p[i] = parseFloat(channel === 'semua' ? item[`prev_pendapatan_per_${i}`] : (channel ===
                        'shopee' ? item[`prev_pendapatan_shopee_per_${i}`] : item[
                            `prev_pendapatan_tiktok_per_${i}`])) || 0;

                    totals.p[i] += p[i];
                    totals.prev_p[i] += prev_p[i];
                }

                let d = {
                    1: p[1] - prev_p[3],
                    2: p[1] - prev_p[1],
                    3: p[2] - p[1],
                    4: p[2] - prev_p[2],
                    5: p[3] - p[2],
                    6: p[3] - prev_p[3],
                };
                let pct = {
                    1: prev_p[3] ? (d[1] / prev_p[3]) * 100 : (d[1] !== 0 ? 100 : 0),
                    2: prev_p[1] ? (d[2] / prev_p[1]) * 100 : (d[2] !== 0 ? 100 : 0),
                    3: p[1] ? (d[3] / p[1]) * 100 : (d[3] !== 0 ? 100 : 0),
                    4: prev_p[2] ? (d[4] / prev_p[2]) * 100 : (d[4] !== 0 ? 100 : 0),
                    5: p[2] ? (d[5] / p[2]) * 100 : (d[5] !== 0 ? 100 : 0),
                    6: prev_p[3] ? (d[6] / prev_p[3]) * 100 : (d[6] !== 0 ? 100 : 0),
                };

                const row = `
                    <tr>
                        <td class="text-center" data-order="${index + 1}">${index + 1}</td>
                        ${tableId === 'kategori-table' ? `<td data-order="${(item.sku||'').toString().toLowerCase()}">${item.sku||''}</td>` : ''}
                        <td data-order="${(tableId === 'kategori-table' ? (item.nama_produk||'') : (item.nama_kategori||'')).toString().toLowerCase()}">${tableId === 'kategori-table' ? item.nama_produk : `<a href="/performa-produk/compare-sales/kategori/${item.id}">${item.nama_kategori}</a>`}</td>
                        <td class="text-end" data-order="${prev_p[3]}">${formatRupiah(prev_p[3])}</td><td class="text-end" data-order="${p[1]}">${formatRupiah(p[1])}</td><td class="text-center" data-order="${d[1]}">${renderSelisih(d[1], pct[1])}</td>
                        <td class="text-end" data-order="${prev_p[1]}">${formatRupiah(prev_p[1])}</td><td class="text-end" data-order="${p[1]}">${formatRupiah(p[1])}</td><td class="text-center" data-order="${d[2]}">${renderSelisih(d[2], pct[2])}</td>
                        <td class="text-end" data-order="${p[1]}">${formatRupiah(p[1])}</td><td class="text-end" data-order="${p[2]}">${formatRupiah(p[2])}</td><td class="text-center" data-order="${d[3]}">${renderSelisih(d[3], pct[3])}</td>
                        <td class="text-end" data-order="${prev_p[2]}">${formatRupiah(prev_p[2])}</td><td class="text-end" data-order="${p[2]}">${formatRupiah(p[2])}</td><td class="text-center" data-order="${d[4]}">${renderSelisih(d[4], pct[4])}</td>
                        <td class="text-end" data-order="${p[2]}">${formatRupiah(p[2])}</td><td class="text-end" data-order="${p[3]}">${formatRupiah(p[3])}</td><td class="text-center" data-order="${d[5]}">${renderSelisih(d[5], pct[5])}</td>
                        <td class="text-end" data-order="${prev_p[3]}">${formatRupiah(prev_p[3])}</td><td class="text-end" data-order="${p[3]}">${formatRupiah(p[3])}</td><td class="text-center" data-order="${d[6]}">${renderSelisih(d[6], pct[6])}</td>
                    </tr>`;
                $(`#${tableId} tbody`).append(row);
            });

            const total_d = {
                1: totals.p[1] - totals.prev_p[3],
                2: totals.p[1] - totals.prev_p[1],
                3: totals.p[2] - totals.p[1],
                4: totals.p[2] - totals.prev_p[2],
                5: totals.p[3] - totals.p[2],
                6: totals.p[3] - totals.prev_p[3],
            };
            const total_pct = {
                1: totals.prev_p[3] ? (total_d[1] / totals.prev_p[3]) * 100 : (total_d[1] !== 0 ? 100 : 0),
                2: totals.prev_p[1] ? (total_d[2] / totals.prev_p[1]) * 100 : (total_d[2] !== 0 ? 100 : 0),
                3: totals.p[1] ? (total_d[3] / totals.p[1]) * 100 : (total_d[3] !== 0 ? 100 : 0),
                4: totals.prev_p[2] ? (total_d[4] / totals.prev_p[2]) * 100 : (total_d[4] !== 0 ? 100 : 0),
                5: totals.p[2] ? (total_d[5] / totals.p[2]) * 100 : (total_d[5] !== 0 ? 100 : 0),
                6: totals.prev_p[3] ? (total_d[6] / totals.prev_p[3]) * 100 : (total_d[6] !== 0 ? 100 : 0)
            };

            let tfoot =
                `<tr class="fw-bold bg-light"><td colspan="${tableId === 'kategori-table' ? 3 : 2}" class="text-end">Total</td>`;
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.prev_p[3])}</td><td class="text-end">${formatRupiah(totals.p[1])}</td><td class="text-center">${renderSelisih(total_d[1], total_pct[1])}</td>`;
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.prev_p[1])}</td><td class="text-end">${formatRupiah(totals.p[1])}</td><td class="text-center">${renderSelisih(total_d[2], total_pct[2])}</td>`;
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.p[1])}</td><td class="text-end">${formatRupiah(totals.p[2])}</td><td class="text-center">${renderSelisih(total_d[3], total_pct[3])}</td>`;
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.prev_p[2])}</td><td class="text-end">${formatRupiah(totals.p[2])}</td><td class="text-center">${renderSelisih(total_d[4], total_pct[4])}</td>`;
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.p[2])}</td><td class="text-end">${formatRupiah(totals.p[3])}</td><td class="text-center">${renderSelisih(total_d[5], total_pct[5])}</td>`;
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.prev_p[3])}</td><td class="text-end">${formatRupiah(totals.p[3])}</td><td class="text-center">${renderSelisih(total_d[6], total_pct[6])}</td>`;
            tfoot += '</tr>';
            $(`#${tableId} tfoot`).html(tfoot);

            $(`#${tableId}`).DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                dom: 'Bfrtip',
                "footerCallback": function(row, data, start, end, display) {
                    // This can be left empty as the footer is now static
                }
            });
        }

        // Summary table for Sub Kategori: total previous (prev1+prev2+prev3) vs total current (p1+p2+p3)
        function generateSubSummaryTable(data) {
            const channel = $('#channel').val();
            // destroy previous datatable first
            if ($.fn.dataTable && $.fn.dataTable.isDataTable('#subkategori-summary-table')) {
                $('#subkategori-summary-table').DataTable().destroy();
            }

            const tbody = $('#subkategori-summary-table tbody');
            const tfoot = $('#subkategori-summary-table tfoot');
            if (!tbody.length) return;
            tbody.empty(); tfoot.empty();

            function rupiah(v){ return 'Rp ' + (v||0).toLocaleString('id-ID'); }
            function badge(diff, pct){
                const up = diff > 0, down = diff < 0;
                const cls = up ? 'text-success' : down ? 'text-danger' : 'text-muted';
                const icon = up ? '<i class="fas fa-caret-up"></i>' : down ? '<i class="fas fa-caret-down"></i>' : '<i class="fas fa-minus"></i>';
                const pctText = (isFinite(pct) ? pct.toFixed(2) : '0.00') + '%';
                const amtText = rupiah(Math.abs(diff));
                return `<span class="${cls}">${icon} ${pctText} <span class="text-muted ms-1">(${amtText})</span></span>`;
            }

            let sumPrev = 0, sumCurr = 0;
            (data||[]).forEach((item, i) => {
                const p1 = parseFloat(channel==='semua'? item.pendapatan_per_1 : channel==='shopee'? item.pendapatan_shopee_per_1 : item.pendapatan_tiktok_per_1) || 0;
                const p2 = parseFloat(channel==='semua'? item.pendapatan_per_2 : channel==='shopee'? item.pendapatan_shopee_per_2 : item.pendapatan_tiktok_per_2) || 0;
                const p3 = parseFloat(channel==='semua'? item.pendapatan_per_3 : channel==='shopee'? item.pendapatan_shopee_per_3 : item.pendapatan_tiktok_per_3) || 0;
                const prev1 = parseFloat(channel==='semua'? item.prev_pendapatan_per_1 : channel==='shopee'? item.prev_pendapatan_shopee_per_1 : item.prev_pendapatan_tiktok_per_1) || 0;
                const prev2 = parseFloat(channel==='semua'? item.prev_pendapatan_per_2 : channel==='shopee'? item.prev_pendapatan_shopee_per_2 : item.prev_pendapatan_tiktok_per_2) || 0;
                const prev3 = parseFloat(channel==='semua'? item.prev_pendapatan_per_3 : channel==='shopee'? item.prev_pendapatan_shopee_per_3 : item.prev_pendapatan_tiktok_per_3) || 0;

                const totalPrev = prev1+prev2+prev3;
                const totalCurr = p1+p2+p3;
                const diff = totalCurr - totalPrev;
                const pct = totalPrev>0 ? (diff/totalPrev)*100 : (diff!==0?100:0);

                sumPrev += totalPrev; sumCurr += totalCurr;

                const row = `
                    <tr>
                        <td class="text-center" data-order="${i+1}">${i+1}</td>
                        <td data-order="${(item.nama_kategori||'').toString().toLowerCase()}">${item.nama_kategori||'-'}</td>
                        <td class="text-end" data-order="${totalPrev}">${rupiah(totalPrev)}</td>
                        <td class="text-end" data-order="${totalCurr}">${rupiah(totalCurr)}</td>
                        <td class="text-center" data-order="${diff}">${badge(diff, pct)}</td>
                    </tr>`;
                tbody.append(row);
            });

            const tdiff = sumCurr - sumPrev;
            const tpct = sumPrev>0 ? (tdiff/sumPrev)*100 : (tdiff!==0?100:0);
            const foot = `
                <tr class="fw-semibold">
                    <td colspan="2" class="text-end">Total:</td>
                    <td class="text-end">${rupiah(sumPrev)}</td>
                    <td class="text-end">${rupiah(sumCurr)}</td>
                    <td class="text-center">${badge(tdiff, tpct)}</td>
                </tr>`;
            tfoot.html(foot);

            if ($.fn.dataTable) {
                $('#subkategori-summary-table').DataTable({
                    order: [],
                    paging: false,
                    info: false,
                    autoWidth: false,
                    searching: false,
                    language: { zeroRecords: 'Tidak ada data' }
                });
            }
        }

        // Initial load
        $(document).ready(function() {
            getData();
            $('#subkategoriId').on('change', function() {
                updateDashboard(window.cachedKategoriData, window.cachedSubKategoriData);
            });
        });
    </script>
@endpush
