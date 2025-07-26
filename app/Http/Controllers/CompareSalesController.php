<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\Shop;
use App\Models\MultiComparativeFSales;
use App\Models\MultiComparativeFSalesFour;
use App\Models\MultiComparativeFSalesThree;
use App\Models\MultiComparativeFSalesTwo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompareSalesController extends Controller
{
    public function index()
    {
        $shop = Shop::all();
        // Logic to fetch and display product performance data
        return view('comparesales.index', compact('shop'));
    }

    public function import(Request $request)
    {

        // 1. Validasi input
        $request->validate([
            'platform' => 'required|in:Shopee,Tiktok',
            'file' => 'required|mimes:csv,txt',
            'periode_ke' => 'required|in:1,2,3,4',
            'shop_id' => 'required',
        ]);

        $platform = $request->input('platform');
        $shop_id = $request->input('shop_id');
        $path = $request->file('file')->getRealPath();
        $delimiter = ';';
        $rowsToInsert = [];

        // 2. Buka dan baca CSV
        if (($handle = fopen($path, 'r')) !== false) {
            $rowNumber = 0;
            $headerRow = fgetcsv($handle, 0, $delimiter);
            $colCount = count($headerRow);

            if ($platform === 'Shopee' && $colCount > 49) {
                fclose($handle);
                return back()->with('error', 'File CSV tidak sesuai format Shopee, mungkin anda upload file tiktok.');
            }
            if ($platform === 'Tiktok' && $colCount < 61) {
                fclose($handle);
                return back()->with('error', 'File CSV tidak sesuai format Tiktok,mungkin anda upload file shopee.');
            }

            // Reset pointer agar header terbaca lagi di loop
            rewind($handle);

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $rowNumber++;

                if ($platform === 'Shopee') {
                    // Mulai dari baris ke-2
                    if ($rowNumber < 2) {
                        continue;
                    }

                    if ($row[1] == 'Batal' || $row[1] == 'Belum Bayar') {
                        continue;
                    }

                    // Kolom J (9), N (13), O (14), R (17)
                    $rawName = $row[13] ?? '';

                    // 1) Convert dari CP1252 ke UTF-8:
                    $utf8Name = mb_convert_encoding($rawName, 'UTF-8', 'Windows-1252');

                    // 2) (Opsional) Hilangkan karakter non-printable:
                    $cleanName = preg_replace('/[^\P{C}\n]+/u', '', $utf8Name);


                    $v1 = $row[9] ?? null;
                    $v2 = $cleanName ?? null;
                    $v3 = $row[14] ?? null;
                    $rawR = $row[17] ?? '';
                    $cleanR = str_replace('.', '', $rawR);
                    $v4 = is_numeric($cleanR)
                        ? (int) $cleanR
                        : 0;
                } else { // Tiktok
                    // Mulai dari baris ke-3
                    if ($rowNumber < 3) {
                        continue;
                    }

                    if ($row[1] == 'Dibatalkan' || $row[1] == 'Belum dibayar') {
                        continue;
                    }
                    // Kolom AB (27), H (7), G (6), P (15)
                    $rawName = $row[7] ?? '';

                    // 1) Convert dari CP1252 ke UTF-8:
                    $utf8Name = mb_convert_encoding($rawName, 'UTF-8', 'Windows-1252');

                    // 2) (Opsional) Hilangkan karakter non-printable:
                    $cleanName = preg_replace('/[^\P{C}\n]+/u', '', $utf8Name);

                    $rawDate = $row[27] ?? null;

                    // Jika ada isinya, parse d/m/Y H:i:s → Y-m-d
                    if ($rawDate) {
                        try {
                            $dateObj = Carbon::createFromFormat('d/m/Y H:i:s', $rawDate);
                            $v1 = $dateObj->format('Y-m-d');      // untuk tipe DATE
                            // $v1 = $dateObj->format('Y-m-d H:i:s'); // untuk DATETIME
                        } catch (\Exception $e) {
                            // kalau gagal parse, set null atau log error
                            $v1 = null;
                        }
                    } else {
                        $v1 = null;
                    }
                    $v2 = $cleanName ?? null;
                    $v3 = $row[6] ?? null;
                    $v4 = $row[15] ?? null;
                }

                // Tambahkan ke array
                $rowsToInsert[] = [
                    'tanggal' => $v1,
                    'nama_produk' => $v2,
                    'sku' => $v3,
                    'pendapatan' => $v4,
                    'platform' => $platform,
                    'shop_id' => $shop_id,
                ];
            }

            fclose($handle);
        }

        // 3. Bulk insert & hitung
        $count = 0;
        if (!empty($rowsToInsert)) {
            // Gunakan transaction untuk safety
            if ($request->periode_ke == 1) {
                DB::transaction(function () use ($rowsToInsert, &$count) {
                    MultiComparativeFSales::insert($rowsToInsert);
                    $count = count($rowsToInsert);
                });
            } elseif ($request->periode_ke == 2) {
                DB::transaction(function () use ($rowsToInsert, &$count) {
                    MultiComparativeFSalesTwo::insert($rowsToInsert);
                    $count = count($rowsToInsert);
                });
            } elseif ($request->periode_ke == 3) {
                DB::transaction(function () use ($rowsToInsert, &$count) {
                    MultiComparativeFSalesThree::insert($rowsToInsert);
                    $count = count($rowsToInsert);
                });
            } else {
                DB::transaction(function () use ($rowsToInsert, &$count) {
                    MultiComparativeFSalesFour::insert($rowsToInsert);
                    $count = count($rowsToInsert);
                });
            }
        }

        // 4. Redirect dengan info jumlah yang berhasil di-import
        return redirect()
            ->back()
            ->with('success', "Berhasil memasukkan data sebanyak {$count} baris dari platform {$platform}.");
    }

    public function reset()
    {
        DB::table('multi_comparative_f_sales')->truncate();
        DB::table('multi_comparative_f_sales_twos')->truncate();
        DB::table('multi_comparative_f_sales_threes')->truncate();
        DB::table('multi_comparative_f_sales_fours')->truncate();
        return response()->json(['message' => 'Database berhasil direset!', 'success' => true]);
    }

    public function chart(Request $request)
    {
        if ($request->periode == 'periode_1') {
            $data = DB::table('multi_comparative_f_sales')
                ->selectRaw('SUM(pendapatan) AS jumlah_penjualan, platform as labels')
                ->groupBy('platform')
                ->get();

            $chartData = [
                'labels' => $data->pluck('labels'),
                'datasets' => [
                    [
                        'label' => 'Jumlah Penjualan',
                        'data' => $data->pluck('jumlah_penjualan'),
                        'backgroundColor' => ['rgba(230, 84, 0, 0.8)', 'rgba(41, 41, 41, 0.8)'],
                        'borderColor' => ['rgba(230, 84, 0, 0.8)', 'rgba(41, 41, 41, 0.8)'],
                        'borderWidth' => 1
                    ]
                ]
            ];

            return response()->json($chartData);
        } else if ($request->periode == 'periode_2') {
            $data = DB::table('multi_comparative_f_sales_twos')
                ->selectRaw('SUM(pendapatan) AS jumlah_penjualan, platform as labels')
                ->groupBy('platform')
                ->get();

            $chartData = [
                'labels' => $data->pluck('labels'),
                'datasets' => [
                    [
                        'label' => 'Jumlah Penjualan',
                        'data' => $data->pluck('jumlah_penjualan'),
                        'backgroundColor' => ['rgba(230, 84, 0, 0.8)', 'rgba(41, 41, 41, 0.8)'],
                        'borderColor' => ['rgba(230, 84, 0, 0.8)', 'rgba(41, 41, 41, 0.8)'],
                        'borderWidth' => 1
                    ]
                ]
            ];

            return response()->json($chartData);
        } else if ($request->periode == 'periode_3') {
            $data = DB::table('multi_comparative_f_sales_threes')
                ->selectRaw('SUM(pendapatan) AS jumlah_penjualan, platform as labels')
                ->groupBy('platform')
                ->get();

            $chartData = [
                'labels' => $data->pluck('labels'),
                'datasets' => [
                    [
                        'label' => 'Jumlah Penjualan',
                        'data' => $data->pluck('jumlah_penjualan'),
                        'backgroundColor' => ['rgba(230, 84, 0, 0.8)', 'rgba(41, 41, 41, 0.8)'],
                        'borderColor' => ['rgba(230, 84, 0, 0.8)', 'rgba(41, 41, 41, 0.8)'],
                        'borderWidth' => 1
                    ]
                ]
            ];

            return response()->json($chartData);
        } else {

            $data = DB::table('multi_comparative_f_sales_fours')
                ->selectRaw('SUM(pendapatan) AS jumlah_penjualan, platform as labels')
                ->groupBy('platform')
                ->get();

            $chartData = [
                'labels' => $data->pluck('labels'),
                'datasets' => [
                    [
                        'label' => 'Jumlah Penjualan',
                        'data' => $data->pluck('jumlah_penjualan'),
                        'backgroundColor' => ['rgba(230, 84, 0, 0.8)', 'rgba(41, 41, 41, 0.8)'],
                        'borderColor' => ['rgba(230, 84, 0, 0.8)', 'rgba(41, 41, 41, 0.8)'],
                        'borderWidth' => 1
                    ]
                ]
            ];

            return response()->json($chartData);
        }
    }

    public function getTop10Sales(Request $request)
    {
        if ($request->periode == 'periode_1') {

            $rows = DB::table('multi_comparative_f_sales')
                ->select('sku', DB::raw('SUM(pendapatan) as total'))
                ->groupBy('sku')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        } else if ($request->periode == 'periode_2') {

            $rows = DB::table('multi_comparative_f_sales_twos')
                ->select('sku', DB::raw('SUM(pendapatan) as total'))
                ->groupBy('sku')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        } else if ($request->periode == 'periode_3') {
            $rows = DB::table('multi_comparative_f_sales_threes')
                ->select('sku', DB::raw('SUM(pendapatan) as total'))
                ->groupBy('sku')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        } else {
            $rows = DB::table('multi_comparative_f_sales_fours')
                ->select('sku', DB::raw('SUM(pendapatan) as total'))
                ->groupBy('sku')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        }

        return response()->json([
            'labels' => $rows->pluck('sku'),
            'data' => $rows->pluck('total'),
        ]);
    }

    public function kategori(Request $request)
    {


        if ($request->ajax()) {

            $toko = $request->input('toko', 'semua');

            // jika nantinya butuh filter per toko, bisa ditambahkan di JOIN product_codes
            $filterToko = $toko !== 'semua'
                ? "AND shop_id = $toko"
                : "";

            $kategori = DB::select("      SELECT id, nama_kategori, SUM(pendapatan_per_1) AS pendapatan_per_1, SUM(pendapatan_per_2) AS pendapatan_per_2, SUM(pendapatan_per_3) AS pendapatan_per_3, SUM(pendapatan_per_4) AS pendapatan_per_4,SUM(pendapatan_shopee_per_1) AS pendapatan_shopee_per_1,
		SUM(pendapatan_tiktok_per_1) AS pendapatan_tiktok_per_1,SUM(pendapatan_shopee_per_2) AS pendapatan_shopee_per_2,
		SUM(pendapatan_tiktok_per_2) AS pendapatan_tiktok_per_2,SUM(pendapatan_shopee_per_3) AS pendapatan_shopee_per_3,	SUM(pendapatan_tiktok_per_3) AS pendapatan_tiktok_per_3,
		SUM(pendapatan_shopee_per_4) AS pendapatan_shopee_per_4,	SUM(pendapatan_tiktok_per_4) AS pendapatan_tiktok_per_4
		  FROM (
			   SELECT *, ac.pendapatan_shopee_per_1 + pendapatan_tiktok_per_1 AS pendapatan_per_1, ac.pendapatan_shopee_per_2 + pendapatan_tiktok_per_2 AS pendapatan_per_2,
				ac.pendapatan_shopee_per_3 + pendapatan_tiktok_per_3 AS pendapatan_per_3,ac.pendapatan_shopee_per_4 + pendapatan_tiktok_per_4 AS pendapatan_per_4
FROM (
SELECT a.id, a.nama_kategori, b.product_code AS sku, 
				IFNULL(c.nama_produk, '-') as nama_produk, 
				IFNULL(c.pendapatan_shopee,0) AS pendapatan_shopee_per_1, 
				IFNULL(d.pendapatan_shopee,0) AS pendapatan_shopee_per_2, 
				IFNULL(g.pendapatan_shopee,0) AS pendapatan_shopee_per_3,
				IFNULL(i.pendapatan_shopee,0) AS pendapatan_shopee_per_4,
				IFNULL(e.pendapatan_tiktok,0) AS pendapatan_tiktok_per_1, 
				IFNULL(f.pendapatan_tiktok,0) AS pendapatan_tiktok_per_2, 
				IFNULL(h.pendapatan_tiktok,0) AS pendapatan_tiktok_per_3, 
				IFNULL(j.pendapatan_tiktok,0) AS pendapatan_tiktok_per_4
                     FROM kategori_produks AS a
                     JOIN product_codes AS b ON a.id = b.kategori_id 
                     LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_shopee
                                 FROM multi_comparative_f_sales
                                 WHERE platform = 'Shopee' $filterToko
                                 GROUP BY sku
                     ) AS c ON b.product_code = c.sku
                     LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_shopee
                              FROM multi_comparative_f_sales_twos
                              WHERE platform = 'Shopee' $filterToko
                              GROUP BY sku
                     ) AS d ON b.product_code = d.sku
                     LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_tiktok
                                 FROM multi_comparative_f_sales
                                 WHERE platform = 'Tiktok' $filterToko
                                 GROUP BY sku
                     ) AS e ON b.product_code = e.sku
                     LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_tiktok
                              FROM multi_comparative_f_sales_twos
                              WHERE platform = 'Tiktok' $filterToko
                              GROUP BY sku
                     ) AS f ON b.product_code = f.sku
                         LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_shopee
                              FROM multi_comparative_f_sales_threes
                              WHERE platform = 'Shopee' $filterToko
                              GROUP BY sku
                     ) AS g ON b.product_code = g.sku
                      LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_tiktok
                              FROM multi_comparative_f_sales_threes
                              WHERE platform = 'Tiktok' $filterToko
                              GROUP BY sku
                     ) AS h ON b.product_code = h.sku
                        LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_shopee
                              FROM multi_comparative_f_sales_fours
                              WHERE platform = 'Shopee' $filterToko
                              GROUP BY sku
                     ) AS i ON b.product_code = i.sku
                      LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_tiktok
                              FROM multi_comparative_f_sales_fours
                              WHERE platform = 'Tiktok' $filterToko
                              GROUP BY sku
                     ) AS j ON b.product_code = j.sku
                     
         ) AS ac
      ) AS ax GROUP BY id ORDER BY pendapatan_per_1 DESC");
            // Ekstrak labels & data
            $labels = array_column($kategori, 'nama_kategori');
            $data = array_map(function ($item) {
                return $item->pendapatan_per_1 + $item->pendapatan_per_2 + $item->pendapatan_per_3 + $item->pendapatan_per_4;
            }, $kategori);
            return response()->json([
                'kategoriData' => $kategori,
                'labels' => $labels,
                'data' => $data,
            ]);
        }

        $shops = Shop::all();
        return view('comparesales.kategori', compact('shops'));
    }



    public function show($id)
    {
        $shops = Shop::all();
        $kategori = KategoriProduk::find($id);




        return view("comparesales.show", compact(['kategori', 'shops']));
    }

    function getDetailKategori(Request $request, $id)
    {
        $toko = $request->input('shop_id', 'semua');

        // jika nantinya butuh filter per toko, bisa ditambahkan di JOIN product_codes
        $filterToko = $toko !== 'semua'
            ? "AND shop_id = $toko"
            : "";

        $kategori = DB::select("SELECT *, ac.pendapatan_shopee_per_1 + pendapatan_tiktok_per_1 AS pendapatan_per_1, ac.pendapatan_shopee_per_2 + pendapatan_tiktok_per_2 AS pendapatan_per_2,
				ac.pendapatan_shopee_per_3 + pendapatan_tiktok_per_3 AS pendapatan_per_3,ac.pendapatan_shopee_per_4 + pendapatan_tiktok_per_4 AS pendapatan_per_4
        FROM (
        SELECT a.id, a.nama_kategori, b.product_code AS sku, 
				IFNULL(c.nama_produk, '-') as nama_produk, 
				IFNULL(c.pendapatan_shopee,0) AS pendapatan_shopee_per_1, 
				IFNULL(d.pendapatan_shopee,0) AS pendapatan_shopee_per_2, 
				IFNULL(g.pendapatan_shopee,0) AS pendapatan_shopee_per_3,
				IFNULL(i.pendapatan_shopee,0) AS pendapatan_shopee_per_4,
				IFNULL(e.pendapatan_tiktok,0) AS pendapatan_tiktok_per_1, 
				IFNULL(f.pendapatan_tiktok,0) AS pendapatan_tiktok_per_2, 
				IFNULL(h.pendapatan_tiktok,0) AS pendapatan_tiktok_per_3, 
				IFNULL(j.pendapatan_tiktok,0) AS pendapatan_tiktok_per_4
                     FROM kategori_produks AS a
                     JOIN product_codes AS b ON a.id = b.kategori_id 
                     LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_shopee
                                 FROM multi_comparative_f_sales
                                 WHERE platform = 'Shopee' $filterToko
                                 GROUP BY sku
                     ) AS c ON b.product_code = c.sku
                     LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_shopee
                              FROM multi_comparative_f_sales_twos
                              WHERE platform = 'Shopee' $filterToko
                              GROUP BY sku
                     ) AS d ON b.product_code = d.sku
                     LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_tiktok
                                 FROM multi_comparative_f_sales
                                 WHERE platform = 'Tiktok' $filterToko
                                 GROUP BY sku
                     ) AS e ON b.product_code = e.sku
                     LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_tiktok
                              FROM multi_comparative_f_sales_twos
                              WHERE platform = 'Tiktok' $filterToko
                              GROUP BY sku
                     ) AS f ON b.product_code = f.sku
                         LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_shopee
                              FROM multi_comparative_f_sales_threes
                              WHERE platform = 'Shopee' $filterToko
                              GROUP BY sku
                     ) AS g ON b.product_code = g.sku
                      LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_tiktok
                              FROM multi_comparative_f_sales_threes
                              WHERE platform = 'Tiktok' $filterToko
                              GROUP BY sku
                     ) AS h ON b.product_code = h.sku
                        LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_shopee
                              FROM multi_comparative_f_sales_fours
                              WHERE platform = 'Shopee' $filterToko
                              GROUP BY sku
                     ) AS i ON b.product_code = i.sku
                      LEFT JOIN (
                              SELECT sku, nama_produk,  SUM(pendapatan) AS pendapatan_tiktok
                              FROM multi_comparative_f_sales_fours
                              WHERE platform = 'Tiktok' $filterToko
                              GROUP BY sku
                     ) AS j ON b.product_code = j.sku
                     
         ) AS ac
                     
        WHERE ac.id = ?
        ORDER BY pendapatan_per_1 desc
        ", [$id]);

        $filterToko2 = $toko !== 'semua'
            ? "AND c.shop_id = $toko"
            : "";


        $sql = "
            SELECT 
                tanggal, 
                SUM(total_pendapatan) as daily_revenue
            FROM (
                SELECT 
                    c.tanggal, 
                    SUM(c.pendapatan) AS total_pendapatan 
                FROM kategori_produks AS a
                JOIN product_codes AS b ON a.id = b.kategori_id
                JOIN multi_comparative_f_sales AS c ON b.product_code = c.sku $filterToko2 
                WHERE a.id = ? 
                GROUP BY c.tanggal

                UNION ALL 

                SELECT 
                    c.tanggal, 
                    SUM(c.pendapatan) AS total_pendapatan 
                FROM kategori_produks AS a
                JOIN product_codes AS b ON a.id = b.kategori_id
                JOIN multi_comparative_f_sales_twos AS c ON b.product_code = c.sku $filterToko2 
                WHERE a.id = ?
                GROUP BY c.tanggal

                UNION ALL 

                SELECT 
                    c.tanggal, 
                    SUM(c.pendapatan) AS total_pendapatan 
                FROM kategori_produks AS a
                JOIN product_codes AS b ON a.id = b.kategori_id
                JOIN multi_comparative_f_sales_threes AS c ON b.product_code = c.sku $filterToko2 
                WHERE a.id = ?
                GROUP BY c.tanggal

                UNION ALL 

                SELECT 
                    c.tanggal, 
                    SUM(c.pendapatan) AS total_pendapatan 
                FROM kategori_produks AS a
                JOIN product_codes AS b ON a.id = b.kategori_id
                JOIN multi_comparative_f_sales_fours AS c ON b.product_code = c.sku $filterToko2 
                WHERE a.id = ?
                GROUP BY c.tanggal
            ) as combined_sales
            GROUP BY tanggal
            ORDER BY tanggal ASC
        ";

        // Menjalankan raw query dengan binding untuk keamanan (mencegah SQL Injection)
        $results = DB::select($sql, [$id, $id, $id, $id]);

        // Memproses hasil query untuk dijadikan format yang sesuai untuk Chart.js
        $labels = [];
        $data = [];

        foreach ($results as $row) {
            // Mengubah format tanggal 'YYYY-MM-DD' menjadi format 'DD Mmm' (e.g., '24 Jul') untuk label chart
            $labels[] = Carbon::parse($row->tanggal)->format('d M');
            $data[] = $row->daily_revenue;
        }

        return response()->json(['kategori' => $kategori, 'labels' => $labels, 'data' => $data]);
    }
}
