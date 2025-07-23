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
                            @php
                                $sum = [
                                    1 => ['s' => 0, 't' => 0, 'tot' => 0, 'diff' => 0],
                                    2 => ['s' => 0, 't' => 0, 'tot' => 0, 'diff' => 0],
                                    3 => ['s' => 0, 't' => 0, 'tot' => 0, 'diff' => 0],
                                    4 => ['s' => 0, 't' => 0, 'tot' => 0, 'diff' => 0],
                                ];
                            @endphp
                            @foreach ($kategori as $item)
                                @php
                                    // hitung per periode
                                    $s1 = $item->pendapatan_shopee_per_1 ?? 0;
                                    $t1 = $item->pendapatan_tiktok_per_1 ?? 0;
                                    $tot1 = $s1 + $t1;
                                    $s2 = $item->pendapatan_shopee_per_2 ?? 0;
                                    $t2 = $item->pendapatan_tiktok_per_2 ?? 0;
                                    $tot2 = $s2 + $t2;
                                    $s3 = $item->pendapatan_shopee_per_3 ?? 0;
                                    $t3 = $item->pendapatan_tiktok_per_3 ?? 0;
                                    $tot3 = $s3 + $t3;
                                    $s4 = $item->pendapatan_shopee_per_4 ?? 0;
                                    $t4 = $item->pendapatan_tiktok_per_4 ?? 0;
                                    $tot4 = $s4 + $t4;
                                    // selisih
                                    $d12 = $tot2 - $tot1;
                                    $d23 = $tot3 - $tot2;
                                    $d34 = $tot4 - $tot3;
                                    // akumulasi
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
                                    <td><a href="/performa-produk/compare-sales/kategori/{{ $item->id }}"
                                            class="text-decoration-none">{{ $item->nama_kategori }}</a></td>
                                    <td>Rp {{ number_format($s1, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($t1, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($tot1, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($tot1 > 0)
                                            @php $pct12 = ($d12 / $tot1) * 100; @endphp
                                            <span
                                                class="@if ($d12 > 0) text-success @elseif($d12 < 0) text-danger @endif">
                                                @if ($d12 > 0)
                                                    ▲
                                                @elseif($d12 < 0)
                                                    ▼
                                                @endif
                                                {{ number_format($pct12, 2) }}%
                                                (Rp {{ number_format(abs($d12), 0, ',', '.') }})
                                            </span>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($s2, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($t2, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($tot2, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($tot2 > 0)
                                            @php $pct23 = ($d23 / $tot2) * 100; @endphp
                                            <span
                                                class="@if ($d23 > 0) text-success @elseif($d23 < 0) text-danger @endif">
                                                @if ($d23 > 0)
                                                    ▲
                                                @elseif($d23 < 0)
                                                    ▼
                                                @endif
                                                {{ number_format($pct23, 2) }}%
                                                (Rp {{ number_format(abs($d23), 0, ',', '.') }})
                                            </span>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($s3, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($t3, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($tot3, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($tot3 > 0)
                                            @php $pct34 = ($d34 / $tot3) * 100; @endphp
                                            <span
                                                class="@if ($d34 > 0) text-success @elseif($d34 < 0) text-danger @endif">
                                                @if ($d34 > 0)
                                                    ▲
                                                @elseif($d34 < 0)
                                                    ▼
                                                @endif
                                                {{ number_format($pct34, 2) }}%
                                                (Rp {{ number_format(abs($d34), 0, ',', '.') }})
                                            </span>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
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
@endsection

@push('scripts')
    {{-- Chart.js dan DataTables JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable


            // Load data untuk Pie Chart via AJAX
            $.ajax({
                url: '/performa-produk/compare-sales/kategori',
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    const ctx = document.getElementById('kategoriPieChart').getContext('2d');
                    const bgColors = res.labels.map(() => {
                        const r = Math.floor(Math.random() * 200) + 55;
                        const g = Math.floor(Math.random() * 200) + 55;
                        const b = Math.floor(Math.random() * 200) + 55;
                        return `rgba(${r},${g},${b},0.7)`;
                    });
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: res.labels,
                            datasets: [{
                                data: res.data,
                                backgroundColor: bgColors,
                                borderColor: bgColors.map(c => c.replace('0.7', '1')),
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
        });
    </script>
@endpush
