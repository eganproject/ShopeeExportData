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
                    @php
                        // Inisialisasi total per kolom
                        $sum_s1 = $sum_t1 = $sum_tot1 = 0;
                        $sum_s2 = $sum_t2 = $sum_tot2 = 0;
                        $sum_diff = 0;
                    @endphp
                    <table class="table table-hover align-middle" id="kategori-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Kategori</th>
                                <th colspan="3" class="text-center">Periode 1</th>
                                <th rowspan="2">Selisih</th>
                                <th colspan="3" class="text-center">Periode 2</th>
                            </tr>
                            <tr>
                                <th>Shopee</th>
                                <th>Tiktok</th>
                                <th>Jumlah</th>
                                <th>Shopee</th>
                                <th>Tiktok</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategori as $index => $item)
                                @php
                                    $s1 = $item->pendapatan_shopee_per_1 ?? 0;
                                    $t1 = $item->pendapatan_tiktok_per_1 ?? 0;
                                    $tot1 = $s1 + $t1;
                                    $s2 = $item->pendapatan_shopee_per_2 ?? 0;
                                    $t2 = $item->pendapatan_tiktok_per_2 ?? 0;
                                    $tot2 = $s2 + $t2;
                                    $diff = $tot2 - $tot1;
                                    $pct = $tot1 > 0 ? ($diff / $tot1) * 100 : 0;
                                    // Akumulasi
                                    $sum_s1 += $s1;
                                    $sum_t1 += $t1;
                                    $sum_tot1 += $tot1;
                                    $sum_s2 += $s2;
                                    $sum_t2 += $t2;
                                    $sum_tot2 += $tot2;
                                    $sum_diff += $diff;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="/performa-produk/compare-sales/kategori/{{ $item->id }}"
                                            class="text-decoration-none">{{ $item->nama_kategori }}</a></td>
                                    <td>Rp {{ number_format($s1, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($t1, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($tot1, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($diff > 0)
                                            <span class="text-success">▲ {{ number_format($pct, 2) }}%<br>(Rp
                                                {{ number_format($diff, 0, ',', '.') }})</span>
                                        @elseif($diff < 0)
                                            <span class="text-danger">▼ {{ number_format(abs($pct), 2) }}%<br>(Rp
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
                                <td colspan="2" class="text-end">Total:</td>
                                <td>Rp {{ number_format($sum_s1, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sum_t1, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sum_tot1, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sum_diff, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sum_s2, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sum_t2, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sum_tot2, 0, ',', '.') }}</td>
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
