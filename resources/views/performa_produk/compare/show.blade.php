@extends('layouts.main')

@push('styles')
    {{-- tambahkan cdn bootstrap icon --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush
@section('content')
    <div class="card shadow border-0 mx-auto mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-lg-12 text-center mb-4">
                    <h1 class="h3 fw-bold text-gray-800">Detail Performa Produk</h1>
                    <a href="/performa-produk/compare">Kembali</a>
                    <input hidden id="kode_produk" value="{{ $dataPerformaProduk[0]->kode_produk }}" />
                    <div class="d-flex justify-content-center mt-4">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-secondary">Informasi Produk</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Kode Produk:</label>
                                        <p class="form-control-static" id="kode_produk_1">
                                            {{ $dataPerformaProduk[0]->kode_produk ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nama Produk:</label>
                                        <p class="form-control-static" id="nama_produk_1">
                                            {{ $dataPerformaProduk[0]->nama_produk ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Jumlah Variasi :</label>
                                        <p class="form-control-static" id="status_produk_1">
                                            {{ $count }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">SKU Induk :</label>
                                        <p class="form-control-static" id="sku_induk">
                                            {{ $dataPerformaProduk[0]->sku_induk ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Pengunjung 1</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->pengunjung_produk_kunjungan_1, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mx-4">

                                @if ($dataPerformaProduk[0]->pengunjung_produk_kunjungan_1 < $dataPerformaProduk[0]->pengunjung_produk_kunjungan_2)
                                    <i class="fa-solid fa-arrow-up" style="color: #28a745;"></i>
                                @elseif ($dataPerformaProduk[0]->pengunjung_produk_kunjungan_1 > $dataPerformaProduk[0]->pengunjung_produk_kunjungan_2)
                                    <i class="fa-solid fa-arrow-down" style="color: #dc3545;"></i>
                                @else
                                    <i class="fa-solid fa-equals" style="color: #6c757d;"></i>
                                @endif

                            </div>

                            <p class="text-center fw-bold mb-0">
                                {{ number_format($dataPerformaProduk[0]->pengunjung_produk_kunjungan_2 - $dataPerformaProduk[0]->pengunjung_produk_kunjungan_1, 0, ',', '.') ?? 'N/A' }}
                            </p>
                            <p class="text-center mb-0 fw-bold">
                                @if ($dataPerformaProduk[0]->persentase_perubahan_pengunjung_produk_kunjungan != 0)
                                    {{ number_format($dataPerformaProduk[0]->persentase_perubahan_pengunjung_produk_kunjungan, 2) }}%
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Pengunjung 2</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->pengunjung_produk_kunjungan_2, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Dimasukan Keranjang 1</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->pengunjung_produk_menambahkan_ke_keranjang_1, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mx-4">

                                @if (
                                    $dataPerformaProduk[0]->pengunjung_produk_menambahkan_ke_keranjang_1 <
                                        $dataPerformaProduk[0]->pengunjung_produk_menambahkan_ke_keranjang_2)
                                    <i class="fa-solid fa-arrow-up" style="color: #28a745;"></i>
                                @elseif (
                                    $dataPerformaProduk[0]->pengunjung_produk_menambahkan_ke_keranjang_1 >
                                        $dataPerformaProduk[0]->pengunjung_produk_menambahkan_ke_keranjang_2)
                                    <i class="fa-solid fa-arrow-down" style="color: #dc3545;"></i>
                                @else
                                    <i class="fa-solid fa-equals" style="color: #6c757d;"></i>
                                @endif

                            </div>

                            <p class="text-center fw-bold mb-0">
                                {{ number_format($dataPerformaProduk[0]->pengunjung_produk_menambahkan_ke_keranjang_2 - $dataPerformaProduk[0]->pengunjung_produk_menambahkan_ke_keranjang_1, 0, ',', '.') ?? 'N/A' }}
                            </p>
                            <p class="text-center mb-0 fw-bold">
                                @if ($dataPerformaProduk[0]->persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang != 0)
                                    {{ number_format($dataPerformaProduk[0]->persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang, 2) }}%
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Dimasukan Keranjang 2</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->pengunjung_produk_menambahkan_ke_keranjang_2, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Pesanan Dibuat 1</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_pesanan_dibuat_1, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mx-4">

                                @if ($dataPerformaProduk[0]->total_pesanan_dibuat_1 < $dataPerformaProduk[0]->total_pesanan_dibuat_2)
                                    <i class="fa-solid fa-arrow-up" style="color: #28a745;"></i>
                                @elseif ($dataPerformaProduk[0]->total_pesanan_dibuat_1 > $dataPerformaProduk[0]->total_pesanan_dibuat_2)
                                    <i class="fa-solid fa-arrow-down" style="color: #dc3545;"></i>
                                @else
                                    <i class="fa-solid fa-equals" style="color: #6c757d;"></i>
                                @endif

                            </div>

                            <p class="text-center fw-bold mb-0">
                                {{ number_format($dataPerformaProduk[0]->total_pesanan_dibuat_2 - $dataPerformaProduk[0]->total_pesanan_dibuat_1, 0, ',', '.') ?? 'N/A' }}
                            </p>
                            <p class="text-center mb-0 fw-bold">
                                @if ($dataPerformaProduk[0]->persentase_perubahan_total_pesanan_dibuat != 0)
                                    {{ number_format($dataPerformaProduk[0]->persentase_perubahan_total_pesanan_dibuat, 2) }}%
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Pesanan Dibuat 2</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_pesanan_dibuat_2, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Pesanan Siap Dikirim 1</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_pesanan_1, 0, ',', '.') ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mx-4">

                                @if ($dataPerformaProduk[0]->total_pesanan_1 < $dataPerformaProduk[0]->total_pesanan_2)
                                    <i class="fa-solid fa-arrow-up" style="color: #28a745;"></i>
                                @elseif ($dataPerformaProduk[0]->total_pesanan_1 > $dataPerformaProduk[0]->total_pesanan_2)
                                    <i class="fa-solid fa-arrow-down" style="color: #dc3545;"></i>
                                @else
                                    <i class="fa-solid fa-equals" style="color: #6c757d;"></i>
                                @endif

                            </div>

                            <p class="text-center fw-bold mb-0">
                                {{ number_format($dataPerformaProduk[0]->total_pesanan_2 - $dataPerformaProduk[0]->total_pesanan_1, 0, ',', '.') ?? 'N/A' }}
                            </p>
                            <p class="text-center mb-0 fw-bold">
                                @if ($dataPerformaProduk[0]->persentase_perubahan_total_pesanan != 0)
                                    {{ number_format($dataPerformaProduk[0]->persentase_perubahan_total_pesanan, 2) }}%
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Pesanan Siap Dikirim 2</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_pesanan_2, 0, ',', '.') ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
                 <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Selisih Pesanan Dibuat ke Siap Dikirim 1</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_pesanan_dibuat_1 - $dataPerformaProduk[0]->total_pesanan_1, 0, ',', '.') ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                          
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Selisih Pesanan Dibuat ke Siap Dikirim 2</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_pesanan_dibuat_2 - $dataPerformaProduk[0]->total_pesanan_2, 0, ',', '.') ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Penjualan Dibuat 1</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_penjualan_dibuat_1, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mx-4">

                                @if ($dataPerformaProduk[0]->total_penjualan_dibuat_1 < $dataPerformaProduk[0]->total_penjualan_dibuat_2)
                                    <i class="fa-solid fa-arrow-up" style="color: #28a745;"></i>
                                @elseif ($dataPerformaProduk[0]->total_penjualan_dibuat_1 > $dataPerformaProduk[0]->total_penjualan_dibuat_2)
                                    <i class="fa-solid fa-arrow-down" style="color: #dc3545;"></i>
                                @else
                                    <i class="fa-solid fa-equals" style="color: #6c757d;"></i>
                                @endif

                            </div>

                            <p class="text-center fw-bold mb-0">
                                {{ number_format($dataPerformaProduk[0]->total_penjualan_dibuat_2 - $dataPerformaProduk[0]->total_penjualan_dibuat_1, 0, ',', '.') ?? 'N/A' }}
                            </p>
                            <p class="text-center mb-0 fw-bold">
                                @if ($dataPerformaProduk[0]->persentase_perubahan_penjualan_dibuat != 0)
                                    {{ number_format($dataPerformaProduk[0]->persentase_perubahan_penjualan_dibuat, 2) }}%
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Penjualan Dibuat 2</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_penjualan_dibuat_2, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Penjualan Siap Dikirim 1</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_penjualan_1, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mx-4">

                                @if ($dataPerformaProduk[0]->total_penjualan_1 < $dataPerformaProduk[0]->total_penjualan_2)
                                    <i class="fa-solid fa-arrow-up" style="color: #28a745;"></i>
                                @elseif ($dataPerformaProduk[0]->total_penjualan_1 > $dataPerformaProduk[0]->total_penjualan_2)
                                    <i class="fa-solid fa-arrow-down" style="color: #dc3545;"></i>
                                @else
                                    <i class="fa-solid fa-equals" style="color: #6c757d;"></i>
                                @endif

                            </div>

                            <p class="text-center fw-bold mb-0">
                                {{ number_format($dataPerformaProduk[0]->total_penjualan_2 - $dataPerformaProduk[0]->total_penjualan_1, 0, ',', '.') ?? 'N/A' }}
                            </p>
                            <p class="text-center mb-0 fw-bold">
                                @if ($dataPerformaProduk[0]->persentase_perubahan_penjualan != 0)
                                    {{ number_format($dataPerformaProduk[0]->persentase_perubahan_penjualan, 2) }}%
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Total Penjualan Siap Dikirim 2</label>
                            <h3 class="form-control-static">
                                {{ number_format($dataPerformaProduk[0]->total_penjualan_2, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Selisih Total Penjualan Dibuat dan Siap Dikirim
                                1</label>
                            <h3 class="form-control-static">
                                @php
                                    $perbedaan_1 =
                                        $dataPerformaProduk[0]->total_penjualan_dibuat_1 -
                                        $dataPerformaProduk[0]->total_penjualan_1;
                                    $perbedaan_2 =
                                        $dataPerformaProduk[0]->total_penjualan_dibuat_2 -
                                        $dataPerformaProduk[0]->total_penjualan_2;
                                    $persentase_perbedaan_penjualan = 0;
                                @endphp
                                {{ number_format($perbedaan_1, 0, ',', '.') ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">

                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Selisih Total Penjualan Dibuat dan Siap Dikirim
                                2</label>
                            <h3 class="form-control-static">
                                {{ number_format($perbedaan_2, 0, ',', '.') ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-4">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Average Order Value (AOV)</label>
                            <h3 class="form-control-static">
                                {{ number_format(($dataPerformaProduk[0]->total_penjualan_1 + $dataPerformaProduk[0]->total_penjualan_2) / ($dataPerformaProduk[0]->total_pesanan_1 + $dataPerformaProduk[0]->total_pesanan_2), 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-4">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Rata rata pesanan perhari</label>
                            <h3 class="form-control-static">
                                {{ number_format(($dataPerformaProduk[0]->total_pesanan_1 + $dataPerformaProduk[0]->total_pesanan_2) / 62, 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-4">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Average Order Quantity (AOQ)</label>
                            <h3 class="form-control-static">
                                {{ number_format(($dataPerformaProduk[0]->total_pesanan_1 + $dataPerformaProduk[0]->total_pesanan_2) / ($dataPerformaProduk[0]->total_pembeli_1 + $dataPerformaProduk[0]->total_pembeli_2), 0, ',', '.') ?? 'N/A' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4 mx-4">
                <div class="mb-5" style="position: relative; height: 500px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Performa Varian Produk</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center table-hover"
                                    id="produkVarianTable">
                                    <thead class="table-secondary align-middle">
                                        <tr class="text-center">
                                            <th scope="col">No</th>
                                            <th scope="col">Kode Produk</th>
                                            <th scope="col">Nama Variasi</th>
                                            <th scope="col">Pengunjung Produk Dimasukan ke Keranjang 1</th>
                                            <th scope="col">Pengunjung Produk Dimasukan ke Keranjang 2</th>
                                            <th scope="col">Persentase Perubahan Pengunjung Produk Masukkan Ke
                                                Keranjang
                                                (%)</th>
                                            <th scope="col">Total Pesanan 1</th>
                                            <th scope="col">Total Pesanan 2</th>
                                            <th scope="col">Persentase Perubahan Total Pesanan (%)</th>
                                            <th scope="col">Total Penjualan dibuat 1</th>
                                            <th scope="col">Total Penjualan dibuat 2</th>
                                            <th scope="col">Persentase Perubahan Total Penjualan (%)</th>
                                            <th scope="col">Total Penjualan Siap Dikirim 1</th>
                                            <th scope="col">Total Penjualan Siap Dikirim 2</th>
                                            <th scope="col">Persentase Perubahan Penjualan (%)</th>
                                            <th scope="col">Average Order Value (AOV) (%)</th>
                                            <th scope="col">Rata-rata Jumlah Pesanan per Hari</th>
                                            <th scope="col">Selisih Penjualan Pesanan Dibuat ke Dikirim</th>
                                            <th scope="col">Persentase Produk Terkait Omset 1 (%)</th>
                                            <th scope="col">Persentase Produk Terkait Omset 2 (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contentProdukVarianTable">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        getData()

    
        /**
         * Fungsi untuk membuat atau memperbarui chart.
         * @param {Array} salesData - Array objek yang berisi data penjualan.
         */
        function createOrUpdateChart(produkData) {
    let myChart;
            // Ekstrak nama produk untuk menjadi label di sumbu X
            const labels = produkData.map(product => product.nama_produk);

            // Ekstrak data penjualan untuk setiap dataset
            const totalPenjualan1 = produkData.map(product => parseFloat(product.total_penjualan_1));
            const totalPenjualan2 = produkData.map(product => parseFloat(product.total_penjualan_2));

            const ctx = document.getElementById('salesChart').getContext('2d');

            // Hancurkan instance chart yang ada sebelum membuat yang baru
            if (myChart) {
                myChart.destroy();
            }

            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Penjualan Periode 1',
                        data: totalPenjualan1,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)', // Biru
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Total Penjualan Periode 2',
                        data: totalPenjualan2,
                        backgroundColor: 'rgba(255, 159, 64, 0.8)', // Oranye
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    // indexAxis: 'y', // Uncomment baris ini jika nama produk terlalu panjang dan ingin grafik menjadi horizontal
                    scales: {
                        x: {
                            ticks: {
                                // Fungsi untuk memotong nama produk jika terlalu panjang
                                callback: function(value) {
                                    const label = this.getLabelForValue(value);
                                    if (label.length > 20) {
                                        return label.substring(0, 20) + '...';
                                    }
                                    return label;
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    // Format angka menjadi lebih ringkas (misal: 1jt, 1k)
                                    if (value >= 1000000) return (value / 1000000) + 'jt';
                                    if (value >= 1000) return (value / 1000) + 'k';
                                    return value;
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: '10 Top Produk Berdasarkan Total Penjualan'
                        },
                        tooltip: {
                            callbacks: {
                                // Menampilkan nama produk lengkap di tooltip
                                title: function(tooltipItems) {
                                    return labels[tooltipItems[0].dataIndex];
                                }
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function getData() {
            $.ajax({
                url: '/performa-produk/compare/detail/getDataTable',
                type: 'post',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    kodeProduk: $("#kode_produk").val()
                },
                success: function(response) {
                    tableDataPerforma(response.data)
                    createOrUpdateChart(response.data)
                }
            });


        }

        function tableDataPerforma(data) {
            if (data.length == 0) {
                $('#produkVarianTable').html(
                    '<p class="text-center">Tidak ada data varian untuk produk ini.</p>');
                return;
            } else {

                function formatAngka(angka) {
                    if (!angka || isNaN(angka)) return '0';
                    return parseFloat(angka).toLocaleString('id-ID');
                }
                const tableBody = document.getElementById('contentProdukVarianTable');
                tableBody.innerHTML = ''; // Kosongkan tabel sebelum mengisi data baru
                let persentasePerubahanPenjualan = 0;
                let persentasePerubahanPenjualanDibuat = 0;
                let persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang = 0;
                let persentase_perubahan_total_pesanan = 0;
                let persentase_perubahan_pengunjung_produk_kunjungan = 0;
                let iiindex = 0;
                data.forEach(item => {
                    const row = document.createElement('tr');
                    persentasePerubahanPenjualan = parseFloat(item.persentase_perubahan_penjualan) || 0;
                    persentasePerubahanPenjualanDibuat = parseFloat(item.persentase_perubahan_penjualan_dibuat) ||
                        0;
                    persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang = parseFloat(item
                        .persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang) || 0;
                    persentase_perubahan_pengunjung_produk_kunjungan = parseFloat(item
                        .persentase_perubahan_pengunjung_produk_kunjungan) || 0;
                    persentase_perubahan_total_pesanan = parseFloat(item.persentase_perubahan_total_pesanan) || 0;
                    row.innerHTML = `
                <td>${iiindex+1}</td>
                    <td class="text-start">${item.kode_produk || ''}</td>
                    <td class="text-start">${item.nama_variasi || ''}</td>
                    <td class="text-end">${formatAngka(item.pengunjung_produk_menambahkan_ke_keranjang_1) || 0}</td>
                    <td class="text-end">${formatAngka(item.pengunjung_produk_menambahkan_ke_keranjang_2) || 0}</td>
                    <td class="text-center ${persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang > 0 ? 'text-success' : 'text-danger' }">${persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-end">${formatAngka(item.total_pesanan_1) || 0}</td>
                    <td class="text-end">${formatAngka(item.total_pesanan_2) || 0}</td>
                    <td class="text-center ${persentase_perubahan_total_pesanan > 0 ? 'text-success' : 'text-danger' }">${persentase_perubahan_total_pesanan.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-end">${formatAngka(item.total_penjualan_dibuat_1) || '0'}</td>
                    <td class="text-end">${formatAngka(item.total_penjualan_dibuat_2) || '0'}</td>
                    <td class="text-center ${persentasePerubahanPenjualanDibuat > 0 ? 'text-success' : 'text-danger' }">${persentasePerubahanPenjualanDibuat.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-end">${formatAngka(item.total_penjualan_1) || '0'}</td>
                    <td class="text-end">${formatAngka(item.total_penjualan_2) || '0'}</td>
                    <td class="text-center ${persentasePerubahanPenjualan > 0 ? 'text-success' : 'text-danger' }">${persentasePerubahanPenjualan.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-center">${item.aov}</td>
                    <td class="text-center">${item.jumlah_pesanan_rata_rata_per_hari}</td>
                    <td class="text-center">${formatAngka(item.selisih_pesanan_dibuat_ke_siap_dikirim)}</td>
                    <td class="text-center">${item.persentase_kontribusi_penjualan_1}</td>
                    <td class="text-center">${item.persentase_kontribusi_penjualan_2}</td>
                `;

                    iiindex++;
                    tableBody.appendChild(row);
                });

                // Inisialisasi DataTable
                $('#produkVarianTable').DataTable({
                   layout: {
                    // Top row: Length menu on the left, search box on the right
                    topStart: {
                        buttons: [{
                            extend: 'colvis',
                            className: 'btn btn-secondary' // Optional: style the button
                        }]
                    },
                    topEnd: 'search',

                    // Bottom row: Table info on the left, buttons and pagination on the right
                    bottomStart: 'pageLength',
                    bottomEnd: {
                        buttons: [{
                            extend: 'excelHtml5',
                            text: 'Export to Excel',
                            className: 'btn btn-success'
                        }],
                        paging: true // Make sure to include pagination here
                    }
                },
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                destroy: true // This is fine to keep
                });

            }
        }
    </script>
@endpush
