<?php

namespace App\Http\Controllers;

use App\Models\ProductCompareTableOne;
use App\Models\ProductCompareTableTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ComparePerformaController extends Controller
{
    public function index()
    {
        // Logic to fetch and display product performance data
        return view('performa_produk.compare.index');
    }

    public function importOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:10240', // Max 10MB
        ], [
            'csv_file.required' => 'File CSV wajib diunggah.',
            'csv_file.mimes' => 'File harus berformat CSV.',
            'csv_file.max' => 'Ukuran file CSV tidak boleh melebihi 10MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422); // Unprocessable Entity
        }

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // 2. Buka dan baca file CSV
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return response()->json(['message' => 'Gagal membuka file CSV.'], 500);
        }

        // Ambil header dari baris pertama CSV
        // UBAH DELIMITER DARI KOMA (,) MENJADI TITIK KOMA (;)
        $header = fgetcsv($handle, 1000, ';'); // <--- PERUBAHAN PENTING DI SINI

        // --- DEBUGGING SEMENTARA (Anda bisa hapus ini setelah masalah teratasi) ---
        Log::info('Headers from CSV:', $header);
        // --- AKHIR DEBUGGING ---

        // Definisikan mapping antara header CSV dan nama kolom database
        // Pastikan urutan dan nama header di CSV sesuai dengan yang diharapkan
        $columnMapping = [
            'Kode Produk' => 'kode_produk',
            'Produk' => 'produk',
            'Status Produk Saat Ini' => 'status_produk_saat_ini',
            // Perhatikan bahwa "Kode Variasi" muncul dua kali di header Anda.
            // Pastikan Anda merujuk ke yang benar atau sesuaikan header CSV Anda.
            // Untuk saat ini, saya akan mengasumsikan yang pertama adalah untuk kode_variasi utama.
            'Kode Variasi' => 'kode_variasi', // Ini akan menjadi kode_variasi yang digunakan untuk updateOrCreate
            'Nama Variasi' => 'nama_variasi',
            'Status Variasi Saat Ini' => 'status_variasi_saat_ini',
            'Kode Variasi 2' => 'kode_variasi_2', // Ini adalah kode_variation kedua yang Anda sebutkan
            'SKU Induk' => 'sku_induk',
            'Pengunjung Produk (Kunjungan)' => 'pengunjung_produk_kunjungan',
            'Halaman Produk Dilihat' => 'halaman_produk_dilihat',
            'Pengunjung Melihat Tanpa Membeli' => 'pengunjung_melihat_tanpa_membeli',
            'Tingkat Pengunjung Melihat Tanpa Membeli' => 'tingkat_pengunjung_melihat_tanpa_membeli',
            'Klik Pencarian' => 'klik_pencarian',
            'Suka' => 'suka',
            'Pengunjung Produk (Menambahkan Produk ke Keranjang)' => 'pengunjung_produk_menambahkan_ke_keranjang',
            'Dimasukkan ke Keranjang (Produk)' => 'dimasukkan_ke_keranjang_produk',
            'Tingkat Konversi Produk Dimasukkan ke Keranjang' => 'tingkat_konversi_produk_dimasukkan_ke_keranjang',
            'Total Pembeli (Pesanan Dibuat)' => 'total_pembeli_pesanan_dibuat',
            'Produk (Pesanan Dibuat)' => 'produk_pesanan_dibuat',
            'Total Penjualan (Pesanan Dibuat) (IDR)' => 'total_penjualan_pesanan_dibuat_idr',
            'Tingkat Konversi (Pesanan yang Dibuat)' => 'tingkat_konversi_pesanan_dibuat',
            'Total Pembeli (Pesanan Siap Dikirim)' => 'total_pembeli_pesanan_siap_dikirim',
            'Produk (Pesanan Siap Dikirim)' => 'produk_pesanan_siap_dikirim',
            'Penjualan (Pesanan Siap Dikirim) (IDR)' => 'penjualan_pesanan_siap_dikirim_idr',
            'Tingkat Konversi (Pesanan Siap Dikirim)' => 'tingkat_konversi_pesanan_siap_dikirim',
            'Tingkat Konversi (Pesanan Siap Dikirim dibagi Pesanan Dibuat)' => 'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat',
            '% Pembelian Ulang (Pesanan Siap Dikirim)' => 'persen_pembelian_ulang_pesanan_siap_dikirim',
            'Rata-rata Hari Pembelian Terulang (Pesanan Siap Dikirim)' => 'rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim',
        ];

        // Verifikasi apakah semua header yang diharapkan ada di CSV
        foreach (array_keys($columnMapping) as $expectedHeader) {
            if (!in_array($expectedHeader, $header)) {
                fclose($handle);
                return response()->json([
                    'message' => "Header CSV tidak valid. Kolom '$expectedHeader' tidak ditemukan.",
                ], 400);
            }
        }

        $importedRows = 0;
        $failedRows = [];


        // Mulai transaksi database untuk memastikan atomisitas
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) { // <--- PERUBAHAN PENTING DI SINI JUGA
                // Lewati baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = [];
                foreach ($header as $index => $csvHeader) {
                    // Pastikan header CSV ada di mapping kita
                    if (isset($columnMapping[$csvHeader])) {
                        $dbColumn = $columnMapping[$csvHeader];
                        $value = $row[$index] ?? null;

                        // Konversi nilai numerik ke float/integer jika diperlukan
                        // dan bersihkan karakter non-numerik (misal: koma sebagai pemisah desimal)
                        if (in_array($dbColumn, [
                            'pengunjung_produk_kunjungan',
                            'halaman_produk_dilihat',
                            'pengunjung_melihat_tanpa_membeli',
                            'klik_pencarian',
                            'suka',
                            'pengunjung_produk_menambahkan_ke_keranjang',
                            'dimasukkan_ke_keranjang_produk',
                            'total_pembeli_pesanan_dibuat',
                            'produk_pesanan_dibuat',
                            'total_pembeli_pesanan_siap_dikirim',
                            'produk_pesanan_siap_dikirim',
                            'rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim'
                        ])) {
                            // Hapus semua karakter non-digit kecuali tanda minus di awal
                            if ($value === '-') {
                                $cleanedValue = 0;
                                $data[$dbColumn] = (int) $cleanedValue;
                            } else {
                                $cleanedValue = preg_replace('/[^0-9-]/', '', $value);
                                $data[$dbColumn] = (int) $cleanedValue;
                            }
                        } elseif (in_array($dbColumn, [
                            'tingkat_pengunjung_melihat_tanpa_membeli',
                            'tingkat_konversi_produk_dimasukkan_ke_keranjang',
                            'total_penjualan_pesanan_dibuat_idr',
                            'tingkat_konversi_pesanan_dibuat',
                            'penjualan_pesanan_siap_dikirim_idr',
                            'tingkat_konversi_pesanan_siap_dikirim',
                            'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat',
                            'persen_pembelian_ulang_pesanan_siap_dikirim'
                        ])) {
                            if ($value === '-') {
                                $data[$dbColumn] = (int) 0;
                            } else {
                                // Ganti koma dengan titik untuk desimal, lalu konversi ke float
                                $data[$dbColumn] = (float) str_replace(',', '.', str_replace('.', '', $value));
                            }
                        } else {
                            if (!mb_check_encoding($value, 'UTF-8')) {
                                // Coba konversi dari encoding umum seperti Windows-1252 (ANSI)
                                $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
                            }
                            // Hapus karakter yang tidak valid dari string UTF-8
                            // Ini akan membantu jika ada karakter yang tidak dapat diwakili di database Anda
                            $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);

                            if ($value === '-') {
                                $data[$dbColumn] = null;
                            } else {
                                $data[$dbColumn] = $value;
                            }
                        }
                    }
                }

                // Cek apakah kode_variasi ada, karena ini adalah kunci unik
                // if (!isset($data['kode_variasi']) || empty($data['kode_variasi'])) {
                //     $failedRows[] = ['row' => $row, 'error' => 'Kode Variasi tidak boleh kosong.'];
                //     continue;
                // }

                try {
                    // Coba temukan berdasarkan kode_variasi, jika ada update, jika tidak buat baru
                    ProductCompareTableOne::create($data);
                    $importedRows++;
                } catch (\Exception $e) {
                    // Tangani kesalahan saat menyimpan ke database
                    $failedRows[] = ['row' => $row, 'error' => $e->getMessage()];
                    Log::error("Gagal mengimpor baris: " . json_encode($row) . " Error: " . $e->getMessage());
                }
            }

            DB::commit(); // Commit transaksi jika semua berhasil
            fclose($handle);
            // dd($failedRows);

            $responseMessage = "Impor selesai. Berhasil mengimpor $importedRows baris.";
            if (!empty($failedRows)) {
                $responseMessage .= " Terdapat " . count($failedRows) . " baris yang gagal diimpor.";
                // Anda bisa log $failedRows atau menyertakannya dalam respons jika diperlukan untuk debugging
            }

            return response()->json(['message' => $responseMessage], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi kesalahan
            dd($e);
            fclose($handle);
            Log::error("Kesalahan fatal saat memproses file CSV: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()], 500);
        }
    }
    public function importTwo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:10240', // Max 10MB
        ], [
            'csv_file.required' => 'File CSV wajib diunggah.',
            'csv_file.mimes' => 'File harus berformat CSV.',
            'csv_file.max' => 'Ukuran file CSV tidak boleh melebihi 10MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422); // Unprocessable Entity
        }

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // 2. Buka dan baca file CSV
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return response()->json(['message' => 'Gagal membuka file CSV.'], 500);
        }

        // Ambil header dari baris pertama CSV
        // UBAH DELIMITER DARI KOMA (,) MENJADI TITIK KOMA (;)
        $header = fgetcsv($handle, 1000, ';'); // <--- PERUBAHAN PENTING DI SINI

        // --- DEBUGGING SEMENTARA (Anda bisa hapus ini setelah masalah teratasi) ---
        Log::info('Headers from CSV:', $header);
        // --- AKHIR DEBUGGING ---

        // Definisikan mapping antara header CSV dan nama kolom database
        // Pastikan urutan dan nama header di CSV sesuai dengan yang diharapkan
        $columnMapping = [
            'Kode Produk' => 'kode_produk',
            'Produk' => 'produk',
            'Status Produk Saat Ini' => 'status_produk_saat_ini',
            // Perhatikan bahwa "Kode Variasi" muncul dua kali di header Anda.
            // Pastikan Anda merujuk ke yang benar atau sesuaikan header CSV Anda.
            // Untuk saat ini, saya akan mengasumsikan yang pertama adalah untuk kode_variasi utama.
            'Kode Variasi' => 'kode_variasi', // Ini akan menjadi kode_variasi yang digunakan untuk updateOrCreate
            'Nama Variasi' => 'nama_variasi',
            'Status Variasi Saat Ini' => 'status_variasi_saat_ini',
            'Kode Variasi 2' => 'kode_variasi_2', // Ini adalah kode_variation kedua yang Anda sebutkan
            'SKU Induk' => 'sku_induk',
            'Pengunjung Produk (Kunjungan)' => 'pengunjung_produk_kunjungan',
            'Halaman Produk Dilihat' => 'halaman_produk_dilihat',
            'Pengunjung Melihat Tanpa Membeli' => 'pengunjung_melihat_tanpa_membeli',
            'Tingkat Pengunjung Melihat Tanpa Membeli' => 'tingkat_pengunjung_melihat_tanpa_membeli',
            'Klik Pencarian' => 'klik_pencarian',
            'Suka' => 'suka',
            'Pengunjung Produk (Menambahkan Produk ke Keranjang)' => 'pengunjung_produk_menambahkan_ke_keranjang',
            'Dimasukkan ke Keranjang (Produk)' => 'dimasukkan_ke_keranjang_produk',
            'Tingkat Konversi Produk Dimasukkan ke Keranjang' => 'tingkat_konversi_produk_dimasukkan_ke_keranjang',
            'Total Pembeli (Pesanan Dibuat)' => 'total_pembeli_pesanan_dibuat',
            'Produk (Pesanan Dibuat)' => 'produk_pesanan_dibuat',
            'Total Penjualan (Pesanan Dibuat) (IDR)' => 'total_penjualan_pesanan_dibuat_idr',
            'Tingkat Konversi (Pesanan yang Dibuat)' => 'tingkat_konversi_pesanan_dibuat',
            'Total Pembeli (Pesanan Siap Dikirim)' => 'total_pembeli_pesanan_siap_dikirim',
            'Produk (Pesanan Siap Dikirim)' => 'produk_pesanan_siap_dikirim',
            'Penjualan (Pesanan Siap Dikirim) (IDR)' => 'penjualan_pesanan_siap_dikirim_idr',
            'Tingkat Konversi (Pesanan Siap Dikirim)' => 'tingkat_konversi_pesanan_siap_dikirim',
            'Tingkat Konversi (Pesanan Siap Dikirim dibagi Pesanan Dibuat)' => 'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat',
            '% Pembelian Ulang (Pesanan Siap Dikirim)' => 'persen_pembelian_ulang_pesanan_siap_dikirim',
            'Rata-rata Hari Pembelian Terulang (Pesanan Siap Dikirim)' => 'rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim',
        ];

        // Verifikasi apakah semua header yang diharapkan ada di CSV
        foreach (array_keys($columnMapping) as $expectedHeader) {
            if (!in_array($expectedHeader, $header)) {
                fclose($handle);
                return response()->json([
                    'message' => "Header CSV tidak valid. Kolom '$expectedHeader' tidak ditemukan.",
                ], 400);
            }
        }

        $importedRows = 0;
        $failedRows = [];


        // Mulai transaksi database untuk memastikan atomisitas
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) { // <--- PERUBAHAN PENTING DI SINI JUGA
                // Lewati baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = [];
                foreach ($header as $index => $csvHeader) {
                    // Pastikan header CSV ada di mapping kita
                    if (isset($columnMapping[$csvHeader])) {
                        $dbColumn = $columnMapping[$csvHeader];
                        $value = $row[$index] ?? null;

                        // Konversi nilai numerik ke float/integer jika diperlukan
                        // dan bersihkan karakter non-numerik (misal: koma sebagai pemisah desimal)
                        if (in_array($dbColumn, [
                            'pengunjung_produk_kunjungan',
                            'halaman_produk_dilihat',
                            'pengunjung_melihat_tanpa_membeli',
                            'klik_pencarian',
                            'suka',
                            'pengunjung_produk_menambahkan_ke_keranjang',
                            'dimasukkan_ke_keranjang_produk',
                            'total_pembeli_pesanan_dibuat',
                            'produk_pesanan_dibuat',
                            'total_pembeli_pesanan_siap_dikirim',
                            'produk_pesanan_siap_dikirim',
                            'rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim'
                        ])) {
                            // Hapus semua karakter non-digit kecuali tanda minus di awal
                            if ($value === '-') {
                                $cleanedValue = 0;
                                $data[$dbColumn] = (int) $cleanedValue;
                            } else {
                                $cleanedValue = preg_replace('/[^0-9-]/', '', $value);
                                $data[$dbColumn] = (int) $cleanedValue;
                            }
                        } elseif (in_array($dbColumn, [
                            'tingkat_pengunjung_melihat_tanpa_membeli',
                            'tingkat_konversi_produk_dimasukkan_ke_keranjang',
                            'total_penjualan_pesanan_dibuat_idr',
                            'tingkat_konversi_pesanan_dibuat',
                            'penjualan_pesanan_siap_dikirim_idr',
                            'tingkat_konversi_pesanan_siap_dikirim',
                            'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat',
                            'persen_pembelian_ulang_pesanan_siap_dikirim'
                        ])) {
                            if ($value === '-') {
                                $data[$dbColumn] = (int) 0;
                            } else {
                                // Ganti koma dengan titik untuk desimal, lalu konversi ke float
                                $data[$dbColumn] = (float) str_replace(',', '.', str_replace('.', '', $value));
                            }
                        } else {
                            if (!mb_check_encoding($value, 'UTF-8')) {
                                // Coba konversi dari encoding umum seperti Windows-1252 (ANSI)
                                $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
                            }
                            // Hapus karakter yang tidak valid dari string UTF-8
                            // Ini akan membantu jika ada karakter yang tidak dapat diwakili di database Anda
                            $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);

                            if ($value === '-') {
                                $data[$dbColumn] = null;
                            } else {
                                $data[$dbColumn] = $value;
                            }
                        }
                    }
                }

                // Cek apakah kode_variasi ada, karena ini adalah kunci unik
                // if (!isset($data['kode_variasi']) || empty($data['kode_variasi'])) {
                //     $failedRows[] = ['row' => $row, 'error' => 'Kode Variasi tidak boleh kosong.'];
                //     continue;
                // }

                try {
                    // Coba temukan berdasarkan kode_variasi, jika ada update, jika tidak buat baru
                    ProductCompareTableTwo::create($data);
                    $importedRows++;
                } catch (\Exception $e) {
                    // Tangani kesalahan saat menyimpan ke database
                    $failedRows[] = ['row' => $row, 'error' => $e->getMessage()];
                    Log::error("Gagal mengimpor baris: " . json_encode($row) . " Error: " . $e->getMessage());
                }
            }

            DB::commit(); // Commit transaksi jika semua berhasil
            fclose($handle);
            // dd($failedRows);

            $responseMessage = "Impor selesai. Berhasil mengimpor $importedRows baris.";
            if (!empty($failedRows)) {
                $responseMessage .= " Terdapat " . count($failedRows) . " baris yang gagal diimpor.";
                // Anda bisa log $failedRows atau menyertakannya dalam respons jika diperlukan untuk debugging
            }

            return response()->json(['message' => $responseMessage], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi kesalahan
            dd($e);
            fclose($handle);
            Log::error("Kesalahan fatal saat memproses file CSV: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()], 500);
        }
    }

    public function resetData()
    {
        ProductCompareTableOne::truncate();
        ProductCompareTableTwo::truncate();
        return response()->json(['message' => 'Data Produk kedua tabel berhasil direset.'], 200);
    }

    public function getCountData(Request $request)
    {
        $jumlahDataOne = ProductCompareTableOne::count();
        $jumlahDataTwo = ProductCompareTableTwo::count();

        $totalPenjualanOne = ProductCompareTableOne::sum('penjualan_pesanan_siap_dikirim_idr');
        $totalPenjualanTwo = ProductCompareTableTwo::sum('penjualan_pesanan_siap_dikirim_idr');
        $totalProdukSiapDikirimOne = ProductCompareTableOne::sum('produk_pesanan_siap_dikirim');
        $totalProdukSiapDikirimTwo = ProductCompareTableTwo::sum('produk_pesanan_siap_dikirim');

        $totalProdukDimasukanKeKeranjangOne = ProductCompareTableOne::sum('pengunjung_produk_menambahkan_ke_keranjang');
        $totalProdukDimasukanKeKeranjangTwo = ProductCompareTableTwo::sum('pengunjung_produk_menambahkan_ke_keranjang');

        $dataPerforma = DB::select("
        SELECT
            COALESCE(a.kode_produk, b.kode_produk) AS kode_produk, 
            COALESCE(a.nama_produk, b.nama_produk) AS nama_produk, 
            a.pengunjung_produk_kunjungan AS pengunjung_produk_kunjungan_1,
            IFNULL(b.pengunjung_produk_kunjungan,0) AS pengunjung_produk_kunjungan_2,
            a.pengunjung_produk_menambahkan_ke_keranjang AS pengunjung_produk_menambahkan_ke_keranjang_1,
            IFNULL(b.pengunjung_produk_menambahkan_ke_keranjang,0) AS pengunjung_produk_menambahkan_ke_keranjang_2,
            a.total_pesanan AS total_pesanan_1,
            IFNULL(b.total_pesanan,0) AS total_pesanan_2,
            a.total_penjualan AS total_penjualan_1,
            IFNULL(b.total_penjualan,0) AS total_penjualan_2,
            CASE
                WHEN a.pengunjung_produk_kunjungan = 0 OR a.pengunjung_produk_kunjungan IS NULL THEN 0
                ELSE ((IFNULL(b.pengunjung_produk_kunjungan,0) - a.pengunjung_produk_kunjungan) * 100.0 / a.pengunjung_produk_kunjungan)
            END AS persentase_perubahan_pengunjung_produk_kunjungan,
            CASE
                WHEN a.pengunjung_produk_menambahkan_ke_keranjang = 0 OR a.pengunjung_produk_menambahkan_ke_keranjang IS NULL THEN 0
                ELSE ((IFNULL(b.pengunjung_produk_menambahkan_ke_keranjang,0) - a.pengunjung_produk_menambahkan_ke_keranjang) * 100.0 / a.pengunjung_produk_menambahkan_ke_keranjang) 
            END AS persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang,
            CASE 
                WHEN a.total_pesanan = 0 OR a.total_pesanan IS NULL THEN 0
                ELSE ((IFNULL(b.total_pesanan,0)- a.total_pesanan) * 100.0 / a.total_pesanan)
            END AS persentase_perubahan_total_pesanan,
            CASE 
                WHEN a.total_penjualan = 0 OR a.total_penjualan IS NULL THEN 0
                ELSE ((IFNULL(b.total_penjualan,0) - a.total_penjualan) * 100.0 / a.total_penjualan)
            END AS persentase_perubahan_penjualan
        FROM (
            SELECT
                kode_produk,
                SUM(IFNULL(pengunjung_produk_kunjungan,0)) AS pengunjung_produk_kunjungan,
                SUM(IFNULL(pengunjung_produk_menambahkan_ke_keranjang,0)) AS pengunjung_produk_menambahkan_ke_keranjang,
                produk AS nama_produk,
                SUM(IFNULL(produk_pesanan_siap_dikirim,0)) AS total_pesanan,
                SUM(IFNULL(penjualan_pesanan_siap_dikirim_idr,0)) AS total_penjualan
            FROM product_compare_table_ones
            GROUP BY kode_produk, produk 
        ) AS a
        LEFT JOIN (
            SELECT
                kode_produk,
                produk AS nama_produk,
                SUM(IFNULL(pengunjung_produk_kunjungan,0)) AS pengunjung_produk_kunjungan,
                SUM(IFNULL(pengunjung_produk_menambahkan_ke_keranjang,0)) AS pengunjung_produk_menambahkan_ke_keranjang,
                SUM(IFNULL(produk_pesanan_siap_dikirim,0)) AS total_pesanan,
                SUM(IFNULL(penjualan_pesanan_siap_dikirim_idr,0)) AS total_penjualan
            FROM product_compare_table_twos
            GROUP BY kode_produk, produk 
        ) AS b ON a.kode_produk = b.kode_produk

        UNION ALL

        SELECT
            b.kode_produk AS kode_produk,
            b.nama_produk AS nama_produk,
            IFNULL(a.pengunjung_produk_kunjungan,0) AS pengunjung_produk_kunjungan_1, 
            b.pengunjung_produk_kunjungan AS pengunjung_produk_kunjungan_2,
            IFNULL(a.pengunjung_produk_menambahkan_ke_keranjang,0) AS pengunjung_produk_menambahkan_ke_keranjang_1,
            b.pengunjung_produk_menambahkan_ke_keranjang AS pengunjung_produk_menambahkan_ke_keranjang_2,
            IFNULL(a.total_pesanan,0) AS total_pesanan_1,
            b.total_pesanan AS total_pesanan_2,
            IFNULL(a.total_penjualan,0) AS total_penjualan_1,
            b.total_penjualan AS total_penjualan_2,
            ((b.pengunjung_produk_kunjungan - IFNULL(a.pengunjung_produk_kunjungan,0)) * 100.0 / IFNULL(a.pengunjung_produk_kunjungan,1))
            AS persentase_perubahan_pengunjung_produk_kunjungan,
            ((b.pengunjung_produk_menambahkan_ke_keranjang - IFNULL(a.pengunjung_produk_menambahkan_ke_keranjang,0)) * 100.0 / IFNULL(a.pengunjung_produk_menambahkan_ke_keranjang,1))
            AS persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang,
            CASE
                WHEN a.total_pesanan IS NULL OR a.total_pesanan = 0 THEN 0
                ELSE ((b.total_pesanan - a.total_pesanan) * 100.0 / a.total_pesanan)
            END AS persentase_perubahan_total_pesanan,
            CASE
                WHEN a.total_penjualan IS NULL OR a.total_penjualan = 0 THEN 0
                ELSE ((b.total_penjualan - a.total_penjualan) * 100.0 / a.total_penjualan)
            END AS persentase_perubahan_penjualan
        FROM (
            SELECT
                kode_produk,
                SUM(IFNULL(pengunjung_produk_kunjungan,0)) AS pengunjung_produk_kunjungan,
                SUM(IFNULL(pengunjung_produk_menambahkan_ke_keranjang,0)) AS pengunjung_produk_menambahkan_ke_keranjang,
                produk AS nama_produk,
                SUM(IFNULL(produk_pesanan_siap_dikirim,0)) AS total_pesanan,
                SUM(IFNULL(penjualan_pesanan_siap_dikirim_idr,0)) AS total_penjualan
            FROM product_compare_table_ones
            GROUP BY kode_produk, produk
        ) AS a
        RIGHT JOIN (
            SELECT
                kode_produk,
                produk AS nama_produk,
                SUM(IFNULL(pengunjung_produk_kunjungan,0)) AS pengunjung_produk_kunjungan,
                SUM(IFNULL(pengunjung_produk_menambahkan_ke_keranjang,0)) AS pengunjung_produk_menambahkan_ke_keranjang,
                SUM(IFNULL(produk_pesanan_siap_dikirim,0)) AS total_pesanan,
                SUM(IFNULL(penjualan_pesanan_siap_dikirim_idr,0)) AS total_penjualan
            FROM product_compare_table_twos
            GROUP BY kode_produk, produk
        ) AS b ON a.kode_produk = b.kode_produk
        WHERE a.kode_produk IS NULL; 
        ");

        // Mencari persentase kontribusi total_penjualan_1 perproduk dari totalPenjualanOne
        $totalPenjualanOne = ProductCompareTableOne::sum('penjualan_pesanan_siap_dikirim_idr');
        $totalPenjualanTwo = ProductCompareTableTwo::sum('penjualan_pesanan_siap_dikirim_idr');
        foreach ($dataPerforma as &$item) {
            if ($item->total_penjualan_1 > 0) {
                $item->persentase_kontribusi_penjualan_1 = number_format(($item->total_penjualan_1 * 100) / $totalPenjualanOne, 2);
            } else {
                $item->persentase_kontribusi_penjualan_1 = number_format(0, 2);
            }
            if ($item->total_penjualan_2 > 0) {
                $item->persentase_kontribusi_penjualan_2 = number_format(($item->total_penjualan_2 * 100) / $totalPenjualanTwo, 2);
            } else {
                $item->persentase_kontribusi_penjualan_2 = number_format(0, 2);
            }
            $total_pesanan = $item->total_pesanan_1 + $item->total_pesanan_2;
            $item->jumlah_pesanan_rata_rata_per_hari = number_format($total_pesanan / 62, 2); // Asumsi 62 hari

            if ($total_pesanan != 0) {
                $item->aov = number_format(($item->total_penjualan_1 + $item->total_penjualan_2) / $total_pesanan, 2);
            } else {
                $item->aov = number_format(0, 2);
            }
        }
        unset($item);

        return response()->json([
            'jumlah_data_one' => $jumlahDataOne,
            'jumlah_data_two' => $jumlahDataTwo,
            'total_penjualan_one' => $totalPenjualanOne,
            'total_penjualan_two' => $totalPenjualanTwo,
            'total_produk_siap_dikirim_one' => $totalProdukSiapDikirimOne,
            'total_produk_siap_dikirim_two' => $totalProdukSiapDikirimTwo,
            'total_produk_dimasukan_ke_keranjang_one' => $totalProdukDimasukanKeKeranjangOne,
            'total_produk_dimasukan_ke_keranjang_two' => $totalProdukDimasukanKeKeranjangTwo,
            'data_performa' => $dataPerforma,
        ], 200);
    }

    public function show($kode_produk)
    {
        // Logika untuk menampilkan detail performa produk berdasarkan kode_produk
        $status2Month = true;
        $monthOne = ProductCompareTableOne::where('kode_produk', $kode_produk)->first();
        $monthTwo = ProductCompareTableTwo::where('kode_produk', $kode_produk)->first();

        // Jika Produk Tidak ada pada bulan pertama / bulan kedua, ambil satu aja bulan yang ada 
        if( !$monthOne) {
            $info = $monthTwo;
        }else if( !$monthTwo) {
            $info = $monthOne;
        }else {
            $info = $monthOne;
        }

        


        return view('performa_produk.compare.show');
    }
}
