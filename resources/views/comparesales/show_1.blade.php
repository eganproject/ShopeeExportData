@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Row 1: Pie Charts Periode 1 & 2 --}}
        <div class="row g-4 mb-4">
            <a href="/performa-produk/compare-sales/kategori" class="btn btn-outline-secondary btn-modern">Kembali</a>
            <div class="row g-4 mb-4 d-flex justify-content-center">
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold text-center text-primary mb-4">{{ $kategori->nama_kategori }}</h1>
                    <div class="mb-4">
                        <div class="d-flex justify-content-center gap-4 mb-4">
                            <div class="col-3">
                                <label for="toko" class="form-label">Toko</label>
                                <select class="form-select" aria-label="Default select example" id="toko"
                                    onchange="getData()">
                                    <option value="semua">Semua</option>
                                    @foreach ($shops as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-4">
                            <div class="col-3">
                                <label for="channel" class="form-label">Channel</label>
                                <select class="form-select" aria-label="Default select example" id="channel"
                                    onchange="changeChannel()">
                                    <option value="semua">Semua Channel</option>
                                    <option value="shopee">Shopee</option>
                                    <option value="tiktok">Tiktok</option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <h1 class="fw-bold mb-0 text-center" id="namaToko">Nama Toko</h1>
                    <h5 class="text-muted text-center" id="namaChannel">Channel</h5>
                    <hr class="my-4 border border-2 border-dark rounded-pill">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="fw-semibold text-center">Persentase Total per Platform</h5>

                            <p class="fw-bold mb-3 text-muted text-center">Current Period</p>
                            <canvas id="piePlatform" style="max-height:250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Row 2: Bar Chart Top 10 SKU Periode 2 --}}
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="fw-semibold text-center">Top 10 SKU Berdasarkan Periode</h5>
                            <p class="fw-bold mb-3 text-muted text-center">Current Period</p>
                            <canvas id="barTop10" style="max-height:400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="fw-bold text-center">Grafik Penjualan</h5>
                            <p class="fw-bold mb-3 text-muted text-center">Current Period</p>
                            <div class="chart-container">
                                <canvas id="revenueChart" style="height:400px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <h2 class="fw-semibold mb-3 text-center">Sub Kategori</h2>
                        <h4 class="fw-bold mb-0 text-center" id="sub_namaToko">Nama Toko</h4>
                        <h5 class="text-muted text-center" id="sub_namaChannel">Channel</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="subkategori-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center" style="min-width: 150px;">Kategori</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p4-P1</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p1-P1</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">P1-P2</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p2-P2</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">P2-P3</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p3-P3</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">P3-P4</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p4-P4</th>
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
        </div>

        {{-- Row 3: Detail Table dengan DataTables --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Detail Pendapatan per SKU</h5>
                        <h4 class="fw-bold mb-0 text-center" id="kat_namaToko">Nama Toko</h4>
                        <h5 class="text-muted text-center" id="kat_namaChannel">Channel</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="kategori-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">SKU</th>
                                        <th class="text-center" style="min-width: 200px;">Nama</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p4-P1</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p1-P1</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 1 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">P1-P2</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p2-P2</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 2 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">P2-P3</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p3-P3</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 3 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">P3-P4</th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">Periode 4 <br> <span
                                                class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center" style="min-width: 100px;">p4-P4</th>
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
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Data dari controller

        function getData() {
            $.ajax({
                url: '/performa-produk/compare-sales/kategori/detail-kategori/' + {{ $kategori->id }},
                method: 'GET',
                data: {
                    id: {{ $kategori->id }},
                    shop_id: $('#toko').val()
                },
                async: false,
                success: function(response) {
                    window.cachedKategoriData = response.kategori;
                    grafikPenjualanChart(response.labels, response.data);
                    getSubKategori();
                    changeChannel()
                },
                error: function(xhr) {
                    console.error('Error fetching data:', xhr);
                }
            });
        }

        function changeChannel() {
            var tokoName = $('#toko option:selected').text()
            var channelName = $('#channel option:selected').text()
            $('#namaToko').text(tokoName)
            $('#namaChannel').text(channelName)
            $('#sub_namaToko').text(tokoName)
            $('#sub_namaChannel').text(channelName)
            $('#kat_namaToko').text(tokoName)
            $('#kat_namaChannel').text(channelName)
            getChart(window.cachedKategoriData)
            putTable(window.cachedKategoriData, 'kategori-table')
            putTable(window.cachedSubKategoriData, 'subkategori-table')

            getDataGrafikChart()


        }

        function getDataGrafikChart() {
            $.ajax({
                url: '/performa-produk/compare-sales/kategori/get-data-grafik-chart/' + {{ $kategori->id }},
                method: 'GET',
                data: {
                    id: {{ $kategori->id }},
                    shop_id: $('#toko').val(),
                    channel: $('#channel').val()
                },
                async: false,
                success: function(response) {
                    grafikPenjualanChart(response.labels, response.data);
                },
                error: function(xhr) {
                    console.error('Error fetching data:', xhr);
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
                async: false,
                success: function(response) {
                    window.cachedSubKategoriData = response.subkategori;
                },
                error: function(xhr) {
                    console.error('Error fetching data:', xhr);
                }
            });
        }

        function getChart(data) {


            if (window.piePlatform && window.piePlatform instanceof Chart) piePlatform.destroy();
            if (window.barTop10 && window.barTop10 instanceof Chart) barTop10.destroy();

            // Labels kategori
            const categories = data.map(i => i.sku);
            // Data P1 & P2
            const valuesP1 = data.map(i => i.pendapatan_per_1);
            const valuesP2 = data.map(i => i.pendapatan_per_2);
            const valuesP3 = data.map(i => i.pendapatan_per_3);
            const valuesP4 = data.map(i => i.pendapatan_per_4);
            const valuesP5 = data.map(i => i.pendapatan_per_5);



            // piechart platform
            const totalS = data.reduce((sum, i) => sum + parseFloat(i.pendapatan_shopee_per_1 || 0) +
                parseFloat(i
                    .pendapatan_shopee_per_2 || 0) + parseFloat(i
                    .pendapatan_shopee_per_3 || 0) + parseFloat(i
                    .pendapatan_shopee_per_4 || 0) + parseFloat(i
                    .pendapatan_shopee_per_5 || 0), 0);
            const totalT = data.reduce((sum, i) => sum + parseFloat(i.pendapatan_tiktok_per_1 || 0) +
                parseFloat(i
                    .pendapatan_tiktok_per_2 || 0) + parseFloat(i
                    .pendapatan_tiktok_per_3 || 0) + parseFloat(i
                    .pendapatan_tiktok_per_4 || 0) + parseFloat(i
                    .pendapatan_tiktok_per_5 || 0), 0);

            const total = totalS + totalT;
            const persentaseS = (totalS / total) * 100;
            const persentaseT = (totalT / total) * 100;

            window.piePlatform = new Chart(document.getElementById('piePlatform'), {
                type: 'pie',
                data: {
                    labels: ['Shopee', 'Tiktok'],
                    datasets: [{
                        data: [totalS, totalT],
                        backgroundColor: ['#f5552dff', '#1b1b1bff']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                generateLabels: () => [{
                                        text: `Shopee: Rp ${totalS.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})} (${persentaseS.toFixed(2)}%)`,
                                        fillStyle: '#f5552dff',
                                        hidden: false,
                                        index: 0
                                    },
                                    {
                                        text: `Tiktok: Rp ${totalT.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})} (${persentaseT.toFixed(2)}%)`,
                                        fillStyle: '#1b1b1bff',
                                        hidden: false,
                                        index: 1
                                    }
                                ]
                            }
                        }
                    }
                }
            });

            // Bar Chart Top 10 SKU Berdasarkan Pendapatan Periode 1 & 2


            var channel = $('#channel').val();
            const sortedAll = [...data].sort((a, b) => {
                const getPendapatan = (item) => {
                    switch (channel) {
                        case 'shopee':
                            return item.pendapatan_shopee_per_2 + item.pendapatan_shopee_per_1;
                        case 'tiktok':
                            return item.pendapatan_tiktok_per_2 + item.pendapatan_tiktok_per_1;
                        default:
                            return item.pendapatan_per_2 + item.pendapatan_per_1;
                    }
                };
                return getPendapatan(b) - getPendapatan(a);
            }).slice(0, 10);
            window.barTop10 = new Chart(document.getElementById('barTop10'), {
                type: 'bar',
                data: {
                    labels: sortedAll.map(i => i.sku),
                    datasets: [{
                            label: 'Pendapatan Periode 1',
                            data: sortedAll.map(i => {
                                switch (channel) {
                                    case 'shopee':
                                        return i.pendapatan_shopee_per_1;
                                    case 'tiktok':
                                        return i.pendapatan_tiktok_per_1;
                                    default:
                                        return i.pendapatan_per_1;
                                }
                            }),
                            backgroundColor: 'rgba(255, 99, 132, 0.8)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Pendapatan Periode 2',
                            data: sortedAll.map(i => {
                                switch (channel) {
                                    case 'shopee':
                                        return i.pendapatan_shopee_per_2;
                                    case 'tiktok':
                                        return i.pendapatan_tiktok_per_2;
                                    default:
                                        return i.pendapatan_per_2;
                                }
                            }),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Pendapatan Periode 3',
                            data: sortedAll.map(i => {
                                switch (channel) {
                                    case 'shopee':
                                        return i.pendapatan_shopee_per_3;
                                    case 'tiktok':
                                        return i.pendapatan_tiktok_per_3;
                                    default:
                                        return i.pendapatan_per_3;
                                }
                            }),
                            backgroundColor: 'rgba(10, 77, 190, 0.8)',
                            borderColor: 'rgba(10, 77, 190, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Pendapatan Periode 4',
                            data: sortedAll.map(i => {
                                switch (channel) {
                                    case 'shopee':
                                        return i.pendapatan_shopee_per_4;
                                    case 'tiktok':
                                        return i.pendapatan_tiktok_per_4;
                                    default:
                                        return i.pendapatan_per_4;
                                }
                            }),
                            backgroundColor: 'rgba(50, 99, 190, 0.8)',
                            borderColor: 'rgba(50, 99, 190, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Pendapatan Periode 5',
                            data: sortedAll.map(i => {
                                switch (channel) {
                                    case 'shopee':
                                        return i.pendapatan_shopee_per_5;
                                    case 'tiktok':
                                        return i.pendapatan_tiktok_per_5;
                                    default:
                                        return i.pendapatan_per_5;
                                }
                            }),
                            backgroundColor: 'rgba(50, 99, 190, 0.8)',
                            borderColor: 'rgba(50, 99, 190, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: false
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Pendapatan (IDR)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        }
        // Inisialisasi DataTable

        function grafikPenjualanChart(thislabels, thisdata) {
            const chartLabels = thislabels;
            const chartData = thisdata;

            const canvasElement = document.getElementById('revenueChart');

            if (canvasElement) {
                const ctx = canvasElement.getContext('2d');

                // Hancurkan chart lama jika ada
                if (window.revenueChart && window.revenueChart instanceof Chart) {
                    window.revenueChart.destroy();
                }

                // Membuat gradient untuk background chart
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(0, 123, 255, 0.6)');
                gradient.addColorStop(1, 'rgba(0, 123, 255, 0.05)');

                window.revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Pendapatan',
                            data: chartData,
                            fill: true,
                            backgroundColor: gradient,
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 2,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(0, 123, 255, 1)',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#dee2e6'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                align: 'end',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Elemen canvas dengan ID "revenueChart" tidak ditemukan.');
            }
        }

        function putTable(data, table) {
            if ($.fn.DataTable.isDataTable(`#${table}`)) {
                $(`#${table}`).DataTable().destroy();
            }
            $(`#${table} tbody`).empty();

            function formatRupiah(value) {
                return 'Rp ' + (value || 0).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).replace(/\B(?=(\d{3})+(?!\d))/g, '.').replace(/\.00$/, '');
            }

            function renderSelisih(diff, pct, total) {
                if (total > 0) {
                    let icon = diff > 0 ? '▲' : diff < 0 ? '▼' : '';
                    let color = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : '';
                    return `<span class="${color}">
                    ${icon} ${pct !== null ? pct.toFixed(2) : '0.00'}%
                    (Rp ${Math.abs(diff).toLocaleString('id-ID')})
                </span>`;
                } else {
                    return '<span>—</span>';
                }
            }

            let channel = $('#channel').val();

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
                7: {
                    pa: 0,
                    pb: 0,
                    diff: 0
                },
                8: {
                    pa: 0,
                    pb: 0,
                    diff: 0
                },
            };

            // Render data
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
                let d1 = p1 - prev_p4;
                let d2 = p1 - prev_p1;
                let d3 = p2 - p1;
                let d4 = p2 - prev_p2;
                let d5 = p3 - p2;
                let d6 = p3 - prev_p3;
                let d7 = p4 - p3;
                let d8 = p4 - prev_p4;

                let pct1 = prev_p4 > 0 ? (d1 / prev_p4) * 100 : (d1 !== 0 ? 100 : 0);
                let pct2 = prev_p1 > 0 ? (d2 / prev_p1) * 100 : (d2 !== 0 ? 100 : 0);
                let pct3 = p1 > 0 ? (d3 / p1) * 100 : (d3 !== 0 ? 100 : 0);
                let pct4 = prev_p2 > 0 ? (d4 / prev_p2) * 100 : (d4 !== 0 ? 100 : 0);
                let pct5 = p2 > 0 ? (d5 / p2) * 100 : (d5 !== 0 ? 100 : 0);
                let pct6 = prev_p3 > 0 ? (d6 / prev_p3) * 100 : (d6 !== 0 ? 100 : 0);
                let pct7 = p3 > 0 ? (d7 / p3) * 100 : (d7 !== 0 ? 100 : 0);
                let pct8 = prev_p4 > 0 ? (d8 / prev_p4) * 100 : (d8 !== 0 ? 100 : 0);

                const row = `
                    <tr>
                        <td>${index + 1}</td>
                     ${table === 'kategori-table' ? '<td >'+item.sku+'</td>' : ''}   
                        <td>${table === 'kategori-table' ? item.nama_produk : '<a href="/performa-produk/compare-sales/kategori/'+item.id+'">'+item.nama_kategori+'</a>'}</td>
                       <td>${formatRupiah(prev_p4)}</td>
                            <td>${formatRupiah(p1)}</td>
                            <td>${renderSelisih(d1, pct1)}</td>
                            <td>${formatRupiah(prev_p1)}</td>
                            <td>${formatRupiah(p1)}</td>
                            <td>${renderSelisih(d2, pct2)}</td>
                            <td>${formatRupiah(p1)}</td>
                            <td>${formatRupiah(p2)}</td>
                            <td>${renderSelisih(d3, pct3)}</td>
                            <td>${formatRupiah(prev_p2)}</td>
                            <td>${formatRupiah(p2)}</td>
                            <td>${renderSelisih(d4, pct4)}</td>
                            <td>${formatRupiah(p2)}</td>
                            <td>${formatRupiah(p3)}</td>
                            <td>${renderSelisih(d5, pct5)}</td>
                            <td>${formatRupiah(prev_p3)}</td>
                            <td>${formatRupiah(p3)}</td>
                            <td>${renderSelisih(d6, pct6)}</td>
                            <td>${formatRupiah(p3)}</td>
                            <td>${formatRupiah(p4)}</td>
                            <td>${renderSelisih(d7, pct7)}</td>
                            <td>${formatRupiah(prev_p4)}</td>
                            <td>${formatRupiah(p4)}</td>
                            <td>${renderSelisih(d8, pct8)}</td>
                    </tr>
                `;


                sum[1].pa += parseFloat(prev_p4) || 0;
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

                sum[7].pa += parseFloat(p3) || 0;
                sum[7].pb += parseFloat(p4) || 0;
                sum[7].diff += parseFloat(d7) || 0;

                sum[8].pa += parseFloat(prev_p4) || 0;
                sum[8].pb += parseFloat(p4) || 0;
                sum[8].diff += parseFloat(d8) || 0;

                $(`#${table} tbody`).append(row);
            });


            let tfoot = `<tr class="fw-semibold">
    <td colspan="${table === 'kategori-table' ? 3 : 2}" class="text-end">Total:</td>`;
            for (let p = 1; p <= 8; p++) {
                tfoot += `
                  <td>${formatRupiah(sum[p].pa)}</td>
                  <td>${formatRupiah(sum[p].pb)}</td>`;
                if (p <= 8) {
                    tfoot += `<td>${formatRupiah(sum[p].diff)}</td>`;
                }
            }
            tfoot += '</tr>';
            $(`#${table} tfoot`).html(tfoot);




            $(`#${table}`).DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                buttons: ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo(`#${table}_wrapper .col-md-6:eq(0)`);
        }

        getData();
    </script>
@endpush
