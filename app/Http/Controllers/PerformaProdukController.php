<?php

namespace App\Http\Controllers;

use App\Models\ProdukPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PerformaProdukController extends Controller
{
    public function index()
    {
        // Logic to fetch and display product performance data
        return view('performa_produk.index');
    }

    public function import(Request $request)
    {
        // 1. Validasi file yang diunggah
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
                    ProdukPerformance::create($data);
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

    public function getcountdata()
    {
        $count = ProdukPerformance::count();
        $totalPenjualan = DB::select("SELECT SUM(penjualan_pesanan_siap_dikirim_idr) as omset FROM produk_performances WHERE kode_variasi IS NULL");
        $totalProdukSiapDikirim = DB::select("SELECT SUM(produk_pesanan_siap_dikirim) as produkPesananSiapDikirim FROM produk_performances WHERE kode_variasi IS NULL");
        $limaProdukLaris = DB::select("SELECT kode_produk, TRIM(
                SUBSTRING_INDEX(produk, ' ', 5)
            ) AS nama_produk, SUM(penjualan_pesanan_siap_dikirim_idr) AS total_penjualan_ FROM produk_performances WHERE kode_variasi IS NULL GROUP BY kode_produk ORDER BY total_penjualan_ DESC
        LIMIT 5;");
        $limaProdukKurangLaris = DB::select("SELECT kode_produk, TRIM(
                SUBSTRING_INDEX(produk, ' ', 5)
            ) AS nama_produk, SUM(produk_pesanan_siap_dikirim) AS total_pesanan,SUM(penjualan_pesanan_siap_dikirim_idr) AS total_penjualan
            FROM produk_performances
            WHERE kode_variasi IS NULL
            GROUP BY kode_produk
            HAVING SUM(produk_pesanan_siap_dikirim) > 0
            ORDER BY penjualan_pesanan_siap_dikirim_idr ASC
            LIMIT 5;");
        return response()->json(['count' => $count, 'totalPenjualan' => $totalPenjualan[0]->omset, 'totalProdukSiapDikirim' => $totalProdukSiapDikirim[0]->produkPesananSiapDikirim, 'limaProdukLaris' => $limaProdukLaris, 'limaProdukKurangLaris' => $limaProdukKurangLaris], 200);
    }
    public function resetData()
    {
        ProdukPerformance::truncate();
        return response()->json(['message' => 'Data Produk Performance berhasil direset.'], 200);
    }

    public function lists()
    {
        return view('performa_produk.lists');
    }

    public function getListData(Request $request)
    {
        $query = ProdukPerformance::query();

        // Pencarian (search)
        $search = $request->input('search');
        if (!empty($search)) {
            $query->whereNull('kode_variasi')
                ->where(function ($q) use ($search) {
                    $q->where('kode_produk', 'like', "%{$search}%")
                        ->orWhere('produk', 'like', "%{$search}%")
                        ->orWhere('status_produk_saat_ini', 'like', "%{$search}%")
                        ->orWhere('kode_variasi', 'like', "%{$search}%");
                });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'kode_produk');
        $sortDir = $request->input('order.0.dir', 'asc');
        $allowedSorts = [
            'kode_produk',
            'produk',
            'status_produk_saat_ini',
            'kode_variasi',
            'nama_variasi',
            'status_variasi_saat_ini',
            'kode_variasi_2',
            'sku_induk',
            'pengunjung_produk_kunjungan',
            'halaman_produk_dilihat',
            'pengunjung_melihat_tanpa_membeli',
            'tingkat_pengunjung_melihat_tanpa_membeli',
            'klik_pencarian',
            'suka',
            'pengunjung_produk_menambahkan_ke_keranjang',
            'dimasukkan_ke_keranjang_produk',
            'tingkat_konversi_produk_dimasukkan_ke_keranjang',
            'total_pembeli_pesanan_dibuat',
            'produk_pesanan_dibuat',
            'total_penjualan_pesanan_dibuat_idr',
            'tingkat_konversi_pesanan_dibuat',
            'total_pembeli_pesanan_siap_dikirim',
            'produk_pesanan_siap_dikirim',
            'penjualan_pesanan_siap_dikirim_idr',
            'tingkat_konversi_pesanan_siap_dikirim',
            'tingkat_konversi_pesanan_siap_dikirim_dibagi_pesanan_dibuat',
            'persen_pembelian_ulang_pesanan_siap_dikirim',
            'rata_rata_hari_pembelian_terulang_pesanan_siap_dikirim'
        ];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'kode_produk';
        }
        $query->orderBy($sortBy, $sortDir);

        // Pagination (DataTables)
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $total = $query->count();
        $data = $query->skip($start)->take($length)->get();
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function getPerformaProduk()
    {
        $grandTotalPenjualan = DB::select("SELECT SUM(penjualan_pesanan_siap_dikirim_idr) AS total_penjualan
        FROM produk_performances WHERE kode_variasi IS NULL");

        $totalPenjualanProduk = DB::select("SELECT kode_produk, produk AS nama_produk, SUM(produk_pesanan_siap_dikirim) AS total_pesanan,SUM(penjualan_pesanan_siap_dikirim_idr) AS total_penjualan
        FROM produk_performances
        WHERE kode_variasi IS NULL
        GROUP BY kode_produk");

        $data = [];
        foreach ($totalPenjualanProduk as $produk) {
            $data[] = [
                'kode_produk' => $produk->kode_produk,
                'nama_produk' => $produk->nama_produk,
                'total_pesanan' => $produk->total_pesanan,
                'total_penjualan' => $produk->total_penjualan,
                'persentase_penjualan' => $grandTotalPenjualan[0]->total_penjualan > 0 ? round(($produk->total_penjualan / $grandTotalPenjualan[0]->total_penjualan) * 100, 3) : 0,
            ];
        }

        // Urutkan data berdasarkan total_penjualan dari terbesar ke terkecil
        usort($data, function ($a, $b) {
            return $b['total_penjualan'] <=> $a['total_penjualan'];
        });

        return response()->json([
            'grand_total_penjualan' => $grandTotalPenjualan[0]->total_penjualan,
            'produk_performances' => $data,
        ], 200);
    }
}
