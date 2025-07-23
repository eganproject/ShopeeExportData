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
                    <h1 class="display-5 fw-bold text-center text-primary mb-4">{{ $kategori[0]->nama_kategori }}</h1>
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
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Komposisi Pendapatan Periode 3</h5>
                        <canvas id="pieP3" style="max-height:300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Komposisi Pendapatan Periode 4</h5>
                        <canvas id="pieP4" style="max-height:300px;"></canvas>
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
                                        <th style="min-width: 200px;" rowspan="2" class="text-center align-middle">Produk</th>
                                        <th colspan="3" class="text-center align-middle">Periode 1</th>
                                        <th rowspan="2" style="min-width: 150px;" class="text-center align-middle">Selisih (P1-P2)</th>
                                        <th colspan="3" class="text-center align-middle">Periode 2</th>
                                        <th rowspan="2" style="min-width: 150px;" class="text-center align-middle">Selisih (P2-P3)</th>
                                        <th colspan="3" class="text-center align-middle">Periode 3</th>
                                        <th rowspan="2" style="min-width: 150px;" class="text-center align-middle">Selisih (P3-P4)</th>
                                        <th colspan="3" class="text-center align-middle">Periode 4</th>
                                    </tr>
                                    <tr>
                                        <th>Shopee (P1)</th>
                                        <th>Tiktok (P1)</th>
                                        <th>Total (P1)</th>
                                        <th>Shopee (P2)</th>
                                        <th>Tiktok (P2)</th>
                                        <th>Total (P2)</th>
                                        <th>Shopee (P3)</th>
                                        <th>Tiktok (P3)</th>
                                        <th>Total (P3)</th>
                                        <th>Shopee (P4)</th>
                                        <th>Tiktok (P4)</th>
                                        <th>Total (P4)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Initialize accumulators for 4 periods and diffs
                                        $sum = [
                                            1 => ['s' => 0, 't' => 0, 'tot' => 0, 'diff' => 0],
                                            2 => ['s' => 0, 't' => 0, 'tot' => 0, 'diff' => 0],
                                            3 => ['s' => 0, 't' => 0, 'tot' => 0, 'diff' => 0],
                                            4 => ['s' => 0, 't' => 0, 'tot' => 0],
                                        ];
                                    @endphp
                                    @foreach ($kategori as $item)
                                        @php
                                            // Periode 1
                                            $s1 = $item->pendapatan_shopee_per_1 ?? 0;
                                            $t1 = $item->pendapatan_tiktok_per_1 ?? 0;
                                            $tot1 = $s1 + $t1;
                                            // Periode 2
                                            $s2 = $item->pendapatan_shopee_per_2 ?? 0;
                                            $t2 = $item->pendapatan_tiktok_per_2 ?? 0;
                                            $tot2 = $s2 + $t2;
                                            // Periode 3
                                            $s3 = $item->pendapatan_shopee_per_3 ?? 0;
                                            $t3 = $item->pendapatan_tiktok_per_3 ?? 0;
                                            $tot3 = $s3 + $t3;
                                            // Periode 4
                                            $s4 = $item->pendapatan_shopee_per_4 ?? 0;
                                            $t4 = $item->pendapatan_tiktok_per_4 ?? 0;
                                            $tot4 = $s4 + $t4;
                                            // Diffs
                                            $d12 = $tot2 - $tot1;
                                            $d23 = $tot3 - $tot2;
                                            $d34 = $tot4 - $tot3;
                                            // Accumulate
                                            $sum[1]['s'] += $s1;
                                            $sum[1]['t'] += $t1;
                                            $sum[1]['tot'] += $tot1;
                                            $sum[1]['diff'] += $d12;
                                            $sum[2]['s'] += $s2;
                                            $sum[2]['t'] += $t2;
                                            $sum[2]['tot'] += $tot2;
                                            $sum[2]['diff'] += $d23;
                                            $sum[3]['s'] += $s3;
                                            $sum[3]['t'] += $t3;
                                            $sum[3]['tot'] += $tot3;
                                            $sum[3]['diff'] += $d34;
                                            $sum[4]['s'] += $s4;
                                            $sum[4]['t'] += $t4;
                                            $sum[4]['tot'] += $tot4;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->sku }}</td>
                                            <td>{{ $item->nama_produk }}</td>
                                            <!-- Periode 1 -->
                                            <td>Rp {{ number_format($s1, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($t1, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($tot1, 0, ',', '.') }}</td>
                                            <td>
                                                @php $pct12 = $tot1>0 ? ($d12/$tot1)*100 : 0; @endphp
                                                <span
                                                    class="{{ $d12 > 0 ? 'text-success text-center' : ($d12 < 0 ? 'text-danger text-center' : '') }}">
                                                    {{ $d12 > 0 ? '▲' : '▼' }} {{ number_format(abs($pct12), 2) }}%<br>
                                                    (Rp {{ number_format(abs($d12), 0, ',', '.') }})
                                                </span>
                                            </td>
                                            <!-- Periode 2 -->
                                            <td>Rp {{ number_format($s2, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($t2, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($tot2, 0, ',', '.') }}</td>
                                            <td>
                                                @php $pct23 = $tot2>0 ? ($d23/$tot2)*100 : 0; @endphp
                                                <span
                                                    class="{{ $d23 > 0 ? 'text-success text-center' : ($d23 < 0 ? 'text-danger text-center' : '') }}">
                                                    {{ $d23 > 0 ? '▲' : '▼' }} {{ number_format(abs($pct23), 2) }}%<br>
                                                    (Rp {{ number_format(abs($d23), 0, ',', '.') }})
                                                </span>
                                            </td>
                                            <!-- Periode 3 -->
                                            <td>Rp {{ number_format($s3, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($t3, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($tot3, 0, ',', '.') }}</td>
                                            <td>
                                                @php $pct34 = $tot3>0 ? ($d34/$tot3)*100 : 0; @endphp
                                                <span
                                                    class="{{ $d34 > 0 ? 'text-success text-center' : ($d34 < 0 ? 'text-danger text-center' : '') }}">
                                                    {{ $d34 > 0 ? '▲' : '▼' }} {{ number_format(abs($pct34), 2) }}%<br>
                                                    (Rp {{ number_format(abs($d34), 0, ',', '.') }})
                                                </span>
                                            </td>
                                            <!-- Periode 4 -->
                                            <td>Rp {{ number_format($s4, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($t4, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($tot4, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-semibold">
                                        <td colspan="2" class="text-end">Total:</td>
                                        @for ($p = 1; $p <= 4; $p++)
                                            <td>Rp {{ number_format($sum[$p]['s'], 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($sum[$p]['t'], 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($sum[$p]['tot'], 0, ',', '.') }}</td>
                                            @if ($p < 4)
                                                <td>Rp {{ number_format($sum[$p]['diff'], 0, ',', '.') }}</td>
                                            @endif
                                        @endfor
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
            const valuesP3 = data.map(i => i.pendapatan_per_3);
            const valuesP4 = data.map(i => i.pendapatan_per_4);

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

            new Chart(document.getElementById('pieP3'), {
                type: 'pie',
                data: {
                    labels: categories,
                    datasets: [{
                        data: valuesP3
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
            new Chart(document.getElementById('pieP4'), {
                type: 'pie',
                data: {
                    labels: categories,
                    datasets: [{
                        data: valuesP4
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
                .pendapatan_shopee_per_2 || 0) + parseFloat(i
                .pendapatan_shopee_per_3 || 0) + parseFloat(i
                .pendapatan_shopee_per_4 || 0), 0);
            const totalT = data.reduce((sum, i) => sum + parseFloat(i.pendapatan_tiktok_per_1 || 0) + parseFloat(i
                .pendapatan_tiktok_per_2 || 0) + parseFloat(i
                .pendapatan_tiktok_per_3 || 0) + parseFloat(i
                .pendapatan_tiktok_per_4 || 0), 0);

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
