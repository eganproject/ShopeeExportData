@extends('layouts.main')

{{-- Styles untuk tampilan modern halaman kategori --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --cok-surface: #ffffff;
            --cok-surface-2: #f8fafc;
            --cok-border: #e5e7eb;
            --cok-text-muted: #6b7280;
            --cok-header-grad-from: #0d6efd;
            --cok-header-grad-to: #0dcaf0;
        }

        .custom-card {
            border: 1px solid var(--cok-border) !important;
            border-radius: 14px !important;
            box-shadow: 0 4px 24px rgba(16, 24, 40, 0.06);
        }

        .page-heading { display: flex; align-items: center; gap: .75rem; }
        .page-heading .icon {
            display: inline-flex; width: 36px; height: 36px; border-radius: 10px;
            align-items: center; justify-content: center; color: #0d6efd;
            background: linear-gradient(135deg, rgba(13,110,253,.12), rgba(13,202,240,.12));
        }

        .toolbar { background: var(--cok-surface-2); border: 1px solid var(--cok-border); border-radius: 12px; padding: 1rem; }
        .btn-soft-secondary { color: #495057; background-color: #f1f3f5; border: 1px solid #e9ecef; }
        .btn-soft-secondary:hover { background-color: #e9ecef; }

        #table-container { max-height: 1000px; overflow: auto; background: var(--cok-surface); border: 1px solid var(--cok-border); border-radius: 12px; }
        #kategori-table { margin: 0; }

        #kategori-table thead th {
            position: sticky; top: 0; z-index: 2;
            background-color: var(--cok-surface-2);
            color: #0b2239; border-bottom: 1px solid var(--cok-border);
        }
        #kategori-table th, #kategori-table td { white-space: nowrap; vertical-align: middle; }
        #kategori-table th:nth-child(2), #kategori-table td:nth-child(2) {
            position: sticky; left: 0; z-index: 1; background-color: #fff; box-shadow: 4px 0 6px rgba(16,24,40,.05);
        }
        #kategori-table thead th:nth-child(2) { z-index: 3; }

        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .diff-badge { font-weight: 600; letter-spacing: .2px; }
        .diff-up { background: var(--bs-success-bg-subtle); color: #157347; border: 1px solid var(--bs-success-border-subtle); }
        .diff-down { background: var(--bs-danger-bg-subtle); color: #b02a37; border: 1px solid var(--bs-danger-border-subtle); }
        .diff-zero { background: var(--bs-secondary-bg-subtle); color: #495057; border: 1px solid #e9ecef; }

        #kategori-table tbody tr:hover { background-color: #f8fafc; }

        /* KPI metric cards */
        .metric-card { border: 1px solid var(--cok-border); border-radius: 12px; background: var(--cok-surface); }
        .metric-card .label { color: var(--cok-text-muted); font-size: .85rem; }
        .metric-card .value { font-size: 1.25rem; font-weight: 700; }
        .kpi-up { color: #157347; }
        .kpi-down { color: #b02a37; }
        .kpi-zero { color: #6c757d; }

        #loadingOverlay {
            position: fixed; inset: 0; background-color: rgba(255,255,255,.6); backdrop-filter: blur(6px);
            z-index: 9999; display: none; justify-content: center; align-items: center; flex-direction: column; gap: .75rem;
        }
        #loadingOverlay span { color: var(--cok-text-muted); font-weight: 600; }
    </style>
@endpush

@section('content')
   <div id="loadingOverlay">
        <div class="spinner-border text-primary" role="status" style="width: 3.5rem; height: 3.5rem;"></div>
        <span>Memuat data...</span>
    </div>
    <div class="container-fluid">
        <div class="card custom-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="page-heading">
                        <span class="icon"><i class="bi bi-pie-chart"></i></span>
                        <div>
                            <h5 class="fw-bold mb-0">Ringkasan Pendapatan per Kategori</h5>
                            <small class="text-muted">Pantau tren pendapatan dan bandingkan antar periode dengan cepat</small>
                        </div>
                    </div>
                    <a href="/performa-produk/compare-sales" class="btn btn-soft-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                </div>

                {{-- Pie Chart --}}
                <div class="mb-4">
                    <canvas id="kategoriPieChart" style="max-height: 300px;"></canvas>
                </div>
                {{-- KPI Metrics --}}
                <div id="metricsRow" class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 h-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check2-all text-success"></i>
                                <span class="label">Consistent Growers (P1→P2→P3)</span>
                            </div>
                            <div class="value mt-1" id="kpi-consistent-count">-</div>
                            <div class="label" id="kpi-consistent-share">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 h-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-speedometer2 text-primary"></i>
                                <span class="label">MTD vs Bulan Lalu</span>
                            </div>
                            <div class="value mt-1"><span id="kpi-mtd-pct" class="kpi-zero">-</span></div>
                            <div class="label" id="kpi-mtd-detail">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 h-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-award text-warning"></i>
                                <span class="label">Top Kategori</span>
                            </div>
                            <div class="value mt-1" id="kpi-topcat-name">-</div>
                            <div class="label" id="kpi-topcat-value">-</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 h-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-pie-chart text-info"></i>
                                <span class="label">Kontribusi Top 3</span>
                            </div>
                            <div class="value mt-1" id="kpi-top3">-</div>
                        </div>
                    </div>
                </div>
                {{-- Trend Chart --}}
                <div class="mb-4">
                    <canvas id="trendChart" style="max-height: 260px;"></canvas>
                </div>
                <hr class="my-4 border border-2 border-dark rounded-pill">
                <h1 class="fw-bold mb-0 text-center" id="namaToko">Nama Toko</h1>
                <h5 class="text-muted text-center" id="namaChannel">Channel</h5>

                <hr>
                <div class="toolbar mb-3">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label for="toko" class="form-label mb-1">Toko</label>
                            <select class="form-select" aria-label="Pilih toko" id="toko" onchange="getDataHere()">
                                <option value="semua">Semua Toko</option>
                                @foreach ($shops as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label for="channel" class="form-label mb-1">Channel</label>
                            <select class="form-select" aria-label="Pilih channel" id="channel" onchange="changeChannel()">
                                <option value="semua">Semua Channel</option>
                                <option value="shopee">Shopee</option>
                                <option value="tiktok">Tiktok</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="table-container">
                    <table class="table table-hover table-sm align-middle" id="kategori-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center" style="min-width: 150px;">Kategori</th>
                                <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center" style="min-width: 100px;">p4-P1</th>
                                <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center" style="min-width: 100px;">p1-P1</th>
                                <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center" style="min-width: 100px;">P1-P2</th>
                                <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center" style="min-width: 100px;">p2-P2</th>
                                <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center" style="min-width: 100px;">P2-P3</th>
                                <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center" style="min-width: 100px;">p3-P3</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Chart.js dan DataTables JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Inisialisasi DataTable
        getDataHere();

        function changeChannel() {

            generateTable();
        }
        // Load data untuk Pie Chart via AJAX
        function getDataHere() {
            // Menampilkan loading overlay
             $('#loadingOverlay').css('display', 'flex');

            $.ajax({
                url: '/performa-produk/compare-sales/kategori',
                method: 'GET',
                data: {
                    toko: $('#toko').val()
                },
                dataType: 'json',
                success: function(res) {
                    // generateTable(res.kategoriData)
                    window.cachedKategoriData = res.kategoriData;
                    window.cachedLabels = res.labels;
                    window.cachedDataTotal = res.dataTotal;
                    window.cachedDataShopee = res.dataShopee;
                    window.cachedDataTiktok = res.dataTiktok;

                    generateTable();
                    genereatePie();
                    updateKpis();
                    renderTrend();

                    // Menghilangkan loading overlay
                    $('#loadingOverlay').css('display', 'none');
                },
                error: function(err) {
                    console.error('Gagal memuat data pie chart:', err);

                    // Menghilangkan loading overlay
                    $('#loadingOverlay').css('display', 'none');
                }
            });
        }

        function genereatePie() {
            if (window.myPieChart) {
                window.myPieChart.destroy();
            }

            var channel = $('#channel').val();
            var res = {
                labels: window.cachedLabels,
                data: channel == 'semua' ? window.cachedDataTotal : channel == 'shopee' ? window.cachedDataShopee :
                    window.cachedDataTiktok
            };

            const ctx = document.getElementById('kategoriPieChart').getContext('2d');
            const bgColors = res.labels.map(() => {
                const r = Math.floor(Math.random() * 200) + 55;
                const g = Math.floor(Math.random() * 200) + 55;
                const b = Math.floor(Math.random() * 200) + 55;
                return `rgba(${r},${g},${b},0.7)`;
            });
            window.myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: res.labels,
                    datasets: [{
                        data: res.data,
                        backgroundColor: bgColors,
                        borderColor: bgColors.map(c => c.replace('0.7',
                            '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }



        function generateTable() {
            genereatePie()
            var tokoName = $('#toko option:selected').text()
            var channelName = $('#channel option:selected').text()

            $('#namaToko').text(tokoName)
            $('#namaChannel').text(channelName)

            var channel = $('#channel').val()
            // refresh KPI + trend when channel changes
            updateKpis();
            renderTrend();

            var data = window.cachedKategoriData;
            $('#kategori-table tbody').empty();

            function formatRupiah(value) {
                return 'Rp ' + (value || 0).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).replace(/\B(?=(\d{3})+(?!\d))/g, '.').replace(/\.00$/, '');
            }

            function renderSelisih(diff, pct, total) {

                let icon = diff > 0 ? '▲' : diff < 0 ? '▼' : '';
                let color = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : '';
                return `<span class="${color}">
                    ${icon} ${pct !== null ? pct.toFixed(2) : '0.00'}%
                    (Rp ${Math.abs(diff).toLocaleString('id-ID')})
                </span>`;

            }

            // Enhanced badge-style diff renderer (override)
            function renderSelisih(diff, pct) {
                const isUp = diff > 0;
                const isDown = diff < 0;
                const cls = isUp ? 'diff-up' : isDown ? 'diff-down' : 'diff-zero';
                const icon = isUp ? 'arrow-up-right' : isDown ? 'arrow-down-right' : 'dash-lg';
                const pctText = (pct !== null ? pct.toFixed(2) : '0.00') + '%';
                const amtText = 'Rp ' + Math.abs(diff).toLocaleString('id-ID');
                return `<span class=\"badge diff-badge ${cls}\"><i class=\"bi bi-${icon} me-1\"></i>${pctText} <span class=\"text-muted ms-1\">(${amtText})</span></span>`;
            }

            let sum = {
                1: {
                    pa: 0,
                    pb: 0,
                    diff: 0
                },
                2: {
                    pa: 0,
                    pb: 0,
                    diff: 0
                },
                3: {
                    pa: 0,
                    pb: 0,
                    diff: 0
                },
                4: {
                    pa: 0,
                    pb: 0,
                    diff: 0
                },
                5: {
                    pa: 0,
                    pb: 0,
                    diff: 0
                },
                6: {
                    pa: 0,
                    pb: 0,
                    diff: 0
                },
            };


            //         // Render data
            $.each(data, function(index, item) {

                // Periode 1
                let prev_p1 = channel == 'semua' ? item.prev_pendapatan_per_1 : channel == 'shopee' ? item
                    .prev_pendapatan_shopee_per_1 : item.prev_pendapatan_tiktok_per_1;

                let prev_p2 = channel == 'semua' ? item.prev_pendapatan_per_2 : channel == 'shopee' ? item
                    .prev_pendapatan_shopee_per_2 : item.prev_pendapatan_tiktok_per_2;

                let prev_p3 = channel == 'semua' ? item.prev_pendapatan_per_3 : channel == 'shopee' ? item
                    .prev_pendapatan_shopee_per_3 : item.prev_pendapatan_tiktok_per_3;

                let prev_p4 = channel == 'semua' ? item.prev_pendapatan_per_4 : channel == 'shopee' ? item
                    .prev_pendapatan_shopee_per_4 : item.prev_pendapatan_tiktok_per_4;

                let p1 = channel == 'semua' ? item.pendapatan_per_1 : channel == 'shopee' ? item
                    .pendapatan_shopee_per_1 : item.pendapatan_tiktok_per_1;

                let p2 = channel == 'semua' ? item.pendapatan_per_2 : channel == 'shopee' ? item
                    .pendapatan_shopee_per_2 : item.pendapatan_tiktok_per_2;

                let p3 = channel == 'semua' ? item.pendapatan_per_3 : channel == 'shopee' ? item
                    .pendapatan_shopee_per_3 : item.pendapatan_tiktok_per_3;

                let p4 = channel == 'semua' ? item.pendapatan_per_4 : channel == 'shopee' ? item
                    .pendapatan_shopee_per_4 : item.pendapatan_tiktok_per_4;



                // Selisih & Persen
                let d1 = p1 - prev_p3;
                let d2 = p1 - prev_p1;
                let d3 = p2 - p1;
                let d4 = p2 - prev_p2;
                let d5 = p3 - p2;
                let d6 = p3 - prev_p3;

                let pct1 = prev_p3 > 0 ? (d1 / prev_p3) * 100 : (d1 !== 0 ? 100 : 0);
                let pct2 = prev_p1 > 0 ? (d2 / prev_p1) * 100 : (d2 !== 0 ? 100 : 0);
                let pct3 = p1 > 0 ? (d3 / p1) * 100 : (d3 !== 0 ? 100 : 0);
                let pct4 = prev_p2 > 0 ? (d4 / prev_p2) * 100 : (d4 !== 0 ? 100 : 0);
                let pct5 = p2 > 0 ? (d5 / p2) * 100 : (d5 !== 0 ? 100 : 0);
                let pct6 = prev_p3 > 0 ? (d6 / prev_p3) * 100 : (d6 !== 0 ? 100 : 0);

                const row = `
                        <tr>
                            <td class="text-center" data-order="${index + 1}">${index + 1}</td>
                            <td data-order="${(item.nama_kategori || '').toString().toLowerCase()}"><a href="/performa-produk/compare-sales/kategori/${item.id}"
                                                class="text-decoration-none">${item.nama_kategori}</a></td>
                            <td class="num" data-order="${parseFloat(prev_p3) || 0}">${formatRupiah(prev_p3)}</td>
                            <td class="num" data-order="${parseFloat(p1) || 0}">${formatRupiah(p1)}</td>
                            <td class="text-center" data-order="${parseFloat(d1) || 0}">${renderSelisih(d1, pct1)}</td>
                            <td class="num" data-order="${parseFloat(prev_p1) || 0}">${formatRupiah(prev_p1)}</td>
                            <td class="num" data-order="${parseFloat(p1) || 0}">${formatRupiah(p1)}</td>
                            <td class="text-center" data-order="${parseFloat(d2) || 0}">${renderSelisih(d2, pct2)}</td>
                            <td class="num" data-order="${parseFloat(p1) || 0}">${formatRupiah(p1)}</td>
                            <td class="num" data-order="${parseFloat(p2) || 0}">${formatRupiah(p2)}</td>
                            <td class="text-center" data-order="${parseFloat(d3) || 0}">${renderSelisih(d3, pct3)}</td>
                            <td class="num" data-order="${parseFloat(prev_p2) || 0}">${formatRupiah(prev_p2)}</td>
                            <td class="num" data-order="${parseFloat(p2) || 0}">${formatRupiah(p2)}</td>
                            <td class="text-center" data-order="${parseFloat(d4) || 0}">${renderSelisih(d4, pct4)}</td>
                            <td class="num" data-order="${parseFloat(p2) || 0}">${formatRupiah(p2)}</td>
                            <td class="num" data-order="${parseFloat(p3) || 0}">${formatRupiah(p3)}</td>
                            <td class="text-center" data-order="${parseFloat(d5) || 0}">${renderSelisih(d5, pct5)}</td>
                            <td class="num" data-order="${parseFloat(prev_p3) || 0}">${formatRupiah(prev_p3)}</td>
                            <td class="num" data-order="${parseFloat(p3) || 0}">${formatRupiah(p3)}</td>
                            <td class="text-center" data-order="${parseFloat(d6) || 0}">${renderSelisih(d6, pct6)}</td>
                            
                        </tr>
                    `;

                sum[1].pa += parseFloat(prev_p3) || 0;
                sum[1].pb += parseFloat(p1) || 0;
                sum[1].diff += parseFloat(d1) || 0;

                sum[2].pa += parseFloat(prev_p1) || 0;
                sum[2].pb += parseFloat(p1) || 0;
                sum[2].diff += parseFloat(d2) || 0;

                sum[3].pa += parseFloat(p1) || 0;
                sum[3].pb += parseFloat(p2) || 0;
                sum[3].diff += parseFloat(d3) || 0;

                sum[4].pa += parseFloat(prev_p2) || 0;
                sum[4].pb += parseFloat(p2) || 0;
                sum[4].diff += parseFloat(d4) || 0;

                sum[5].pa += parseFloat(p2) || 0;
                sum[5].pb += parseFloat(p3) || 0;
                sum[5].diff += parseFloat(d5) || 0;

                sum[6].pa += parseFloat(prev_p3) || 0;
                sum[6].pb += parseFloat(p3) || 0;
                sum[6].diff += parseFloat(d6) || 0;

                $('#kategori-table tbody').append(row);
            });


            let tfoot = `<tr class="fw-semibold">
                <td colspan="2" class="text-end">Total:</td>`;
            for (let p = 1; p <= 6; p++) {
                tfoot += `
                  <td class=\"num\">${formatRupiah(sum[p].pa)}</td>
                  <td class=\"num\">${formatRupiah(sum[p].pb)}</td>`;
                if (p <= 8) {
                    tfoot += `<td class=\"num\">${formatRupiah(sum[p].diff)}</td>`;
                }
            }
            tfoot += '</tr>';
            $('#kategori-table tfoot').html(tfoot);

            // Inisialisasi atau refresh DataTable untuk sorting per kolom
            if ($.fn.dataTable && $.fn.dataTable.isDataTable('#kategori-table')) {
                $('#kategori-table').DataTable().destroy();
            }
            if ($.fn.dataTable) {
                $('#kategori-table').DataTable({
                    order: [],
                    paging: false,
                    info: false,
                    autoWidth: false,
                    language: { search: 'Cari:', zeroRecords: 'Tidak ada data' }
                });
            }
        }

        // ================= Metrics & Trend =================
        function computeTotalsAndInsights() {
            const channel = $('#channel').val();
            const data = window.cachedKategoriData || [];
            let totals = { p1: 0, p2: 0, p3: 0, prev1: 0, prev2: 0, prev3: 0 };
            let top = { name: '-', value: 0 };
            const arr = [];
            let consistentCount = 0; let consistentSumP3 = 0;
            data.forEach(item => {
                const p1 = channel == 'semua' ? item.pendapatan_per_1 : channel == 'shopee' ? item.pendapatan_shopee_per_1 : item.pendapatan_tiktok_per_1;
                const p2 = channel == 'semua' ? item.pendapatan_per_2 : channel == 'shopee' ? item.pendapatan_shopee_per_2 : item.pendapatan_tiktok_per_2;
                const p3 = channel == 'semua' ? item.pendapatan_per_3 : channel == 'shopee' ? item.pendapatan_shopee_per_3 : item.pendapatan_tiktok_per_3;
                const prev1 = channel == 'semua' ? item.prev_pendapatan_per_1 : channel == 'shopee' ? item.prev_pendapatan_shopee_per_1 : item.prev_pendapatan_tiktok_per_1;
                const prev2 = channel == 'semua' ? item.prev_pendapatan_per_2 : channel == 'shopee' ? item.prev_pendapatan_shopee_per_2 : item.prev_pendapatan_tiktok_per_2;
                const prev3 = channel == 'semua' ? item.prev_pendapatan_per_3 : channel == 'shopee' ? item.prev_pendapatan_shopee_per_3 : item.prev_pendapatan_tiktok_per_3;
                const n1 = parseFloat(p1) || 0, n2 = parseFloat(p2) || 0, n3 = parseFloat(p3) || 0;
                const nprev1 = parseFloat(prev1) || 0, nprev2 = parseFloat(prev2) || 0, nprev3 = parseFloat(prev3) || 0;
                totals.p1 += n1; totals.p2 += n2; totals.p3 += n3; totals.prev1 += nprev1; totals.prev2 += nprev2; totals.prev3 += nprev3;
                arr.push({ name: item.nama_kategori, v: n3 });
                if (n3 > top.value) top = { name: item.nama_kategori, value: n3 };
                if (n2 >= n1 && n3 >= n2) { consistentCount++; consistentSumP3 += n3; }
            });
            const sorted = arr.sort((a,b)=>b.v-a.v);
            const top3 = sorted.slice(0,3).reduce((s,i)=>s+i.v,0);
            const contribTop3 = totals.p3 > 0 ? (top3 / totals.p3) * 100 : 0;
            // Month-to-date vs last month (sum of P1..P3)
            const mtd = totals.p1 + totals.p2 + totals.p3;
            const prevMonth = totals.prev1 + totals.prev2 + totals.prev3;
            const mtdPct = prevMonth > 0 ? ((mtd - prevMonth) / prevMonth) * 100 : (mtd !== 0 ? 100 : 0);
            const mtdAmt = mtd - prevMonth;
            const consistentShare = totals.p3 > 0 ? (consistentSumP3 / totals.p3) * 100 : 0;
            return { totals, top, contribTop3, mtdPct, mtdAmt, totalCategories: sorted.length, consistentCount, consistentShare };
        }

        function updateKpis() {
            const { totals, top, contribTop3, mtdPct, mtdAmt, totalCategories, consistentCount, consistentShare } = computeTotalsAndInsights();
            const formatRupiah = (v)=> 'Rp ' + (v||0).toLocaleString('id-ID');
            $('#kpi-consistent-count').text(consistentCount + ' Kategori');
            $('#kpi-consistent-share').text( (consistentShare||0).toFixed(1) + '% dari total P3');
            const mtdEl = $('#kpi-mtd-pct');
            mtdEl.text((mtdPct||0).toFixed(2) + '%');
            mtdEl.removeClass('kpi-up kpi-down kpi-zero').addClass(mtdPct>0?'kpi-up':mtdPct<0?'kpi-down':'kpi-zero');
            $('#kpi-mtd-detail').text(((mtdAmt>=0?'+':'') + formatRupiah(mtdAmt)) + ' vs bulan lalu');
            $('#kpi-topcat-name').text(top.name || '-');
            $('#kpi-topcat-value').text(formatRupiah(top.value || 0));
            $('#kpi-top3').text((contribTop3||0).toFixed(1) + '%');
        }

        let trendChart;
        function renderTrend() {
            const { totals } = computeTotalsAndInsights();
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;
            if (trendChart) { trendChart.destroy(); }
            trendChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['P1 (current)','P2 (current)','P3 (current)'],
                    datasets: [{
                        label: 'Total Pendapatan',
                        data: [totals.p1, totals.p2, totals.p3],
                        borderColor: 'rgba(108,117,125,0.9)',
                        backgroundColor: 'rgba(108,117,125,0.15)',
                        fill: true,
                        tension: 0.25,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(108,117,125,0.9)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    </script>
@endpush
