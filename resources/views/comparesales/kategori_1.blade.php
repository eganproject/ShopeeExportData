@extends('layouts.main')

{{-- Styles untuk DataTables dan Chart.js --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card custom-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Ringkasan Pendapatan per Kategori</h5>
                    <a href="/performa-produk/compare-sales" class="btn btn-outline-secondary btn-modern">Kembali</a>
                </div>

                {{-- Pie Chart --}}
                <div class="mb-4">
                    <canvas id="kategoriPieChart" style="max-height: 300px;"></canvas>
                </div>
                <hr class="my-4 border border-2 border-dark rounded-pill">
                <div class="row mb-4">
                    <div class="col-lg-4 d-flex justify-content-between align-items-center  gap-4">
                        <label for="toko" class="form-label">Toko</label>
                        <select class="form-select" aria-label="Default select example" id="toko"
                            onchange="getDataHere()">
                            <option value="semua">Semua</option>
                            @foreach ($shops as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-lg-4 d-flex justify-content-between align-items-center gap-4">
                        <label for="channel" class="form-label">Channel</label>
                        <select class="form-select" aria-label="Default select example" id="channel"
                            onchange="changeChannel()">
                            <option value="semua">Semua</option>
                            <option value="shopee">Shopee</option>
                            <option value="tiktok">Tiktok</option>

                        </select>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="kategori-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Periode 4 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">p4-P1</th>
                                <th class="text-center">Periode 1 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">p1-P1</th>
                                <th class="text-center">Periode 1 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">P1-P2</th>
                                <th class="text-center">Periode 2 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">p2-P2</th>
                                <th class="text-center">Periode 2 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">P2-P3</th>
                                <th class="text-center">Periode 3 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">p3-P3</th>
                                <th class="text-center">Periode 3 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">Periode 4 <br> <span class="text-primary">(current)</span></th>
                                <th class="text-center">P3-P4</th>
                                <th class="text-center">Periode 4 <br> <span class="text-warning">(-month)</span></th>
                                <th class="text-center">Periode 4 <br> <span class="text-primary">(current)</span></th>
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

                    generateTable();

                    if (window.myPieChart) {
                        window.myPieChart.destroy();
                    }

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
                },
                error: function(err) {
                    console.error('Gagal memuat data pie chart:', err);
                }
            });
        }



        function generateTable() {
            var channel = $('#channel').val()

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
                            <td><a href="/performa-produk/compare-sales/kategori/${item.id}"
                                                class="text-decoration-none">${item.nama_kategori}</a></td>
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

                $('#kategori-table tbody').append(row);
            });


            let tfoot = `<tr class="fw-semibold">
        <td colspan="2" class="text-end">Total:</td>`;
            for (let p = 1; p <= 8; p++) {
                tfoot += `
                  <td>${formatRupiah(sum[p].pa)}</td>
                  <td>${formatRupiah(sum[p].pb)}</td>`;
                if (p <= 8) {
                    tfoot += `<td>${formatRupiah(sum[p].diff)}</td>`;
                }
            }
            tfoot += '</tr>';
            $('#kategori-table tfoot').html(tfoot);

        }
    </script>
@endpush
