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
                                    <option value="semua">Semua Toko</option>
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
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="kpi-card bg-gradient-primary animated-card" style="animation-delay: 0.1s;">
                    <i class="fas fa-dollar-sign kpi-icon"></i>
                    <div class="kpi-content">
                        <h5 class="fw-bold">Total Pendapatan (Bulan Ini)</h5>
                        <h2 class="display-6" id="kpi-total-revenue">Rp 0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card bg-gradient-success animated-card" style="animation-delay: 0.2s;">
                    <i class="fas fa-chart-line kpi-icon"></i>
                    <div class="kpi-content">
                        <h5 class="fw-bold">Pertumbuhan (MoM)</h5>
                        <h2 class="display-6" id="kpi-growth">0%</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card bg-gradient-info animated-card" style="animation-delay: 0.3s;">
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
                        <div class="table-responsive mt-4">
                            <table class="table table-hover align-middle" id="subkategori-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center" style="min-width: 150px;">Kategori</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
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
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">P3-P4</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p4-P4</th>
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
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
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
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">P3-P4</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-warning">(-month)</span></th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-primary">(current)</span></th>
                                        <th class="text-center" style="min-width: 100px;">p4-P4</th>
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
                putTable(subKategoriData, 'subkategori-table');
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

            const growth = (totalPrevMonthRevenue > 0) ? ((totalCurrentMonthRevenue - totalPrevMonthRevenue) /
                totalPrevMonthRevenue) * 100 : (totalCurrentMonthRevenue > 0 ? 100 : 0);
            $('#kpi-growth').text(`${growth.toFixed(2)}%`).removeClass('text-success text-danger').addClass(growth >= 0 ?
                'text-white' : 'text-warning');

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

            const currentMonthPeriodTotals = [0, 0, 0, 0];
            processedData.forEach(item => {
                for (let i = 1; i <= 4; i++) {
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
                    labels: ['Periode 1', 'Periode 2', 'Periode 3', 'Periode 4'],
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
                    labels: ['P1 ke P2', 'P2 ke P3', 'P3 ke P4'],
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
                    1: p[1] - prev_p[4],
                    2: p[1] - prev_p[1],
                    3: p[2] - p[1],
                    4: p[2] - prev_p[2],
                    5: p[3] - p[2],
                    6: p[3] - prev_p[3],
                    7: p[4] - p[3],
                    8: p[4] - prev_p[4]
                };
                let pct = {
                    1: prev_p[4] ? (d[1] / prev_p[4]) * 100 : (d[1] !== 0 ? 100 : 0),
                    2: prev_p[1] ? (d[2] / prev_p[1]) * 100 : (d[2] !== 0 ? 100 : 0),
                    3: p[1] ? (d[3] / p[1]) * 100 : (d[3] !== 0 ? 100 : 0),
                    4: prev_p[2] ? (d[4] / prev_p[2]) * 100 : (d[4] !== 0 ? 100 : 0),
                    5: p[2] ? (d[5] / p[2]) * 100 : (d[5] !== 0 ? 100 : 0),
                    6: prev_p[3] ? (d[6] / prev_p[3]) * 100 : (d[6] !== 0 ? 100 : 0),
                    7: p[3] ? (d[7] / p[3]) * 100 : (d[7] !== 0 ? 100 : 0),
                    8: prev_p[4] ? (d[8] / prev_p[4]) * 100 : (d[8] !== 0 ? 100 : 0)
                };

                const row = `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        ${tableId === 'kategori-table' ? `<td>${item.sku}</td>` : ''}
                        <td>${tableId === 'kategori-table' ? item.nama_produk : `<a href="/performa-produk/compare-sales/kategori/${item.id}">${item.nama_kategori}</a>`}</td>
                        <td class="text-end">${formatRupiah(prev_p[4])}</td><td class="text-end">${formatRupiah(p[1])}</td><td class="text-center">${renderSelisih(d[1], pct[1])}</td>
                        <td class="text-end">${formatRupiah(prev_p[1])}</td><td class="text-end">${formatRupiah(p[1])}</td><td class="text-center">${renderSelisih(d[2], pct[2])}</td>
                        <td class="text-end">${formatRupiah(p[1])}</td><td class="text-end">${formatRupiah(p[2])}</td><td class="text-center">${renderSelisih(d[3], pct[3])}</td>
                        <td class="text-end">${formatRupiah(prev_p[2])}</td><td class="text-end">${formatRupiah(p[2])}</td><td class="text-center">${renderSelisih(d[4], pct[4])}</td>
                        <td class="text-end">${formatRupiah(p[2])}</td><td class="text-end">${formatRupiah(p[3])}</td><td class="text-center">${renderSelisih(d[5], pct[5])}</td>
                        <td class="text-end">${formatRupiah(prev_p[3])}</td><td class="text-end">${formatRupiah(p[3])}</td><td class="text-center">${renderSelisih(d[6], pct[6])}</td>
                        <td class="text-end">${formatRupiah(p[3])}</td><td class="text-end">${formatRupiah(p[4])}</td><td class="text-center">${renderSelisih(d[7], pct[7])}</td>
                        <td class="text-end">${formatRupiah(prev_p[4])}</td><td class="text-end">${formatRupiah(p[4])}</td><td class="text-center">${renderSelisih(d[8], pct[8])}</td>
                    </tr>`;
                $(`#${tableId} tbody`).append(row);
            });

            const total_d = {
                1: totals.p[1] - totals.prev_p[4],
                2: totals.p[1] - totals.prev_p[1],
                3: totals.p[2] - totals.p[1],
                4: totals.p[2] - totals.prev_p[2],
                5: totals.p[3] - totals.p[2],
                6: totals.p[3] - totals.prev_p[3],
                7: totals.p[4] - totals.p[3],
                8: totals.p[4] - totals.prev_p[4]
            };
            const total_pct = {
                1: totals.prev_p[4] ? (total_d[1] / totals.prev_p[4]) * 100 : (total_d[1] !== 0 ? 100 : 0),
                2: totals.prev_p[1] ? (total_d[2] / totals.prev_p[1]) * 100 : (total_d[2] !== 0 ? 100 : 0),
                3: totals.p[1] ? (total_d[3] / totals.p[1]) * 100 : (total_d[3] !== 0 ? 100 : 0),
                4: totals.prev_p[2] ? (total_d[4] / totals.prev_p[2]) * 100 : (total_d[4] !== 0 ? 100 : 0),
                5: totals.p[2] ? (total_d[5] / totals.p[2]) * 100 : (total_d[5] !== 0 ? 100 : 0),
                6: totals.prev_p[3] ? (total_d[6] / totals.prev_p[3]) * 100 : (total_d[6] !== 0 ? 100 : 0),
                7: totals.p[3] ? (total_d[7] / totals.p[3]) * 100 : (total_d[7] !== 0 ? 100 : 0),
                8: totals.prev_p[4] ? (total_d[8] / totals.prev_p[4]) * 100 : (total_d[8] !== 0 ? 100 : 0)
            };

            let tfoot =
                `<tr class="fw-bold bg-light"><td colspan="${tableId === 'kategori-table' ? 3 : 2}" class="text-end">Total</td>`;
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.prev_p[4])}</td><td class="text-end">${formatRupiah(totals.p[1])}</td><td class="text-center">${renderSelisih(total_d[1], total_pct[1])}</td>`;
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
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.p[3])}</td><td class="text-end">${formatRupiah(totals.p[4])}</td><td class="text-center">${renderSelisih(total_d[7], total_pct[7])}</td>`;
            tfoot +=
                `<td class="text-end">${formatRupiah(totals.prev_p[4])}</td><td class="text-end">${formatRupiah(totals.p[4])}</td><td class="text-center">${renderSelisih(total_d[8], total_pct[8])}</td>`;
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

        // Initial load
        $(document).ready(function() {
            getData();
        });
    </script>
@endpush
