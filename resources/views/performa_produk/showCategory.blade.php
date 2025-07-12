@extends('layouts.main')

@section('content')
    <a href="/performa-produk/kategori" class="btn btn-sm btn-secondary mb-4">Kembali</a>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="mb-4">
                <h4>{{ $kategori->nama_kategori }}</h4>
            </div>

            <form id="productForm">
                @csrf
                <div class="form-group">
                    <label for="product_code">Kode Produk (SKU)</label>
                    <input type="hidden" name="kategori_id" id="kategori_id" value="{{ $kategori->id }}">
                    <input type="text" class="form-control" id="product_code" name="product_code"
                        placeholder="Masukan kode produk." required>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Submit</button>
            </form>
        </div>

        <div class="mt-4 mb-4">
            <div class="card-body">
                <table id="productCodesTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mb-4 d-flex justify-content-center">
            <div class="card text-white bg-success mb-3 me-3" style="max-width: 22rem;">
                <div class="card-header text-center fw-bold">Total Penjualan Periode 1</div>
                <div class="card-body">
                    <h5 class="card-title text-center" id="totalPenjualanPeriode1">0</h5>
                    <p class="card-text text-center">Total penjualan seluruh produk pada periode 1.</p>
                </div>
            </div>
            <!-- Selisih Kenaikan/Penurunan -->
            <div class="card mb-3 me-3 d-flex flex-column justify-content-center align-items-center"
                style="max-width: 18rem; min-width: 18rem; background: #f8f9fa;">
                <div class="card-header text-center fw-bold" style="color: #333;">Selisih</div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title text-center" id="selisihPenjualan" style="font-size: 2rem;">0</h5>
                    <p class="card-text text-center" id="statusPerubahan" style="margin-bottom: 0;">-</p>
                </div>
            </div>
            <div class="card text-white bg-primary mb-3" style="max-width: 22rem;">
                <div class="card-header text-center fw-bold">Total Penjualan Periode 2</div>
                <div class="card-body">
                    <h5 class="card-title text-center" id="totalPenjualanPeriode2">0</h5>
                    <p class="card-text text-center">Total penjualan seluruh produk pada periode 2.</p>
                </div>
            </div>
        </div>
        <div class="mb-4 mx-4">
            <div class="mb-5" style="position: relative; height: 500px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center table-hover" id="compareTable">
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
                        <th scope="col">Persentase Perubahan Pengunjung Produk Masukkan Ke Keranjang (%)
                        </th>
                        <th scope="col">Total Pesanan 1</th>
                        <th scope="col">Total Pesanan 2</th>
                        <th scope="col">Persentase Perubahan Total Pesanan (%)</th>
                        <th scope="col">Total Penjualan dibuat 1</th>
                        <th scope="col">Total Penjualan dibuat 2</th>
                        <th scope="col">Persentase Perubahan Total Penjualan Dibuat(%)</th>
                        <th scope="col">Total Penjualan Siap Dikirim 1</th>
                        <th scope="col">Total Penjualan Siap Dikirim 2</th>
                        <th scope="col">Persentase Perubahan Penjualan Siap Dikirim(%)</th>
                        <th scope="col">Selisih Penjualan Pesanan Dibuat ke Dikirim</th>
                        <th scope="col">Average Order Value (AOV) (%)</th>
                        <th scope="col">Rata-rata Jumlah Pesanan per Hari</th>
                    </tr>
                </thead>
                <tbody id="dataPerformaTable">
                    <!-- Data akan diisi melalui JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Sertakan Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Deklarasikan variabel chart di scope global agar bisa diakses dari fungsi manapun
        let myChart;
        /**
         * Fungsi untuk membuat atau memperbarui chart.
         * @param {Array} salesData - Array objek yang berisi data penjualan.
         */
        function createOrUpdateChart(produkData) {
            //memfilter agar produk data ini maksimal diambil 10 data untuk dibuatkan chart
            produkData = produkData.slice(0, 10);

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

        function updateTotalPenjualanKategori(produkData) {
            let totalPenjualan1 = 0;
            let totalPenjualan2 = 0;
            let totalPesanan = 0;
            if (produkData && produkData.length) {
                totalPenjualan1 = produkData.reduce((sum, item) => {
                    // Penjumlahan kedua periode, bisa disesuaikan jika hanya salah satu periode
                    return sum + (parseFloat(item.total_penjualan_1) || 0);
                }, 0);
                totalPenjualan2 = produkData.reduce((sum, item) => {
                    // Penjumlahan kedua periode, bisa disesuaikan jika hanya salah satu periode
                    return sum + (parseFloat(item.total_penjualan_2) || 0);
                }, 0);
            }
            //tambahkan selisih penjualan dan persentase selisih penjualan
            let selisihPenjualan = totalPenjualan2 - totalPenjualan1;
            let persentaseSelisih = (selisihPenjualan / totalPenjualan1) * 100;
            document.getElementById('selisihPenjualan').textContent = selisihPenjualan.toLocaleString('id-ID');
            document.getElementById('statusPerubahan').textContent = persentaseSelisih > 0 ? '+' + persentaseSelisih
                .toFixed(2) + '%' : persentaseSelisih.toFixed(2) + '%';


            // Format angka dengan pemisah ribuan
            document.getElementById('totalPenjualanPeriode1').textContent = totalPenjualan1.toLocaleString('id-ID');
            document.getElementById('totalPenjualanPeriode2').textContent = totalPenjualan2.toLocaleString('id-ID');
        }

        /**
         * Fungsi untuk memuat data produk (tabel) dan data penjualan (chart) dari server.
         */
        function loadData() {
            $.ajax({
                url: `/performa-produk/kategori/get-product-codes/${$('#kategori_id').val()}`,
                method: "GET",
                success: function(response) {
                    // 1. Perbarui Tabel Produk
                    if ($.fn.DataTable.isDataTable('#productCodesTable')) {
                        $('#productCodesTable').DataTable().destroy();
                    }
                    let tbody = $('#productCodesTable tbody');
                    tbody.empty();
                    response.data.forEach(function(item, index) {
                        tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td class="${item.status == 'tidak_ada' ? 'text-danger fw-bold' : ''}">${item.kode_produk}</td>
                            <td>${item.nama_produk}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="deleteProductCode('${item.id}')">Delete</button>
                            </td>
                        </tr>
                    `);
                    });
                    $('#productCodesTable').DataTable({
                        destroy: true,
                        pageLength: 5
                    });


                    // 2. Panggil fungsi untuk membuat/memperbarui chart dengan data yang relevan
                    // Asumsi: response.sales_data berisi data untuk chart
                    if (response.produkData) {
                        updateTotalPenjualanKategori(response.produkData);
                        createOrUpdateChart(response.produkData);
                        compareTable(response.produkData)
                    }
                },
                error: function(xhr) {
                    alert('Gagal memuat data: ' + xhr.responseText);
                }
            });
        }

        function deleteProductCode(id) {
            if (confirm('Anda yakin ingin menghapus kode produk ini?')) {
                $.ajax({
                    url: `/performa-produk/kategori/delete-product-code/${id}`,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    method: "DELETE",
                    success: function() {
                        alert('Kode produk berhasil dihapus!');
                        loadData(); // Muat ulang data tabel dan chart
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus kode produk.');
                    }
                });
            }
        }

        function compareTable(data) {
          function formatAngka(angka) {
                if (!angka || isNaN(angka)) return '0';
                return parseFloat(angka).toLocaleString('id-ID');
            }
            const tableBody = document.getElementById('dataPerformaTable');
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
                    <td class="text-center">${formatAngka(item.selisih_pesanan_dibuat_ke_siap_dikirim)}</td>
                    <td class="text-center">${item.aov}</td>
                    <td class="text-center">${item.jumlah_pesanan_rata_rata_per_hari}</td>
                `;

                iiindex++;
                tableBody.appendChild(row);
            });

            // Inisialisasi DataTable
            $('#compareTable').DataTable({
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

        // --- Event Listeners ---
        $(document).ready(function() {
            // Panggil fungsi utama saat halaman pertama kali dimuat
            loadData();

            $('#productForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('performa_produk.createProductCode') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        alert('Produk berhasil disimpan!');
                        $('#productForm')[0].reset();
                        loadData
                            (); // Muat ulang data tabel dan chart setelah produk baru ditambahkan
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
