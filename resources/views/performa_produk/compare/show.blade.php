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
                    <input hidden id="kode_produk" value="{{ $monthOne[0]->kode_produk }}" />
                </div>
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Informasi Produk (Periode 1)</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kode Produk:</label>
                                <p class="form-control-static" id="kode_produk_1">{{ $monthOne[0]->kode_produk ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Produk:</label>
                                <p class="form-control-static" id="nama_produk_1">{{ $monthOne[0]->produk ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jumlah Variasi :</label>
                                <p class="form-control-static" id="status_produk_1">
                                    {{ count($monthOne) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Informasi Produk (Periode 2)</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kode Produk:</label>
                                <p class="form-control-static" id="kode_produk_2">{{ $monthTwo[0]->kode_produk ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Produk:</label>
                                <p class="form-control-static" id="nama_produk_2">{{ $monthTwo[0]->produk ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jumlah Variasi :</label>
                                <p class="form-control-static" id="status_produk_1">
                                    {{ count($monthTwo) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Penjualan Siap Dikirim 1</label>
                            <h3 class="form-control-static">
                                {{ number_format($total_penjualan_one, 0, ',', '.') ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mx-4">

                                @if ($total_penjualan_one < $total_penjualan_two)
                                    <i class="fa-solid fa-arrow-up" style="color: #28a745;"></i>
                                @elseif ($total_penjualan_one > $total_penjualan_two)
                                    <i class="fa-solid fa-arrow-down" style="color: #dc3545;"></i>
                                @else
                                    <i class="fa-solid fa-equals" style="color: #6c757d;"></i>
                                @endif

                            </div>
                            @php
                                $percentage_diff = 0;
                                if ($total_penjualan_one != 0) {
                                    $percentage_diff =
                                        (($total_penjualan_two - $total_penjualan_one) / $total_penjualan_one) * 100;
                                }
                            @endphp
                            <p class="text-center fw-bold mb-0">
                                {{ number_format(abs($total_penjualan_two - $total_penjualan_one), 0, ',', '.') ?? 'N/A' }}
                            </p>
                            <p class="text-center mb-0 fw-bold">
                                @if ($percentage_diff != 0)
                                    ({{ number_format($percentage_diff, 2) }}%)
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <label class="form-label fw-semibold">Penjualan Siap Dikirim 2</label>
                            <h3 class="form-control-static">
                                {{ number_format($total_penjualan_two, 0, ',', '.') ?? 'N/A' }} </h3>
                        </div>
                    </div>
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
                                <table class="table table-striped table-bordered align-middle text-center table-hover"
                                    id="produkVarianTable">
                                    <thead class="table-primary align-middle">
                                        <tr class="text-center">
                                            <th scope="col">No</th>
                                            <th scope="col">Kode Produk</th>
                                            <th scope="col" style="min-width: 300px;">Produk</th>
                                            <th scope="col">Total Pengunjung 1</th>
                                            <th scope="col">Total Pengunjung 2</th>
                                            <th scope="col">Persentase Perubahan Total Pengunjung (%)</th>
                                            <th scope="col">Pengunjung Produk Dimasukan ke Keranjang 1</th>
                                            <th scope="col">Pengunjung Produk Dimasukan ke Keranjang 2</th>
                                            <th scope="col">Persentase Perubahan Pengunjung Produk Masukkan Ke Keranjang
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

    console.log(
        'ada'
    )
        function getData() {
            $.ajax({
                url: '/performa-produk/compare/detail/getDataTable',
                type: 'post',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    kodeProduk: $("#kode_produk").val()
                },
                success: function(response) {
                    if (response.success) {
                        tableDataPerforma(response.data)
                    }
                }
            });


        }

        function tableDataPerforma(data) {
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
                persentasePerubahanPenjualanDibuat = parseFloat(item.persentase_perubahan_penjualan_dibuat) || 0;
                persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang = parseFloat(item
                    .persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang) || 0;
                persentase_perubahan_pengunjung_produk_kunjungan = parseFloat(item
                    .persentase_perubahan_pengunjung_produk_kunjungan) || 0;
                persentase_perubahan_total_pesanan = parseFloat(item.persentase_perubahan_total_pesanan) || 0;
                row.innerHTML = `
                <td>${iiindex+1}</td>
                    <td class="text-start">${item.kode_produk || ''}</td>
                    <td class="text-start"><a href="/performa-produk/compare/detail/${item.kode_produk}"> ${item.nama_produk || ''}</a></td>
                    <td class="text-end">${formatAngka(item.pengunjung_produk_kunjungan_1) || 0}</td>
                    <td class="text-end">${formatAngka(item.pengunjung_produk_kunjungan_2) || 0}</td>
                    <td class="text-center ${persentase_perubahan_pengunjung_produk_kunjungan > 0 ? 'text-success' : 'text-danger' }">${persentase_perubahan_pengunjung_produk_kunjungan.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
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
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'Bp>>",
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    className: 'btn btn-success'
                }],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                destroy: true // Agar DataTable bisa diinisialisasi ulang tanpa error
            });
        }
    </script>
@endpush
