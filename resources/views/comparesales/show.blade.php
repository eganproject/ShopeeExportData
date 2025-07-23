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
                <h1 class="display-5 fw-bold text-center text-primary mb-4">{{$kategori[0]->nama_kategori}}</h1>
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-3 text-center">Persentase Total per Platform</h5>
                            <canvas id="piePlatform" style="max-height:250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Komposisi Pendapatan Periode 1</h5>
                        <canvas id="pieP1" style="max-height:300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Komposisi Pendapatan Periode 2</h5>
                        <canvas id="pieP2" style="max-height:300px;"></canvas>
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

        {{-- Row 3: Detail Table dengan DataTables --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Detail Pendapatan per SKU</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="kategori-table" style="width:100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">No</th>
                                        <th rowspan="2" class="text-center align-middle">SKU</th>
                                        <th rowspan="2" class="text-center align-middle">Produk</th>
                                        <th colspan="3" class="text-center align-middle">Periode 1</th>
                                        <th rowspan="2" class="text-center align-middle">Selisih (P1-P2)</th>
                                        <th colspan="3" class="text-center align-middle">Periode 2</th>
                                    </tr>
                                    <tr>
                                        <th>Shopee (P1)</th>
                                        <th>Tiktok (P1)</th>
                                        <th>Total (P1)</th>
                                        <th>Shopee (P2)</th>
                                        <th>Tiktok (P2)</th>
                                        <th>Total (P2)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Initialize totals
                                        $total_s1 = 0;
                                        $total_t1 = 0;
                                        $total_tot1 = 0;
                                        $total_s2 = 0;
                                        $total_t2 = 0;
                                        $total_tot2 = 0;
                                        $total_diff = 0;
                                    @endphp
                                    @foreach ($kategori as $idx => $item)
                                        @php
                                            $s1 = $item->pendapatan_shopee_per_1;
                                            $t1 = $item->pendapatan_tiktok_per_1;
                                            $tot1 = $item->pendapatan_per_1;
                                            $s2 = $item->pendapatan_shopee_per_2;
                                            $t2 = $item->pendapatan_tiktok_per_2;
                                            $tot2 = $item->pendapatan_per_2;
                                            $diff = $tot2 - $tot1;

                                            // Accumulate
                                            $total_s1 += $s1;
                                            $total_t1 += $t1;
                                            $total_tot1 += $tot1;
                                            $total_s2 += $s2;
                                            $total_t2 += $t2;
                                            $total_tot2 += $tot2;
                                            $total_diff += $diff;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->sku }}</td>
                                            <td>{{ $item->nama_produk }}</td>
                                            <td>Rp {{ number_format($s1, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($t1, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($tot1, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($diff > 0)
                                                    <span class="text-success">▲
                                                        {{ number_format($tot1 > 0 ? ($diff / $tot1) * 100 : 0, 2) }}%<br>(Rp
                                                        {{ number_format($diff, 0, ',', '.') }})</span>
                                                @elseif ($diff < 0)
                                                    <span class="text-danger">▼
                                                        {{ number_format($tot1 > 0 ? abs($diff / $tot1) * 100 : 0, 2) }}%<br>(Rp
                                                        {{ number_format(abs($diff), 0, ',', '.') }})</span>
                                                @else
                                                    <span>—</span>
                                                @endif
                                            </td>
                                            <td>Rp {{ number_format($s2, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($t2, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($tot2, 0, ',', '.') }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total:</td>
                                        <td>Rp {{ number_format($total_s1, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($total_t1, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($total_tot1, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($total_diff, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($total_s2, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($total_t2, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($total_tot2, 0, ',', '.') }}</td>
                                    </tr>
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
        $(function() {
            // Data dari controller
            const data = @json($kategori);

            // Labels kategori
            const categories = data.map(i => i.sku);
            // Data P1 & P2
            const valuesP1 = data.map(i => i.pendapatan_per_1);
            const valuesP2 = data.map(i => i.pendapatan_per_2);

            // Pie Chart Periode 1
            new Chart(document.getElementById('pieP1'), {
                type: 'pie',
                data: {
                    labels: categories,
                    datasets: [{
                        data: valuesP1
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

            // Pie Chart Periode 2
            new Chart(document.getElementById('pieP2'), {
                type: 'pie',
                data: {
                    labels: categories,
                    datasets: [{
                        data: valuesP2
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


            // piechart platform
            const totalS = data.reduce((sum, i) => sum + parseFloat(i.pendapatan_shopee_per_1 || 0) + parseFloat(i
                .pendapatan_shopee_per_2 || 0), 0);
            const totalT = data.reduce((sum, i) => sum + parseFloat(i.pendapatan_tiktok_per_1 || 0) + parseFloat(i
                .pendapatan_tiktok_per_2 || 0), 0);

            const total = totalS + totalT;
            const persentaseS = (totalS / total) * 100;
            const persentaseT = (totalT / total) * 100;

            new Chart(document.getElementById('piePlatform'), {
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
            new Chart(document.getElementById('barTop10'), {
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

            // Inisialisasi DataTable
            $('#kategori-table').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'colvis',
                    text: 'Tampilkan Kolom'
                }],
                responsive: true,
                searching: true,
                ordering: true,
                info: true,
                paging: true,
                columnDefs: [
                    // Enable ordering only on Total columns
                    {
                        targets: '_all',
                        orderable: false
                    },
                ],
                language: {
                    search: '_INPUT_',
                    searchPlaceholder: 'Cari...',
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    }
                }
            });
        });
    </script>
@endpush
