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
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-modern">Kembali</a>
            </div>

            {{-- Pie Chart --}}
            <div class="mb-4">
                <canvas id="kategoriPieChart" style="max-height: 300px;"></canvas>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="kategori-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Pendapatan Periode 1</th>
                            <th>Pendapatan Periode 2</th>
                            <th>Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kategori as $index => $item)
                            @php
                                $diff = $item->pendapatan_per_2 - $item->pendapatan_per_1;
                                // Hitung persentase perubahan (hindari dibagi 0)
                                $pct = $item->pendapatan_per_1 > 0
                                    ? ($diff / $item->pendapatan_per_1) * 100
                                    : 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="/performa-produk/compare-sales/kategori/{{ $item->id }}" class="text-decoration-none">
                                        {{ $item->nama_kategori }}
                                    </a>
                                </td>
                                <td>Rp {{ number_format($item->pendapatan_per_1, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->pendapatan_per_2, 0, ',', '.') }}</td>
                                <td>
                                    @if($diff > 0)
                                        <span class="text-success">
                                            ▲ {{ number_format($pct, 2) }}%
                                            (Rp {{ number_format($diff, 0, ',', '.') }})
                                        </span>
                                    @elseif($diff < 0)
                                        <span class="text-danger">
                                            ▼ {{ number_format(abs($pct), 2) }}%
                                            (Rp {{ number_format(abs($diff), 0, ',', '.') }})
                                        </span>
                                    @else
                                        <span>—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
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
                        legend: { position: 'bottom' }
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
