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
        SELECT a.id,a.product_code AS kode_produk, IFNULL(IFNULL(CONCAT(b.produk,'-',b.nama_variasi),b.produk),'-') AS nama_produk, 
        CASE WHEN b.produk IS NULL THEN 'tidak_ada'
        ELSE 'ada' END AS status
        FROM product_codes AS a
        LEFT JOIN product_compare_table_ones AS b ON a.product_code = b.kode_variasi_2 OR b.sku_induk = a.product_code
        WHERE a.kategori_id = $id
        GROUP BY a.product_code
        ");

        $produkData = DB::select("SELECT sku, produk, SUM(IFNULL(total_pesanan_siap_dikirim_1,0)) AS pesanan_siap_dikirim_1, SUM(IFNULL(total_pesanan_siap_dikirim_2,0)) AS pesanan_siap_dikirim_2, SUM(penjualan_1) AS penjualan_periode_1, SUM(penjualan_2) AS penjualan_periode_2 FROM (
            SELECT pc.product_code AS sku, joinan.total_pesanan_siap_dikirim_1, joinan.total_pesanan_siap_dikirim_2, joinan.produk, joinan.penjualan_1, joinan.penjualan_2 
            FROM product_codes AS pc 
            JOIN (
                    SELECT aa.kode_variasi_2, aa.produk, aa.total_pesanan_siap_dikirim_1, bb.total_pesanan_siap_dikirim_2, aa.penjualan_1, bb.penjualan_2 FROM (
                        SELECT a.kode_variasi_2,a.produk, SUM(a.produk_pesanan_siap_dikirim) AS total_pesanan_siap_dikirim_1, SUM(a.penjualan_pesanan_siap_dikirim_idr) AS penjualan_1
                        FROM product_compare_table_ones AS a 
                        GROUP BY a.kode_variasi_2
                ) AS aa
                JOIN (
                    SELECT a.kode_variasi_2, a.produk, SUM(a.produk_pesanan_siap_dikirim) AS total_pesanan_siap_dikirim_2, SUM(a.penjualan_pesanan_siap_dikirim_idr) AS penjualan_2
                    FROM product_compare_table_twos AS a 
                    GROUP BY a.kode_variasi_2
                ) AS bb ON aa.kode_variasi_2 = bb.kode_variasi_2
            ) AS joinan ON pc.product_code = joinan.kode_variasi_2

            UNION ALL

            SELECT pc.product_code AS sku, joinan.total_pesanan_siap_dikirim_1, joinan.total_pesanan_siap_dikirim_2, joinan.produk, joinan.penjualan_1, joinan.penjualan_2 
            FROM product_codes AS pc 
            JOIN (
            SELECT aa.sku_induk,aa.produk, aa.total_pesanan_siap_dikirim_1, bb.total_pesanan_siap_dikirim_2, aa.penjualan_1, bb.penjualan_2 FROM (
            SELECT a.sku_induk, a.produk, SUM(a.produk_pesanan_siap_dikirim) AS total_pesanan_siap_dikirim_1,SUM(a.penjualan_pesanan_siap_dikirim_idr) AS penjualan_1
                    FROM product_compare_table_ones AS a 
                    WHERE a.kode_variasi IS NULL
                    GROUP BY a.sku_induk
                    
                    ) AS aa
                    JOIN (
                    SELECT a.sku_induk, a.produk, SUM(a.produk_pesanan_siap_dikirim) AS total_pesanan_siap_dikirim_2, SUM(a.penjualan_pesanan_siap_dikirim_idr) AS penjualan_2
                    FROM product_compare_table_twos AS a 
                    WHERE a.kode_variasi IS NULL
                    GROUP BY a.sku_induk )
                    AS bb ON aa.sku_induk = bb.sku_induk
                    ) AS joinan ON pc.product_code = joinan.sku_induk
            ) AS aax
            GROUP BY sku ORDER BY penjualan_periode_2 DESC");
        foreach ($produkData as &$item) {
            $total_pesanan = $item->pesanan_siap_dikirim_1 + $item->pesanan_siap_dikirim_2;

            // Tambahkan persentase perbedaan pesanan_siap_dikirim_1 dan pesanan_siap_dikirim_2
            if($item->pesanan_siap_dikirim_1 != 0) {
                $item->persentase_perubahan_pesanan = number_format(($item->pesanan_siap_dikirim_2 - $item->pesanan_siap_dikirim_1) * 100.0 / $item->pesanan_siap_dikirim_1, 2);
            } else {
                $item->persentase_perubahan_pesanan = number_format(0, 2);
            }
            
            // Tambahkan persentase perbedaan penjualan_peridoe_1 dan penjualan_periode_2
            if ($item->penjualan_periode_1 != 0) {
                $item->persentase_perubahan_penjualan = number_format(($item->penjualan_periode_2 - $item->penjualan_periode_1) * 100.0 / $item->penjualan_periode_1, 2);
            } else {
                $item->persentase_perubahan_penjualan = number_format(0, 2);
            }

            // Tambahkan AOV

            if ($total_pesanan != 0) {
                $item->aov = number_format(($item->penjualan_periode_1 + $item->penjualan_periode_2) / $total_pesanan, 2);
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
