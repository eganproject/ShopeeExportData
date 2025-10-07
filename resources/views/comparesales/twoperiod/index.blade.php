@extends('layouts.main')

{{-- Menambahkan style kustom dan library eksternal --}}
@section('content')
    {{-- Card untuk Filter --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title fw-bold fst-italic mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter me-2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                Comparative Analysis Filter
            </h5>
        </div>
        <div class="card-body">
            <form action="" method="" id="formInputNih">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="periode1" class="form-label">Periode A</label>
                        <select name="periode1" id="periode1" class="form-select">
                            <option value="" selected disabled>Pilih Periode A</option>
                            <option value="current sales">Periode 1 (saat ini)</option>
                            <option value="current sales_twos">Periode 2 (saat ini)</option>
                            <option value="current sales_threes">Periode 3 (saat ini)</option>
                            <option value="current sales_fours">Periode 4 (saat ini)</option>
                            <option value="previous sales">Periode 1 (-prev)</option>
                            <option value="previous sales_twos">Periode 2 (-prev)</option>
                            <option value="previous sales_threes">Periode 3 (-prev)</option>
                            <option value="previous sales_fours">Periode 4 (-prev)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="periode2" class="form-label">Periode B</label>
                        <select name="periode2" id="periode2" class="form-select">
                             <option value="" selected disabled>Pilih Periode B</option>
                            <option value="current sales">Periode 1 (saat ini)</option>
                            <option value="current sales_twos">Periode 2 (saat ini)</option>
                            <option value="current sales_threes">Periode 3 (saat ini)</option>
                            <option value="current sales_fours">Periode 4 (saat ini)</option>
                            <option value="previous sales">Periode 1 (-prev)</option>
                            <option value="previous sales_twos">Periode 2 (-prev)</option>
                            <option value="previous sales_threes">Periode 3 (-prev)</option>
                            <option value="previous sales_fours">Periode 4 (-prev)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="shop_id" class="form-label">Toko</label>
                        <select name="shop_id" id="shop_id" class="form-select">
                            @if(optional(auth()->user())->shop_id == 0)
                                <option value="semua">Semua Toko</option>
                            @endif
                            @foreach ($shop as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" onclick="getDataTwoPeriod()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search me-1" style="width: 16px; height: 16px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Placeholder untuk saat data belum di-load --}}
    <div id="initial-view" class="text-center text-muted py-5">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2 mb-3" style="width: 48px; height: 48px;"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        <h4>Analisis Komparatif</h4>
        <p>Silakan pilih periode dan toko, lalu klik tombol "Filter" untuk melihat data.</p>
    </div>
    
    {{-- Kontainer untuk hasil analisis (charts dan tabel), disembunyikan secara default --}}
    <div id="analysis-results" style="display: none;">
        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Pendapatan (Periode A)</h6>
                        <h3 class="card-title fw-bold text-primary" id="total-pendapatan-a">Rp 0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Pendapatan (Periode B)</h6>
                        <h3 class="card-title fw-bold text-success" id="total-pendapatan-b">Rp 0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Selisih (B - A)</h6>
                        <h3 class="card-title fw-bold" id="total-selisih">Rp 0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Perbandingan Pendapatan per Kategori</h5>
                        <div style="height: 400px;">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Distribusi Pendapatan Periode A</h5>
                         <div style="height: 350px;">
                            <canvas id="distributionChartA"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3 text-center">Distribusi Pendapatan Periode B</h5>
                         <div style="height: 350px;">
                            <canvas id="distributionChartB"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="fw-semibold mb-3 text-center">Detail Pendapatan per Kategori</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableComparative" style="width:100%;">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="text-center align-middle">No</th>
                                <th style="min-width: 150px;" rowspan="2" class="text-center align-middle">Kategori</th>
                                <th colspan="3" class="text-center align-middle bg-primary bg-opacity-10">Periode A</th>
                                <th colspan="3" class="text-center align-middle bg-success bg-opacity-10">Periode B</th>
                                <th rowspan="2" class="text-center align-middle">Selisih</th>
                            </tr>
                            <tr>
                                <th class="bg-primary bg-opacity-10">Shopee</th>
                                <th class="bg-primary bg-opacity-10">Tiktok</th>
                                <th class="bg-primary bg-opacity-10">Total</th>
                                <th class="bg-success bg-opacity-10">Shopee</th>
                                <th class="bg-success bg-opacity-10">Tiktok</th>
                                <th class="bg-success bg-opacity-10">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh JavaScript --}}
                        </tbody>
                        <tfoot>
                            {{-- Total akan diisi oleh JavaScript --}}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- SweetAlert2 untuk notifikasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    {{-- Chart.js untuk membuat bagan --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const periode1Select = document.getElementById('periode1');
        const periode2Select = document.getElementById('periode2');
        
        // Variabel global untuk menyimpan instance chart agar bisa di-destroy sebelum membuat yang baru
        let comparisonChart, distributionChartA, distributionChartB;

        function validatePeriodSelection(element) {
            if (periode1Select.value && periode2Select.value && periode1Select.value === periode2Select.value) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Periode A dan Periode B tidak boleh sama.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                element.value = ""; // Reset pilihan yang baru diubah
            }
        }

        periode1Select.addEventListener('change', () => validatePeriodSelection(periode1Select));
        periode2Select.addEventListener('change', () => validatePeriodSelection(periode2Select));

        function formatRupiah(angka) {
            // Fungsi untuk format angka ke Rupiah dengan titik sebagai pemisah ribuan
            return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
        }

        function getDataTwoPeriod() {
            // Validasi sebelum mengirim request
            if (!periode1Select.value || !periode2Select.value) {
                Swal.fire('Info', 'Harap pilih kedua periode (A dan B) terlebih dahulu.', 'info');
                return;
            }
            if (periode1Select.value === periode2Select.value) {
                Swal.fire('Peringatan', 'Periode A dan Periode B tidak boleh sama.', 'warning');
                return;
            }

            // Tampilkan loading
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Harap tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/performa-produk/compare-sales/twoperiod',
                type: 'POST',
                data: $('#formInputNih').serialize(),
                success: function(response) {
                    Swal.close();

                    if (!response.data || response.data.length === 0) {
                        $('#analysis-results').hide();
                        $('#initial-view').html(`
                            <div class="text-center text-muted py-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle mb-3" style="width: 48px; height: 48px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <h4>Data Tidak Ditemukan</h4>
                                <p>Tidak ada data penjualan untuk periode dan toko yang dipilih.</p>
                            </div>
                        `).show();
                        return;
                    }

                    // Tampilkan kontainer hasil jika ada data
                    $('#initial-view').hide();
                    $('#analysis-results').show();
                    
                    // Proses data untuk tabel dan chart
                    processAndRenderData(response.data);
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan saat mengambil data.', 'error');
                }
            });
        }

        function processAndRenderData(data) {
            let tableBodyHtml = '';
            let totalShopee1 = 0, totalTiktok1 = 0, total1 = 0;
            let totalShopee2 = 0, totalTiktok2 = 0, total2 = 0;
            let totalSelisih = 0;

            // Data untuk charts
            const labels = [];
            const periodAData = [];
            const periodBData = [];

            data.forEach((item, index) => {
                const selisih = item.pendapatan_per_2 - item.pendapatan_per_1;

                // Akumulasi total untuk footer tabel dan summary cards
                totalShopee1 += parseInt(item.pendapatan_shopee_per_1);
                totalTiktok1 += parseInt(item.pendapatan_tiktok_per_1);
                total1 += parseInt(item.pendapatan_per_1);
                totalShopee2 += parseInt(item.pendapatan_shopee_per_2);
                totalTiktok2 += parseInt(item.pendapatan_tiktok_per_2);
                total2 += parseInt(item.pendapatan_per_2);
                totalSelisih += selisih;

                // Buat baris tabel
                tableBodyHtml += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td><a href="/performa-produk/compare-sales/kategori/${item.id}">${item.nama_kategori}</a></td>
                        <td class="text-end">${formatRupiah(item.pendapatan_shopee_per_1)}</td>
                        <td class="text-end">${formatRupiah(item.pendapatan_tiktok_per_1)}</td>
                        <td class="text-end fw-bold">${formatRupiah(item.pendapatan_per_1)}</td>
                        <td class="text-end">${formatRupiah(item.pendapatan_shopee_per_2)}</td>
                        <td class="text-end">${formatRupiah(item.pendapatan_tiktok_per_2)}</td>
                        <td class="text-end fw-bold">${formatRupiah(item.pendapatan_per_2)}</td>
                        <td class="text-end fw-bold ${selisih < 0 ? 'text-danger' : 'text-success'}">${formatRupiah(selisih)}</td>
                    </tr>
                `;
                
                // Siapkan data untuk chart perbandingan
                labels.push(item.nama_kategori);
                periodAData.push(item.pendapatan_per_1);
                periodBData.push(item.pendapatan_per_2);
            });

            // Render tabel
            $('#tableComparative tbody').html(tableBodyHtml);

            // Render footer tabel
            const tableFooterHtml = `
                <tr class="table-light fw-bold">
                    <th colspan="2" class="text-center">Grand Total</th>
                    <th class="text-end">${formatRupiah(totalShopee1)}</th>
                    <th class="text-end">${formatRupiah(totalTiktok1)}</th>
                    <th class="text-end">${formatRupiah(total1)}</th>
                    <th class="text-end">${formatRupiah(totalShopee2)}</th>
                    <th class="text-end">${formatRupiah(totalTiktok2)}</th>
                    <th class="text-end">${formatRupiah(total2)}</th>
                    <th class="text-end ${totalSelisih < 0 ? 'text-danger' : 'text-success'}">${formatRupiah(totalSelisih)}</th>
                </tr>
            `;
            $('#tableComparative tfoot').html(tableFooterHtml);

            // Update Summary Cards
            $('#total-pendapatan-a').text(formatRupiah(total1));
            $('#total-pendapatan-b').text(formatRupiah(total2));
            $('#total-selisih').text(formatRupiah(totalSelisih)).removeClass('text-danger text-success').addClass(totalSelisih < 0 ? 'text-danger' : 'text-success');

            // Render Charts
            renderComparisonChart(labels, periodAData, periodBData);
            renderDistributionChartA(totalShopee1, totalTiktok1);
            renderDistributionChartB(totalShopee2, totalTiktok2);
        }
        
        function renderComparisonChart(labels, dataA, dataB) {
            const ctx = document.getElementById('comparisonChart').getContext('2d');
            if (comparisonChart) {
                comparisonChart.destroy(); // Hancurkan chart lama sebelum membuat yang baru
            }
            comparisonChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Periode A',
                        data: dataA,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Pendapatan Periode B',
                        data: dataB,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return 'Rp ' + (value / 1000000) + ' Jt'; // Format ke jutaan
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += formatRupiah(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderDistributionChartA(shopee, tiktok) {
            const ctx = document.getElementById('distributionChartA').getContext('2d');
            if (distributionChartA) {
                distributionChartA.destroy();
            }
            distributionChartA = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Shopee', 'Tiktok'],
                    datasets: [{
                        label: 'Distribusi Pendapatan A',
                        data: [shopee, tiktok],
                        backgroundColor: ['#ff6f61', '#2c3e50'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                     plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += formatRupiah(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderDistributionChartB(shopee, tiktok) {
            const ctx = document.getElementById('distributionChartB').getContext('2d');
            if (distributionChartB) {
                distributionChartB.destroy();
            }
            distributionChartB = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Shopee', 'Tiktok'],
                    datasets: [{
                        label: 'Distribusi Pendapatan B',
                        data: [shopee, tiktok],
                        backgroundColor: ['#ff6f61', '#2c3e50'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                     plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += formatRupiah(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
