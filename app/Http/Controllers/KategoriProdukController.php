<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\ProductCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('performa_produk.kategori');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function lists()
    {
        $kategori = KategoriProduk::query()->where('status', 'aktif')->get();
        return response()->json(['categories' => $kategori], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori,
            'status' => 'aktif'
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.'], 200);
    }

    public function storeProduct(Request $request)
    {
        ProductCode::create([
            'kategori_id' => $request->kategori_id,
            'product_code' => $request->product_code,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
    }

    public function getProductCodes($id)
    {
        $product = DB::select("
        SELECT a.id,a.product_code AS kode_produk, IFNULL(b.produk,'-') AS nama_produk, 
        CASE WHEN b.produk IS NULL THEN 'tidak_ada'
        ELSE 'ada' END AS status
        FROM product_codes AS a
        LEFT JOIN product_compare_table_ones AS b ON a.product_code = b.kode_produk AND b.kode_variasi IS NULL
        WHERE a.kategori_id = $id
        ");

        $produkData = DB::select("SELECT a.id,a.product_code AS kode_produk, b.*
        FROM product_codes AS a 
		  JOIN ( SELECT *,
        CASE
                WHEN pengunjung_produk_kunjungan_1 = 0 OR pengunjung_produk_kunjungan_1 IS NULL THEN 0
                ELSE ((IFNULL(pengunjung_produk_kunjungan_2,0) - pengunjung_produk_kunjungan_1) * 100.0 / pengunjung_produk_kunjungan_1)
            END AS persentase_perubahan_pengunjung_produk_kunjungan,
 				CASE
                WHEN pengunjung_produk_menambahkan_ke_keranjang_1 = 0 OR pengunjung_produk_menambahkan_ke_keranjang_1 IS NULL THEN 0
                ELSE ((IFNULL(pengunjung_produk_menambahkan_ke_keranjang_2,0) - pengunjung_produk_menambahkan_ke_keranjang_1) * 100.0 / pengunjung_produk_menambahkan_ke_keranjang_1) 
            END AS persentase_perubahan_pengunjung_produk_menambahkan_ke_keranjang,
            CASE 
                WHEN total_pesanan_1 = 0 OR total_pesanan_1 IS NULL THEN 0
                ELSE ((IFNULL(total_pesanan_2,0)- total_pesanan_1) * 100.0 / total_pesanan_1)
            END AS persentase_perubahan_total_pesanan,
              CASE 
                WHEN total_penjualan_dibuat_1 = 0 OR total_penjualan_dibuat_1 IS NULL THEN 0
                ELSE ((IFNULL(total_penjualan_dibuat_2,0) - total_penjualan_dibuat_1) * 100.0 / total_penjualan_dibuat_1)
            END AS persentase_perubahan_penjualan_dibuat,
              CASE 
                WHEN total_penjualan_1 = 0 OR total_penjualan_1 IS NULL THEN 0
                ELSE ((IFNULL(total_penjualan_2,0) - total_penjualan_1) * 100.0 / total_penjualan_1)
            END AS persentase_perubahan_penjualan
        FROM (
				SELECT
                a.kode_produk,
                a.produk AS nama_produk,
               IFNULL(a.pengunjung_produk_kunjungan,0) AS pengunjung_produk_kunjungan_1,
					IFNULL(b.pengunjung_produk_kunjungan,0) AS pengunjung_produk_kunjungan_2,
					IFNULL(a.pengunjung_produk_menambahkan_ke_keranjang,0) AS pengunjung_produk_menambahkan_ke_keranjang_1,
                IFNULL(b.pengunjung_produk_menambahkan_ke_keranjang,0) AS pengunjung_produk_menambahkan_ke_keranjang_2,                
                IFNULL(a.produk_pesanan_siap_dikirim,0) AS total_pesanan_1,
                IFNULL(b.produk_pesanan_siap_dikirim,0) AS total_pesanan_2,
                 IFNULL(a.total_penjualan_pesanan_dibuat_idr,0) AS total_penjualan_dibuat_1,
                 IFNULL(b.total_penjualan_pesanan_dibuat_idr,0) AS total_penjualan_dibuat_2,
                IFNULL(a.penjualan_pesanan_siap_dikirim_idr,0) AS total_penjualan_1,
                IFNULL(b.penjualan_pesanan_siap_dikirim_idr,0) AS total_penjualan_2
            FROM product_compare_table_ones AS a
            LEFT JOIN product_compare_table_twos AS b ON a.kode_produk = b.kode_produk AND b.kode_variasi is null 
            WHERE a.kode_variasi IS NULL 
            GROUP BY a.kode_produk, a.produk       			
				) AS ax) AS b ON a.product_code = b.kode_produk
                 WHERE a.kategori_id = $id
        		ORDER BY b.total_penjualan_2 DESC LIMIT 10");
        foreach ($produkData as &$item) {
            $total_pesanan = $item->total_pesanan_1 + $item->total_pesanan_2;
            $item->jumlah_pesanan_rata_rata_per_hari = number_format($total_pesanan / 62, 2); // Asumsi 62 hari

            $item->selisih_pesanan_dibuat_ke_siap_dikirim = ($item->total_penjualan_dibuat_1 + $item->total_penjualan_dibuat_2) - ($item->total_penjualan_1 + $item->total_penjualan_2);

            if ($total_pesanan != 0) {
                $item->aov = number_format(($item->total_penjualan_1 + $item->total_penjualan_2) / $total_pesanan, 2);
            } else {
                $item->aov = number_format(0, 2);
            }
        }
        unset($item);



        return response()->json(['data' => $product, 'produkData' => $produkData], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kategori = KategoriProduk::where('id', $id)->first();
        return view('performa_produk.showCategory', ['kategori' => $kategori]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriProduk $kategoriProduk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriProduk $kategoriProduk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        ProductCode::where('kategori_id', $id)->delete();
        KategoriProduk::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }

    public function deleteProductCode($id)
    {
        ProductCode::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Product code berhasil dihapus.']);
    }
}
