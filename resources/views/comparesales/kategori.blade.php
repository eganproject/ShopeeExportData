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
                <div class="mb-4">
                    <div class="d-flex justify-content-between gap-4">
                        <div class="col-3">
                            <label for="toko" class="form-label">Toko</label>
                            <select class="form-select" aria-label="Default select example" id="toko"
                                onchange="getDataHere()">
                                <option value="semua">Semua</option>
                                @foreach ($shops as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="kategori-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center">No</th>
                                <th rowspan="2" class="text-center">Kategori</th>
                                <th colspan="3" class="text-center">Periode 1</th>
                                <th rowspan="2" class="text-center">Selisih P1-P2</th>
                                <th colspan="3" class="text-center">Periode 2</th>
                                <th rowspan="2" class="text-center">Selisih P2-P3</th>
                                <th colspan="3" class="text-center">Periode 3</th>
                                <th rowspan="2" class="text-center">Selisih P3-P4</th>
                                <th colspan="3" class="text-center">Periode 4</th>
                            </tr>
                            <tr>
                                <th class="text-center">Shopee</th>
                                <th class="text-center">Tiktok</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Shopee</th>
                                <th class="text-center">Tiktok</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Shopee</th>
                                <th class="text-center">Tiktok</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Shopee</th>
                                <th class="text-center">Tiktok</th>
                                <th class="text-center">Total</th>
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
                    generateTable(res.kategoriData)

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



        function generateTable(data) {
            $('#kategori-table tbody').empty();

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

                // Selisih & Persen
                let d12 = tot2 - tot1;
                let d23 = tot3 - tot2;
                let d34 = tot4 - tot3;
                let pct12 = tot1 > 0 ? (d12 / tot1) * 100 : null;
                let pct23 = tot2 > 0 ? (d23 / tot2) * 100 : null;
                let pct34 = tot3 > 0 ? (d34 / tot3) * 100 : null;

                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td><a href="/performa-produk/compare-sales/kategori/${item.id}"
                                            class="text-decoration-none">${item.nama_kategori}</a></td>
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

                $('#kategori-table tbody').append(row);
            });


            let tfoot = `<tr class="fw-semibold">
    <td colspan="2" class="text-end">Total:</td>`;
            for (let p = 1; p <= 4; p++) {
                tfoot += `<td>${formatRupiah(sum[p].s)}</td>
              <td>${formatRupiah(sum[p].t)}</td>
              <td>${formatRupiah(sum[p].tot)}</td>`;
                if (p < 4) {
                    tfoot += `<td>${formatRupiah(sum[p].diff)}</td>`;
                }
            }
            tfoot += '</tr>';
            $('#kategori-table tfoot').html(tfoot);

        }
    </script>
@endpush
