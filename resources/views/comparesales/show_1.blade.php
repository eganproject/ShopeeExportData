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
                            <h5 class="fw-semibold mb-3 text-center">Persentase Total per Platform</h5>
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
                            <h5 class="fw-semibold mb-3 text-center">Top 10 SKU Berdasarkan Periode</h5>
                            <canvas id="barTop10" style="max-height:400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3 text-center">Grafik Penjualan</h5>
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
                        <h5 class="fw-semibold mb-3 text-center">Sub Kategori</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="subkategori-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Kategori</th>
                                        <th class="text-center">Periode 4 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p4-P1</th>
                                        <th class="text-center">Periode 1 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p1-P1</th>
                                        <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">P1-P2</th>
                                        <th class="text-center">Periode 2 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p2-P2</th>
                                        <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">P2-P3</th>
                                        <th class="text-center">Periode 3 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p3-P3</th>
                                        <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">Periode 4 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">P3-P4</th>
                                        <th class="text-center">Periode 4 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 4 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p4-P4</th>
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
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="kategori-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Kategori</th>
                                        <th class="text-center">Periode 4 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p4-P1</th>
                                        <th class="text-center">Periode 1 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p1-P1</th>
                                        <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">P1-P2</th>
                                        <th class="text-center">Periode 2 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p2-P2</th>
                                        <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">P2-P3</th>
                                        <th class="text-center">Periode 3 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p3-P3</th>
                                        <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">Periode 4 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">P3-P4</th>
                                        <th class="text-center">Periode 4 <br> <span class="text-warning">(-month)</span>
                                        </th>
                                        <th class="text-center">Periode 4 <br> <span class="text-primary">(current)</span>
                                        </th>
                                        <th class="text-center">p4-P4</th>
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
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

                    getChart(response.kategori);
                    grafikPenjualanChart(response.labels, response.data);
                    putTable(response.kategori, 'kategori-table')
                    getSubKategori();
                },
                error: function(xhr) {
                    console.error('Error fetching data:', xhr);
                }
            });
        }

        function getSubKategori() {
            console.log()
            $.ajax({
                url: '/performa-produk/compare-sales/kategori/get-sub-kategori/' + {{ $kategori->id }},
                method: 'GET',
                data: {
                    id: {{ $kategori->id }},
                    shop_id: $('#toko').val()
                },
                async: false,
                success: function(response) {

                    putTable(response.subkategori, 'subkategori-table')
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
            const sortedAll = [...data].sort((a, b) => (b.pendapatan_per_2 + b.pendapatan_per_1) - (a
                .pendapatan_per_2 + a.pendapatan_per_1)).slice(0, 10);
            window.barTop10 = new Chart(document.getElementById('barTop10'), {
                type: 'bar',
                data: {
                    labels: sortedAll.map(i => i.sku),
                    datasets: [{
                            label: 'Pendapatan Periode 1',
                            data: sortedAll.map(i => i.pendapatan_per_1),
                            backgroundColor: 'rgba(255, 99, 132, 0.8)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Pendapatan Periode 2',
                            data: sortedAll.map(i => i.pendapatan_per_2),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Pendapatan Periode 3',
                            data: sortedAll.map(i => i.pendapatan_per_3),
                            backgroundColor: 'rgba(10, 77, 190, 0.8)',
                            borderColor: 'rgba(10, 77, 190, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Pendapatan Periode 4',
                            data: sortedAll.map(i => i.pendapatan_per_4),
                            backgroundColor: 'rgba(50, 99, 190, 0.8)',
                            borderColor: 'rgba(50, 99, 190, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Pendapatan Periode 5',
                            data: sortedAll.map(i => i.pendapatan_per_5),
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

            let sum = {
                1: {
                    s: 0,
                    t: 0,
                    tot: 0,
                    diff: 0
                },
                2: {
                    s: 0,
                    t: 0,
                    tot: 0,
                    diff: 0
                },
                3: {
                    s: 0,
                    t: 0,
                    tot: 0,
                    diff: 0
                },
                4: {
                    s: 0,
                    t: 0,
                    tot: 0
                },
                5: {
                    s: 0,
                    t: 0,
                    tot: 0
                }

            };

            // Render data
            $.each(data, function(index, item) {

                // Periode 1
                let s1 = item.pendapatan_shopee_per_1 || 0;
                let t1 = item.pendapatan_tiktok_per_1 || 0;
                let tot1 = item.pendapatan_per_1 || (s1 + t1);

                // Periode 2
                let s2 = item.pendapatan_shopee_per_2 || 0;
                let t2 = item.pendapatan_tiktok_per_2 || 0;
                let tot2 = item.pendapatan_per_2 || (s2 + t2);

                // Periode 3
                let s3 = item.pendapatan_shopee_per_3 || 0;
                let t3 = item.pendapatan_tiktok_per_3 || 0;
                let tot3 = item.pendapatan_per_3 || (s3 + t3);

                // Periode 4
                let s4 = item.pendapatan_shopee_per_4 || 0;
                let t4 = item.pendapatan_tiktok_per_4 || 0;
                let tot4 = item.pendapatan_per_4 || (s4 + t4);

                // Periode 5
                let s5 = item.pendapatan_shopee_per_5 || 0;
                let t5 = item.pendapatan_tiktok_per_5 || 0;
                let tot5 = item.pendapatan_per_5 || (s5 + t5);

                // Selisih & Persen
                let d12 = tot2 - tot1;
                let d23 = tot3 - tot2;
                let d34 = tot4 - tot3;
                let d45 = tot5 - tot4;
                let pct12 = tot1 > 0 ? (d12 / tot1) * 100 : null;
                let pct23 = tot2 > 0 ? (d23 / tot2) * 100 : null;
                let pct34 = tot3 > 0 ? (d34 / tot3) * 100 : null;
                let pct45 = tot4 > 0 ? (d45 / tot4) * 100 : null;

                const row = `
                    <tr>
                        <td>${index + 1}</td>
                     ${table === 'kategori-table' ? '<td >'+item.sku+'</td>' : ''}   
                        <td>${table === 'kategori-table' ? item.nama_produk : '<a href="/performa-produk/compare-sales/kategori/'+item.id+'">'+item.nama_kategori+'</a>'}</td>
                        <td>${formatRupiah(s1)}</td>
                        <td>${formatRupiah(t1)}</td>
                        <td>${formatRupiah(tot1)}</td>
                        <td>${renderSelisih(d12, pct12, tot1)}</td>
                        <td>${formatRupiah(s2)}</td>
                        <td>${formatRupiah(t2)}</td>
                        <td>${formatRupiah(tot2)}</td>
                        <td>${renderSelisih(d23, pct23, tot2)}</td>
                        <td>${formatRupiah(s3)}</td>
                        <td>${formatRupiah(t3)}</td>
                        <td>${formatRupiah(tot3)}</td>
                        <td>${renderSelisih(d34, pct34, tot3)}</td>
                        <td>${formatRupiah(s4)}</td>
                        <td>${formatRupiah(t4)}</td>
                        <td>${formatRupiah(tot4)}</td>
                        <td>${renderSelisih(d45, pct45, tot4)}</td>
                        <td>${formatRupiah(s5)}</td>
                        <td>${formatRupiah(t5)}</td>
                        <td>${formatRupiah(tot5)}</td>
                    </tr>
                `;


                sum[1].s += parseFloat(s1) || 0;
                sum[1].t += parseFloat(t1) || 0;
                sum[1].tot += parseFloat(tot1) || 0;
                sum[1].diff += parseFloat(d12) || 0;

                sum[2].s += parseFloat(s2) || 0;
                sum[2].t += parseFloat(t2) || 0;
                sum[2].tot += parseFloat(tot2) || 0;
                sum[2].diff += parseFloat(d23) || 0;

                sum[3].s += parseFloat(s3) || 0;
                sum[3].t += parseFloat(t3) || 0;
                sum[3].tot += parseFloat(tot3) || 0;
                sum[3].diff += parseFloat(d34) || 0;

                sum[4].s += parseFloat(s4) || 0;
                sum[4].t += parseFloat(t4) || 0;
                sum[4].tot += parseFloat(tot4) || 0;

                sum[5].s += parseFloat(s5) || 0;
                sum[5].t += parseFloat(t5) || 0;
                sum[5].tot += parseFloat(tot5) || 0;

                $(`#${table} tbody`).append(row);
            });


            let tfoot = `<tr class="fw-semibold">
    <td colspan="${table === 'kategori-table' ? 3 : 2}" class="text-end">Total:</td>`;
            for (let p = 1; p <= 5; p++) {
                tfoot += `<td>${formatRupiah(sum[p].s)}</td>
              <td>${formatRupiah(sum[p].t)}</td>
              <td>${formatRupiah(sum[p].tot)}</td>`;
                if (p < 5) {
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
    </script> --}}
@endpush
