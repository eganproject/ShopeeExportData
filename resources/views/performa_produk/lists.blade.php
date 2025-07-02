@extends('layouts.main')

@section('content')
    <div class="container">
        <h2>Daftar Produk</h2>
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="searchBox" class="form-control" placeholder="Cari produk...">
            </div>
            <div class="col-md-4">
                <select id="sortBy" class="form-control">
                    <option value="">Urutkan</option>
                    <option value="terbesar">Terbesar</option>
                    <option value="terkecil">Terkecil</option>
                    <!-- Tambahkan filter lain jika perlu -->
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table id="produkTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Produk</th>
                        <th>Status Produk Saat Ini</th>
                        <th>Kode Variasi</th>
                        <th>Nama Variasi</th>
                        <th>Status Variasi Saat Ini</th>
                        <th>Kode Variasi 2</th>
                        <th>SKU Induk</th>
                        <th>Pengunjung Produk (Kunjungan)</th>
                        <th>Halaman Produk Dilihat</th>
                        <th>Pengunjung Melihat Tanpa Membeli</th>
                        <th>Tingkat Pengunjung Melihat Tanpa Membeli</th>
                        <th>Klik Pencarian</th>
                        <th>Suka</th>
                        <th>Pengunjung Produk (Menambahkan Produk ke Keranjang)</th>
                        <th>Dimasukkan ke Keranjang (Produk)</th>
                        <th>Tingkat Konversi Produk Dimasukkan ke Keranjang</th>
                        <th>Total Pembeli (Pesanan Dibuat)</th>
                        <th>Produk (Pesanan Dibuat)</th>
                        <th>Total Penjualan (Pesanan Dibuat) (IDR)</th>
                        <th>Tingkat Konversi (Pesanan yang Dibuat)</th>
                        <th>Total Pembeli (Pesanan Siap Dikirim)</th>
                        <th>Produk (Pesanan Siap Dikirim)</th>
                        <th>Penjualan (Pesanan Siap Dikirim) (IDR)</th>
                        <th>Tingkat Konversi (Pesanan Siap Dikirim)</th>
                        <th>Tingkat Konversi (Pesanan Siap Dikirim dibagi Pesanan Dibuat)</th>
                        <th>% Pembelian Ulang (Pesanan Siap Dikirim)</th>
                        <th>Rata-rata Hari Pembelian Terulang (Pesanan Siap Dikirim)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#produkTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                ajax: {
                    url: '/performa-produk/lists/get-data',
                    data: function(d) {
                        d.search = $('#searchBox').val();
                        d.sort_by = $('#sortBy').val();
                    }
                },
                columns: [{
                        data: 'kode_produk',
                        name: 'kode_produk'
                    },
                    {
                        data: 'produk',
                        name: 'produk'
                    },
                    {
                        data: 'status_produk_saat_ini',
                        name: 'status_produk_saat_ini'
                    },
                    {
                        data: 'kode_variasi',
                        name: 'kode_variasi'
                    },
                    {
                        data: 'nama_variasi',
                        name: 'nama_variasi'
                    },
                    {
                        data: 'status_variasi_saat_ini',
                        name: 'status_variasi_saat_ini'
                    },
                    {
                        data: 'kode_variasi_2',
                        name: 'kode_variasi_2'
                    },
                    {
                        data: 'sku_induk',
                        name: 'sku_induk'
                    },
                    {
                        data: 'pengunjung_produk_kunjungan',
                        name: 'pengunjung_produk_kunjungan'
                    },
                    {
                        data: 'halaman_produk_dilihat',
                        name: 'halaman_produk_dilihat'
                    },
                    {
                        data: 'pengunjung_melihat_tanpa_membeli',
                        name: 'pengunjung_melihat_tanpa_membeli'
                    },
                    {
                        data: 'tingkat_pengunjung_melihat_tanpa_membeli',
                        name: 'tingkat_pengunjung_melihat_tanpa_membeli'
                    },
                    {
                        data: 'klik_pencarian',
                        name: 'klik_pencarian'
                    },
                    {
                        data: 'suka',
                        name: 'suka'
                    },
                    {
                        data: 'pengunjung_produk_menambahkan_ke_keranjang',
                        name: 'pengunjung_produk_menambahkan_ke_keranjang'
                    },
                    {
                        data: 'dimasukkan_ke_keranjang_produk',
                        name: 'dimasukkan_ke_keranjang_produk'
                    },
                    {
                        data: 'tingkat_konversi_produk_dimasukkan_ke_keranjang',
                        name: 'tingkat_konversi_produk_dimasukkan_ke_keranjang'
                    },
                    {
                        data: 'total_pembeli_pesanan_dibuat',
                        name: 'total_pembeli_pesanan_dibuat'
                    },
                    {
                        data: 'produk_pesanan_dibuat',
                        name: 'produk_pesanan_dibuat'
                    },
                    {
                        data: 'total_penjualan_pesanan_dibuat_idr',
                        name: 'total_penjualan_pesanan_dibuat_idr'
                    },
                    {
                        data: 'tingkat_konversi_pesanan_dibuat',
                        name: 'tingkat_konversi_pesanan_dibuat'
                    },
                    {
                        data: 'total_pembeli_pesanan_siap_dikirim',
                        name: 'total_pembeli_pesanan_siap_dikirim'
                    },
                    {
                        data: 'produk_pesanan_siap_dikirim',
                        name: 'produk_pesanan_siap_dikirim'
                    },
                    {
                        data: 'penjualan_pesanan_siap_dikirim_idr',
                        name: 'penjualan_pesanan_siap_dikirim_idr'
                    },
                    {
                        data: 'tingkat_konversi_pesanan_siap_dikirim',
                        name: 'tingkat_konversi_pesanan_siap_dikirim'
                    },
                    {
                        data: 'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat',
                        name: 'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat'
                    },
                    {
                        data: 'persen_pembelian_ulang_pesanan_siap_dikirim',
                        name: 'persen_pembelian_ulang_pesanan_siap_dikirim'
                    },
                    {
                        data: 'rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim',
                        name: 'rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim'
                    },
                ]
            });

            // Trigger server-side search on keyup
            $('#searchBox').on('keyup', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
