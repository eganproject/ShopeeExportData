@extends('layouts.main')

{{-- Menambahkan style kustom dan library eksternal --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Font dan body */
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        /* Kustomisasi Card */
        .custom-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        /* Summary Cards */
        .summary-card {
            display: flex;
            align-items: center;
            padding: 1.5rem;
        }

        .summary-card .icon-bg {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .summary-card .icon-bg svg {
            width: 24px;
            height: 24px;
        }

        .summary-card .card-title {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .summary-card .card-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
        }

        /* Tombol modern */
        .btn-modern {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            transition: all 0.2s ease;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
        }

        /* Kustomisasi Tabel dengan DataTables */
        .dataTables_wrapper {
            padding-top: 1rem;
        }

        .dataTables_filter input,
        .dataTables_length select {
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            border: 1px solid #dee2e6 !important;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        /* Kustomisasi File Input */
        .custom-file-upload {
            border: 2px dashed #0d6efd;
            border-radius: .5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s ease-in-out;
        }

        .custom-file-upload:hover {
            background-color: #f8f9fa;
        }

        /* Hide the default file input */
        #csv-upload {
            display: none;
        }

        .btn-modern {
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }
    </style>
@endpush

@section('content')
    {{-- Indikator Loading --}}
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Header Halaman -->
        <div class="d-flex justify-content-center align-items-center mb-4">
            <div>
                <h3 class="mt-2 mb-0 fw-bold">Impor & Analisis Data Produk</h3>
                <small class="text-muted">Unggah file CSV untuk melihat ringkasan Penjualan.</small>
            </div>
        </div>

        <!-- Bagian Impor dan Instruksi -->
        <div class="d-flex justify-content-center g-4 mb-4">
            <div class="col-lg-5">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Impor Data Analisis</h5>
                        <form id="importForm" action="/performa-produk/compare-sales/import" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="platform" class="form-label fw-semibold">Pilih Platform</label>
                                <select class="form-select" id="platform" name="platform" required>
                                    <option value="" disabled selected>-- Pilih Platform --</option>
                                    <option value="Shopee">Shopee</option>
                                    <option value="Tiktok">Tiktok</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="periode_ke" class="form-label fw-semibold">Periode</label>
                                <select class="form-select" id="periode_ke" name="periode_ke" required>
                                    <option value="" disabled selected>-- Pilih Platform --</option>
                                    <option value="1">Periode 1</option>
                                    <option value="2">Periode 2</option>
                                    <option value="3">Periode 3</option>
                                    <option value="4">Periode 4</option>
                                    <option value="5">Periode 5</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="shop_id" class="form-label fw-semibold">Toko</label>
                                <select class="form-select" id="shop_id" name="shop_id" required>
                                    <option value="" disabled selected>-- Pilih Toko --</option>
                                    @foreach ($shop as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="month_status" class="form-label fw-semibold">Status Periode</label>
                                <select class="form-select" id="month_status" name="month_status" required>
                                    <option value="current">Saat Ini</option>
                                    <option value="previous">Sebelumnya</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-center mt-4">
                                <label for="csv-upload" class="custom-file-upload">
                                    <!--
                                            CHANGE 1: Add the 'multiple' attribute to allow multiple file selection.
                                            CHANGE 2: Change name from "file" to "file[]" to send files as an array.
                                        -->
                                    <input type="file" id="csv-upload" name="file[]" accept=".csv" required multiple />
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                        fill="currentColor" class="bi bi-cloud-arrow-up-fill text-primary mb-2"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z" />
                                    </svg>
                                    <p class="fw-semibold mb-0">Klik untuk memilih file CSV</p>
                                    <small class="text-muted">(Bisa pilih lebih dari satu)</small>
                                    <small id="selected-file-name" class="text-muted d-block mt-1 fw-bold"></small>
                                </label>
                            </div>

                            <button type="button" id="upload-button" class="btn btn-primary btn-modern w-100 mt-3"
                                disabled>Unggah & Proses</button>
                            <button type="button" id="reset-button"
                                class="btn btn-outline-danger btn-modern w-100 mt-2">Reset Semua Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mb-5">
            <a href="/performa-produk/compare-sales/kategori" class="btn btn-outline-primary btn-modern">Kategori</a>
        </div>
        <!-- Summary Cards, Charts, Tables (tetap seperti sebelumnya) -->


        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div class="mx-3">
                            <i class="bi bi-cash-stack fs-2"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold">Total Periode 1</h5>
                            <p class="card-text fs-4" id="totalPeriode1">Rp 0</p>
                            <p class="fs-7 text-end text-secondary" id="prev_totalPeriode1">Rp 0</p>
                            <button type="button" onclick="resetDataPeriode('sales')"
                                class="btn btn-sm btn-outline-danger btn-modern w-100 mt-2">Reset Data Periode ini</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div class="mx-3">
                            <i class="bi bi-cash-stack fs-2"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold">Total Periode 2</h5>
                            <p class="card-text fs-4" id="totalPeriode2">Rp 0</p>
                            <p class="fs-7 text-end text-secondary" id="prev_totalPeriode2">Rp 0</p>
                            <button type="button" onclick="resetDataPeriode('sales_twos')"
                                class="btn btn-sm btn-outline-danger btn-modern w-100 mt-2">Reset Data Periode ini</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div class="mx-3">
                            <i class="bi bi-cash-stack fs-2"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold">Total Periode 3</h5>
                            <p class="card-text fs-4" id="totalPeriode3">Rp 0</p>
                            <p class="fs-7 text-end text-secondary" id="prev_totalPeriode3">Rp 0</p>
                            <button type="button" onclick="resetDataPeriode('sales_threes')"
                                class="btn btn-sm btn-outline-danger btn-modern w-100 mt-2">Reset Data Periode ini</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div class="mx-3">
                            <i class="bi bi-cash-stack fs-2"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold">Total Periode 4</h5>
                            <p class="card-text fs-4" id="totalPeriode4">Rp 0</p>
                            <p class="fs-7 text-end text-secondary" id="prev_totalPeriode4">Rp 0</p>
                            <button type="button" onclick="resetDataPeriode('sales_fours')"
                                class="btn btn-sm btn-outline-danger btn-modern w-100 mt-2">Reset Data Periode ini</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div class="mx-3">
                            <i class="bi bi-cash-stack fs-2"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold">Total Periode 5</h5>
                            <p class="card-text fs-4" id="totalPeriode5">Rp 0</p>
                            <p class="fs-7 text-end text-secondary" id="prev_totalPeriode5">Rp 0</p>
                            <button type="button" onclick="resetDataPeriode('sales_fives')"
                                class="btn btn-sm btn-outline-danger btn-modern w-100 mt-2">Reset Data Periode ini</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Distribusi Penjualan Periode 1</h5>
                        <canvas id="piePeriodOne"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Distribusi Penjualan Periode 2</h5>
                        <canvas id="piePeriodTwo"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Distribusi Penjualan Periode 3</h5>
                        <canvas id="piePeriodThree"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Distribusi Penjualan Periode 4</h5>
                        <canvas id="piePeriodFour"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Distribusi Penjualan Periode 5</h5>
                        <canvas id="piePeriodFive"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row kedua: Pie Chart Tiktok --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">10 Penjualan Teratas (Periode 1)</h5>
                        <canvas id="top10SalesChartP1" style="max-height:400px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">10 Penjualan Teratas (Periode 2)</h5>
                        <canvas id="top10SalesChartP2" style="max-height:400px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">10 Penjualan Teratas (Periode 3)</h5>
                        <canvas id="top10SalesChartP3" style="max-height:400px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">10 Penjualan Teratas (Periode 4)</h5>
                        <canvas id="top10SalesChartP4" style="max-height:400px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">10 Penjualan Teratas (Periode 5)</h5>
                        <canvas id="top10SalesChartP5" style="max-height:400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <!-- ... -->
        </div>
    </div>
    <div id="loading-overlay">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            // Aktifkan tombol jika file dan platform terpilih
            function validateForm() {
                const files = $('#csv-upload').prop('files');
                const fileSelected = files && files.length > 0;
                const platformSelected = $('#platform').val() !== null;
                const periodeSelected = $('#periode_ke').val() !== null;
                const shopSelected = $('#shop_id').val() !== null;

                if (fileSelected && platformSelected && periodeSelected && shopSelected) {
                    $('#upload-button').prop('disabled', false);
                } else {
                    $('#upload-button').prop('disabled', true);
                }
            }

            // Event listener for file input change
            $('#csv-upload').on('change', function() {
                const files = $(this).prop('files');
                if (files.length > 0) {
                    // Display the number of files selected instead of their names
                    $('#selected-file-name').text(files.length + ' file dipilih');
                } else {
                    $('#selected-file-name').text('');
                }
                validateForm();
            });

            // Event listener for select dropdowns
            $('#platform, #periode_ke, #shop_id, #month_status').on('change', function() {
                validateForm();
            });

            // Event listener for the upload button click
            $('#upload-button').on('click', function() {
                const platform = $('#platform').val();
                const periode_ke = $('#periode_ke').val();
                const shop = $('#shop_id option:selected').text();
                const month_status = $('#month_status option:selected').text(); // Corrected variable name

                Swal.fire({
                    title: 'Konfirmasi Unggah',
                    html: `<p class="mb-3">Apakah Anda yakin data yang akan diunggah sudah benar?</p>
                           <ul class="list-group text-start">
                               <li class="list-group-item"><strong>Toko:</strong> ${shop}</li>
                               <li class="list-group-item"><strong>Platform:</strong> ${platform}</li>
                               <li class="list-group-item"><strong>Periode Ke:</strong> ${periode_ke}</li>
                               <li class="list-group-item"><strong>Status Bulan:</strong> ${month_status}</li>
                               <li class="list-group-item"><strong>Jumlah File:</strong> ${$('#csv-upload').prop('files').length}</li>
                           </ul>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Unggah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loading-overlay').css('display', 'flex');
                        // Submit the form
                        $('#importForm').submit();
                    }
                });
            });



            // Reset form
            $('#reset-button').on('click', function() {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mereset semua data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/performa-produk/compare-sales/reset',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Sukses',
                                        text: response.message,
                                        icon: 'success'
                                    });
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1500);
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat memanggil controller',
                                    icon: 'error'
                                });
                                console.log(error);
                            }
                        });
                    }
                });
            });

            // Konfirmasi sebelum submit

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: {!! json_encode(session('success')) !!},
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: {!! json_encode(session('error')) !!},
                    confirmButtonText: 'OK'
                });
            @endif

            {{-- Jika mau juga menangani multiple errors dari Validator: --}}
            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Validasi Error',
                    html: '{!! implode('<br>', $errors->all()) !!}',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            function renderPie(canvasId, periode) {
                $.ajax({
                    url: '/performa-produk/compare-sales/chart',
                    method: 'POST',
                    data: {
                        periode: periode
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        console.log('res', res);
                        const ctx = document.getElementById(canvasId).getContext('2d');
                        var periode_current = res.jumlah_penjualan_current.reduce((a, b) => Number(a) +
                            Number(b), 0);
                        var periode_previous = res.jumlah_penjualan_previous.reduce((a, b) => Number(
                            a) + Number(b), 0);
                        if (periode == 'periode_1') {
                            $('#totalPeriode1').text('Rp ' + periode_current.toLocaleString());
                            $('#prev_totalPeriode1').text('Rp ' + periode_previous.toLocaleString());
                        } else if (periode == 'periode_2') {
                            $('#totalPeriode2').text('Rp ' + periode_current.toLocaleString());
                            $('#prev_totalPeriode2').text('Rp ' + periode_previous.toLocaleString());
                        } else if (periode == 'periode_3') {
                            $('#totalPeriode3').text('Rp ' + periode_current.toLocaleString());
                            $('#prev_totalPeriode3').text('Rp ' + periode_previous.toLocaleString());
                        } else if (periode == 'periode_4') {
                            $('#totalPeriode4').text('Rp ' + periode_current.toLocaleString());
                            $('#prev_totalPeriode4').text('Rp ' + periode_previous.toLocaleString());
                        } else if (periode == 'periode_5') {
                            $('#totalPeriode5').text('Rp ' + periode_current.toLocaleString());
                            $('#prev_totalPeriode5').text('Rp ' + periode_previous.toLocaleString());
                        }
                        new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: res.labels,
                                datasets: res
                                    .datasets // <-- langsung pakai datasets dari response
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                    },
                                    title: {
                                        display: false
                                    }
                                }
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading chart data:', xhr);
                    }
                });
            }

            function getTop10Sales(canvasId, periode) {
                $.ajax({
                    url: '/performa-produk/compare-sales/top-sales',
                    method: 'POST',
                    data: {
                        periode: periode
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (periode == 'periode_5') {
                            console.log('res : ', res)
                        }
                        const ctx = document.getElementById(canvasId).getContext('2d');
                        new Chart(ctx, {
                            type: 'bar', // bar chart untuk top 10
                            data: {
                                labels: res.labels,
                                datasets: [{
                                    label: 'Total Pendapatan',
                                    data: res.data,
                                    backgroundColor: res.data.map(() =>
                                        'rgba(54, 162, 235, 0.7)'),
                                    borderColor: res.data.map(() =>
                                        'rgba(54, 162, 235, 1)'),
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                indexAxis: 'x', // vertical bars
                                responsive: true,
                                scales: {
                                    x: {
                                        ticks: {
                                            autoSkip: false
                                        },
                                        title: {
                                            display: true,
                                            text: 'SKU'
                                        }
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
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(ctx) {
                                                return 'Rp ' + ctx.parsed.y
                                                    .toLocaleString();
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Gagal load data top10:', xhr);
                    }
                });
            }

            // Render kedua chart
            renderPie('piePeriodOne', 'periode_1');
            renderPie('piePeriodTwo', 'periode_2');
            renderPie('piePeriodThree', 'periode_3');
            renderPie('piePeriodFour', 'periode_4');
            renderPie('piePeriodFive', 'periode_5');
            getTop10Sales('top10SalesChartP1', 'periode_1');
            getTop10Sales('top10SalesChartP2', 'periode_2')
            getTop10Sales('top10SalesChartP3', 'periode_3')
            getTop10Sales('top10SalesChartP4', 'periode_4')
            getTop10Sales('top10SalesChartP5', 'periode_5')

        });

        function resetDataPeriode(periode) {
            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin mereset data periode ke ${periode} ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/performa-produk/compare-sales/reset',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            periode: periode
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Sukses',
                                    text: response.message,
                                    icon: 'success'
                                });
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: response.message,
                                    icon: 'error'
                                });
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat memanggil controller',
                                icon: 'error'
                            });
                            console.log(error);
                        }
                    });
                }
            });
        }
    </script>
@endpush
