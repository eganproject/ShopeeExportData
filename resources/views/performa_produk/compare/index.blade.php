@extends('layouts.main')

@section('content')
    <div class="card shadow border-0 mx-auto mb-4">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-lg-4">
                    <h1 class="h3 fw-bold text-center mb-4">
                        Periode 1
                    </h1>
                    <div class="mb-3">
                        <label for="csv-upload" class="form-label fw-semibold">
                            Pilih File CSV Anda:
                        </label>
                        <input type="file" id="csv-upload" accept=".csv" class="form-control" />
                        <div id="selected-file-name" class="form-text text-center mt-2 d-none">
                            File terpilih: <span class="fw-medium"></span>
                        </div>
                    </div>

                    <button id="upload-button" class="btn btn-primary w-100 fw-bold mb-2" disabled>
                        Unggah dan Impor Data
                    </button>
                </div>
                <div class="col-lg-4">
                    <div class="mt-4 small text-muted text-center">
                        <div class="fw-semibold mb-2">
                            Pastikan file CSV Anda memiliki header yang sesuai dengan urutan ini:
                        </div>
                        <div class="bg-light p-3 rounded border text-start" style="font-size: 0.85em;">
                            Kode Produk, Produk, Status Produk Saat Ini, Kode Variasi, Nama Variasi, Status Variasi Saat
                            Ini, Kode Variasi 2, SKU
                            Induk, Pengunjung Produk (Kunjungan), Halaman Produk Dilihat, Pengunjung Melihat Tanpa Membeli,
                            Tingkat
                            Pengunjung Melihat Tanpa Membeli, Klik Pencarian, Suka, Pengunjung Produk (Menambahkan Produk ke
                            Keranjang), Dimasukkan ke Keranjang (Produk), Tingkat Konversi Produk Dimasukkan ke Keranjang,
                            Total
                            Pembeli (Pesanan Dibuat), Produk (Pesanan Dibuat), Total Penjualan (Pesanan Dibuat) (IDR),
                            Tingkat
                            Konversi (Pesanan yang Dibuat), Total Pembeli (Pesanan Siap Dikirim), Produk (Pesanan Siap
                            Dikirim),
                            Penjualan (Pesanan Siap Dikirim) (IDR), Tingkat Konversi (Pesanan Siap Dikirim), Tingkat
                            Konversi
                            (Pesanan Siap Dikirim dibagi Pesanan Dibuat), % Pembelian Ulang (Pesanan Siap Dikirim),
                            Rata-rata Hari
                            Pembelian Terulang (Pesanan Siap Dikirim)
                        </div>
                        <button class="btn btn-danger w-100 fw-bold mb-2" type="button" onclick="resetData()">
                            Reset semua data
                        </button>
                        <div id="message-area" class="alert text-center fw-medium d-none mt-3" role="alert">
                            <!-- Pesan akan ditampilkan di sini -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h1 class="h3 fw-bold text-center mb-4">
                        Periode 2
                    </h1>
                    <div class="mb-3">
                        <label for="csv-upload_2" class="form-label fw-semibold">
                            Pilih File CSV Anda:
                        </label>
                        <input type="file" id="csv-upload_2" accept=".csv" class="form-control" />
                        <div id="selected-file-name_2" class="form-text text-center mt-2 d-none">
                            File terpilih: <span class="fw-medium"></span>
                        </div>
                    </div>

                    <button id="upload-button_2" class="btn btn-primary w-100 fw-bold mb-2" disabled>
                        Unggah dan Impor Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow border-0 mx-auto">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="row mb-4">
                        <div class="col-lg-5">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Jumlah Data
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="jumlah_data_1">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div id="persentasePerbedaanDataJumlah"></div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Jumlah Data
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="jumlah_data_2">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Total Penjualan (IDR)
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="totalPenjualan_1">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div id="persentasePerbedaanDataTotalPenjualan"></div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Total Penjualan (IDR)
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="totalPenjualan_2">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Total Produk Pesanan Siap Dikirim
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="totalProdukSiapDikirim_1">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div id="persentasePerbedaanDataTotalProdukSiapDikirim" class="d-flex justify-content-center">
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Total Produk Pesanan Siap Dikirim
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="totalProdukSiapDikirim_2">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Total Produk Dimasukan Keranjang
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="totalProdukDimasukanKeranjang_1">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div id="persentasePerbedaanDataTotalProdukDimasukanKeranjang"></div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card shadow border-0 mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Total Produk Dimasukan Keranjang
                                    </h5>
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="fw-bold fs-3" id="totalProdukDimasukanKeranjang_2">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <div class="mt-4 small text-muted text-center">
                        <div class="fw-semibold mb-2">
                            Note :
                        </div>
                        <div class="bg-light p-3 rounded border text-start" style="font-size: 0.85em;">
                          Produk dibawah sudah di merge dengan produk variannya, jadi : 
                          <ul>
                          <li> ketika ada perbedaan antara jumlah pengunjung lebih kecil daripada produk dimasukkan ke keranjang itu bisa terjadi ketika pengunjung memasukkan beberapa variasi kedalam keranjangnya.</li>
                          </ul>  
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
                                    <th scope="col">Persentase Perubahan Pengunjung Produk Masukkan Ke Keranjang (%)</th>
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
                                    <th scope="col">Persentase Produk Terkait Omset 1 (%)</th>
                                    <th scope="col">Persentase Produk Terkait Omset 2 (%)</th>
                                </tr>
                            </thead>
                            <tbody id="dataPerformaTable">
                                <!-- Data akan diisi melalui JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
   
    <script>
        // Fungsi untuk menampilkan pesan (didefinisikan di global scope)
        getCountData()

        function showMessage(msg, isSuccess) {
            const messageArea = document.getElementById('message-area');
            messageArea.textContent = msg;
            messageArea.classList.remove('hidden');
            messageArea.classList.remove('d-none');
            if (isSuccess) {
                messageArea.classList.remove('bg-danger', 'text-white');
                messageArea.classList.add('bg-success', 'text-white');
            } else {
                messageArea.classList.remove('bg-success', 'text-white');
                messageArea.classList.add('bg-danger', 'text-white');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('csv-upload');
            const uploadButton = document.getElementById('upload-button');
            const selectedFileNameDisplay = document.getElementById('selected-file-name');
            const fileInput_2 = document.getElementById('csv-upload_2');
            const uploadButton_2 = document.getElementById('upload-button_2');
            const selectedFileNameDisplay_2 = document.getElementById('selected-file-name_2');
            const messageArea = document.getElementById('message-area');
            let selectedFile = null;
            let selectedFile_2 = null;

            // Event listener untuk perubahan file input
            fileInput.addEventListener('change', function(event) {
                selectedFile = event.target.files[0];
                if (selectedFile) {
                    selectedFileNameDisplay.querySelector('span').textContent = selectedFile.name;
                    selectedFileNameDisplay.classList.remove('hidden');
                    uploadButton.disabled = false; // Aktifkan tombol unggah
                    messageArea.classList.add('hidden'); // Sembunyikan pesan sebelumnya
                } else {
                    selectedFileNameDisplay.classList.add('hidden');
                    uploadButton.disabled = true; // Nonaktifkan tombol unggah
                }
            });

            // Event listener untuk tombol unggah
            uploadButton.addEventListener('click', async function() {
                if (!selectedFile) {
                    showMessage('Silakan pilih file CSV terlebih dahulu.', false);
                    return;
                }

                uploadButton.disabled = true;
                uploadButton.textContent = 'Mengunggah...';
                showMessage('Mengunggah dan memproses file...', true);

                const formData = new FormData();
                formData.append('csv_file',
                    selectedFile); // Pastikan nama 'csv_file' sesuai dengan validator Laravel

                try {
                    // Ambil CSRF token dari meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');
                    formData.append('_token', csrfToken);

                    const response = await fetch('/performa-produk/compare/import_1', {
                        method: 'POST',
                        body: formData,
                        // Headers Content-Type tidak perlu diatur secara manual untuk FormData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showMessage(data.message || 'File CSV berhasil diimpor!', true);
                        fileInput.value = ''; // Reset input file
                        selectedFile = null;
                        selectedFileNameDisplay.classList.add('hidden');
                    } else {
                        showMessage(data.message || 'Terjadi kesalahan saat mengimpor file.', false);
                    }
                } catch (error) {
                    console.error('Error uploading CSV:', error);
                    showMessage('Terjadi kesalahan jaringan atau server: ' + error.message, false);
                } finally {
                    uploadButton.disabled = false;
                    uploadButton.textContent = 'Unggah dan Impor Data';
                }
            });

            fileInput_2.addEventListener('change', function(event) {
                selectedFile_2 = event.target.files[0];
                if (selectedFile_2) {
                    selectedFileNameDisplay_2.querySelector('span').textContent = selectedFile_2.name;
                    selectedFileNameDisplay_2.classList.remove('hidden');
                    uploadButton_2.disabled = false; // Aktifkan tombol unggah
                    messageArea.classList.add('hidden'); // Sembunyikan pesan sebelumnya
                } else {
                    selectedFileNameDisplay_2.classList.add('hidden');
                    uploadButton_2.disabled = true; // Nonaktifkan tombol unggah
                }
            });

            // Event listener untuk tombol unggah
            uploadButton_2.addEventListener('click', async function() {
                if (!selectedFile_2) {
                    showMessage('Silakan pilih file CSV terlebih dahulu.', false);
                    return;
                }

                uploadButton_2.disabled = true;
                uploadButton_2.textContent = 'Mengunggah...';
                showMessage('Mengunggah dan memproses file...', true);

                const formData = new FormData();
                formData.append('csv_file',
                    selectedFile_2); // Pastikan nama 'csv_file' sesuai dengan validator Laravel

                try {
                    // Ambil CSRF token dari meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');
                    formData.append('_token', csrfToken);

                    const response = await fetch('/performa-produk/compare/import_2', {
                        method: 'POST',
                        body: formData,
                        // Headers Content-Type tidak perlu diatur secara manual untuk FormData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showMessage(data.message || 'File CSV berhasil diimpor!', true);
                        fileInput_2.value = ''; // Reset input file
                        selectedFile_2 = null;
                        selectedFileNameDisplay_2.classList.add('hidden');
                    } else {
                        showMessage(data.message || 'Terjadi kesalahan saat mengimpor file.', false);
                    }
                } catch (error) {
                    console.error('Error uploading CSV:', error);
                    showMessage('Terjadi kesalahan jaringan atau server: ' + error.message, false);
                } finally {
                    uploadButton_2.disabled = false;
                    uploadButton_2.textContent = 'Unggah dan Impor Data';
                }
            });
        });

        function resetData() {
            if (!confirm('Apakah Anda yakin ingin mereset semua data? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
            $.ajax({
                url: '/performa-produk/compare/reset-data',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    showMessage(response.message || 'Database berhasil direset!', true);
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengambil jumlah data:', error);
                    alert('Terjadi kesalahan saat mereset data.');
                }
            });
        }

        function getCountData() {
            $.ajax({
                url: '/performa-produk/compare/getcountdata',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    $('#jumlah_data_1').text(response.jumlah_data_one || 0);
                    $('#jumlah_data_2').text(response.jumlah_data_two || 0);
                    // Format angka ke mata uang IDR
                    function formatAngka(angka) {
                        if (!angka || isNaN(angka)) return '0';
                        return parseFloat(angka).toLocaleString('id-ID');
                    }

                    $('#totalPenjualan_1').text(formatAngka(response.total_penjualan_one));
                    $('#totalPenjualan_2').text(formatAngka(response.total_penjualan_two));
                    $('#totalProdukSiapDikirim_1').text(formatAngka(response.total_produk_siap_dikirim_one));
                    $('#totalProdukSiapDikirim_2').text(formatAngka(response.total_produk_siap_dikirim_two));
                    $('#totalProdukDimasukanKeranjang_1').text(formatAngka(response
                        .total_produk_dimasukan_ke_keranjang_one));
                    $('#totalProdukDimasukanKeranjang_2').text(formatAngka(response
                        .total_produk_dimasukan_ke_keranjang_two));
                    const jumlah1 = parseInt(response.jumlah_data_one || 0);
                    const jumlah2 = parseInt(response.jumlah_data_two || 0);
                    let percentDiff = 0;
                    let colorClass = '';
                    let icon = '';

                    if (jumlah1 > 0) {
                        percentDiff = ((jumlah2 - jumlah1) / jumlah1) * 100;
                        percentDiff = Math.round(percentDiff * 100) / 100; // 2 decimal
                        if (percentDiff > 0) {
                            colorClass = 'text-success fw-bold';
                            icon = '▲';
                        } else if (percentDiff < 0) {
                            colorClass = 'text-danger fw-bold';
                            icon = '▼';
                        } else {
                            colorClass = 'text-secondary fw-bold';
                            icon = '';
                        }
                    } else {
                        percentDiff = 0;
                        colorClass = 'text-secondary fw-bold';
                        icon = '';
                    }

                    $('#persentasePerbedaanDataJumlah').html(
                        `<div class="${colorClass}" style="font-size:1.2em">${icon} ${percentDiff}%</div>`
                    );

                    const totalPenjualan1 = parseFloat(response.total_penjualan_one || 0);
                    const totalPenjualan2 = parseFloat(response.total_penjualan_two || 0);
                    let totalPercentDiff = 0;
                    let totalColorClass = '';
                    let totalIcon = '';

                    if (totalPenjualan1 > 0) {
                        totalPercentDiff = ((totalPenjualan2 - totalPenjualan1) / totalPenjualan1) * 100;
                        totalPercentDiff = Math.round(totalPercentDiff * 100) / 100; // 2 decimal
                        if (totalPercentDiff > 0) {
                            totalColorClass = 'text-success fw-bold';
                            totalIcon = '▲';
                        } else if (totalPercentDiff < 0) {
                            totalColorClass = 'text-danger fw-bold';
                            totalIcon = '▼';
                        } else {
                            totalColorClass = 'text-secondary fw-bold';
                            totalIcon = '';
                        }
                    } else {
                        totalPercentDiff = 0;
                        totalColorClass = 'text-secondary fw-bold';
                        totalIcon = '';
                    }

                    $('#persentasePerbedaanDataTotalPenjualan').html(
                        `<div class="${totalColorClass}" style="font-size:1.2em">${totalIcon} ${totalPercentDiff}%</div>`
                    );

                    const totalProdukSiapDikirim1 = parseInt(response.total_produk_siap_dikirim_one || 0);
                    const totalProdukSiapDikirim2 = parseInt(response.total_produk_siap_dikirim_two || 0);
                    let totalProdukSiapDikirimPercentDiff = 0;
                    let totalProdukSiapDikirimColorClass = '';
                    let totalProdukSiapDikirimIcon = '';
                    if (totalProdukSiapDikirim1 > 0) {
                        totalProdukSiapDikirimPercentDiff = ((totalProdukSiapDikirim2 -
                                totalProdukSiapDikirim1) /
                            totalProdukSiapDikirim1) * 100;
                        totalProdukSiapDikirimPercentDiff = Math.round(totalProdukSiapDikirimPercentDiff *
                                100) /
                            100; // 2 decimal
                        if (totalProdukSiapDikirimPercentDiff > 0) {
                            totalProdukSiapDikirimColorClass = 'text-success fw-bold';
                            totalProdukSiapDikirimIcon = '▲';
                        } else if (totalProdukSiapDikirimPercentDiff < 0) {
                            totalProdukSiapDikirimColorClass = 'text-danger fw-bold';
                            totalProdukSiapDikirimIcon = '▼';
                        } else {
                            totalProdukSiapDikirimColorClass = 'text-secondary fw-bold';
                            totalProdukSiapDikirimIcon = '';
                        }
                    } else {
                        totalProdukSiapDikirimPercentDiff = 0;
                        totalProdukSiapDikirimColorClass = 'text-secondary fw-bold';
                        totalProdukSiapDikirimIcon = '';
                    }
                    $('#persentasePerbedaanDataTotalProdukSiapDikirim').html(
                        `<div class="${totalProdukSiapDikirimColorClass}" style="font-size:1.2em">${totalProdukSiapDikirimIcon} ${totalProdukSiapDikirimPercentDiff}%</div>`
                    );

                    const totalProdukDimasukanKeranjang1 = parseInt(response
                        .total_produk_dimasukan_ke_keranjang_one || 0);
                    const totalProdukDimasukanKeranjang2 = parseInt(response
                        .total_produk_dimasukan_ke_keranjang_two || 0);
                    let totalProdukDimasukanKeranjangPercentDiff = 0;
                    let totalProdukDimasukanKeranjangColorClass = '';
                    let totalProdukDimasukanKeranjangIcon = '';
                    if (totalProdukDimasukanKeranjang1 > 0) {
                        totalProdukDimasukanKeranjangPercentDiff = ((totalProdukDimasukanKeranjang2 -
                                totalProdukDimasukanKeranjang1) /
                            totalProdukDimasukanKeranjang1) * 100;
                        totalProdukDimasukanKeranjangPercentDiff = Math.round(
                                totalProdukDimasukanKeranjangPercentDiff * 100) /
                            100; // 2 decimal
                        if (totalProdukDimasukanKeranjangPercentDiff > 0) {
                            totalProdukDimasukanKeranjangColorClass = 'text-success fw-bold';
                            totalProdukDimasukanKeranjangIcon = '▲';
                        } else if (totalProdukDimasukanKeranjangPercentDiff < 0) {
                            totalProdukDimasukanKeranjangColorClass = 'text-danger fw-bold';
                            totalProdukDimasukanKeranjangIcon = '▼';
                        } else {
                            totalProdukDimasukanKeranjangColorClass = 'text-secondary fw-bold';
                            totalProdukDimasukanKeranjangIcon = '';
                        }
                    } else {
                        totalProdukDimasukanKeranjangPercentDiff = 0;
                        totalProdukDimasukanKeranjangColorClass = 'text-secondary fw-bold';
                        totalProdukDimasukanKeranjangIcon = '';
                    }

                    $('#persentasePerbedaanDataTotalProdukDimasukanKeranjang').html(
                        `<div class="${totalProdukDimasukanKeranjangColorClass}" style="font-size:1.2em">${totalProdukDimasukanKeranjangIcon} ${totalProdukDimasukanKeranjangPercentDiff}%</div>`
                    );

                    tableDataPerforma(response.data_performa || []);


                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengambil jumlah data:', error);
                }
            });
        }

        function tableDataPerforma(data) {
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
                persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang = parseFloat(item.persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang) || 0;
                persentase_perubahan_pengunjung_produk_kunjungan = parseFloat(item.persentase_perubahan_pengunjung_produk_kunjungan) || 0;
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
                    <td class="text-center">${item.persentase_kontribusi_penjualan_1}</td>
                    <td class="text-center">${item.persentase_kontribusi_penjualan_2}</td>
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
    </script>
@endpush
